<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PragmaRX\Google2FA\Google2FA;

class SecurityController extends Controller
{
    protected $google2fa;

    public function __construct(Google2FA $google2fa)
    {
        $this->google2fa = $google2fa;
    }

    // 🔹 GET /api/security/status
    public function status(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'email_verified' => !is_null($user->email_verified_at),
            'phone_verified' => !is_null($user->phone_verified_at),
            'two_factor_enabled' => $user->two_factor_enabled,
            'last_login' => $user->last_login_at?->toDateTimeString(), // tambahkan kolom last_login_at kalau perlu
        ]);
    }

    // 🔹 POST /api/change-password
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', function ($attribute, $value, $fail) use ($request) {
                if (!Hash::check($value, $request->user()->password)) {
                    $fail('Password lama tidak sesuai.');
                }
            }],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Password berhasil diubah.'
        ]);
    }

    // 🔹 PUT /api/profile
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->user()->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        // Jika email berubah, reset verifikasi
        if ($request->email !== $user->getOriginal('email')) {
            $user->email_verified_at = null;
            $user->save();

            // 🔔 Opsional: kirim email verifikasi ulang
            // $user->sendEmailVerificationNotification();
        }

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
            ],
        ]);
    }

    // 🔹 POST /api/security/2fa/enable
    public function enable2FA(Request $request)
    {
        $user = $request->user();

        if ($user->two_factor_enabled) {
            return response()->json(['message' => '2FA sudah aktif.'], 400);
        }

        // Generate secret
        $secret = $this->google2fa->generateSecretKey();
        $user->two_factor_secret = $secret;
        $user->save();

        // QR Code URL (untuk frontend tampilkan)
        $qrUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return response()->json([
            'message' => 'Silakan verifikasi 2FA untuk mengaktifkannya.',
            'secret' => $secret,
            'qr_code_url' => $qrUrl,
            'requires_verification' => true,
        ]);
    }

    // 🔹 POST /api/security/2fa/verify
    public function verify2FA(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();

        if (!$user->two_factor_secret) {
            return response()->json(['message' => '2FA belum diaktifkan.'], 400);
        }

        $valid = $this->google2fa->verifyKey($user->two_factor_secret, $request->code);

        if (!$valid) {
            return response()->json(['message' => 'Kode 2FA tidak valid.'], 422);
        }

        $user->two_factor_enabled = true;
        $user->save();

        return response()->json([
            'message' => '✅ 2FA berhasil diaktifkan.',
        ]);
    }

    // 🔹 POST /api/security/2fa/disable
    public function disable2FA(Request $request)
    {
        $user = $request->user();

        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->save();

        return response()->json([
            'message' => '2FA berhasil dinonaktifkan.',
        ]);
    }

    // 🔹 POST /api/security/logout-other-devices
    public function logoutOtherDevices(Request $request)
    {
        // Hapus semua token Sanctum **kecuali** yang sedang dipakai
        $request->user()->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();

        return response()->json([
            'message' => 'Berhasil logout dari perangkat lain.'
        ]);
    }
}