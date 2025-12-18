<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ParticipantController extends Controller
{
    // ===============================
    // Display all participants
    // ===============================
    public function index()
    {
        $participants = User::where('role', 'peserta')->get();

        return view('admin.participants.index', compact('participants'));
    }

    // ===============================
    // Show create form
    // ===============================
    public function create()
    {
        return view('admin.participants.create');
    }

    // ===============================
    // Store new participant
    // ===============================
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        // Simpan ke tabel users
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'peserta',
        ]);

        // Simpan ke tabel participants
        Participant::create([
            'user_id'     => $user->id,
            'total_score' => 0,
            'exam_taken'  => null,
            'status'      => 'gagal',
        ]);

        return redirect()
            ->route('admin.participants.index')
            ->with('success', 'Peserta berhasil ditambahkan!');
    }

    // ===============================
    // Show edit form
    // ===============================
    public function edit($id)
    {
        $participant = User::where('role', 'peserta')
            ->where('id', $id)
            ->firstOrFail();

        return view('admin.participants.edit', compact('participant'));
    }

    // ===============================
    // Update participant
    // ===============================
    public function update(Request $request, $id)
    {
        $participant = User::where('role', 'peserta')
            ->where('id', $id)
            ->firstOrFail();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $participant->id,
        ]);

        $participant->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return redirect()
            ->route('admin.participants.index')
            ->with('success', 'Peserta berhasil diperbarui!');
    }

    // ===============================
    // Delete participant
    // ===============================
    public function destroy($id)
    {
        $participant = User::where('role', 'peserta')
            ->where('id', $id)
            ->firstOrFail();

        // Hapus relasi participant dulu (aman)
        Participant::where('user_id', $participant->id)->delete();

        // Hapus user
        $participant->delete();

        return redirect()
            ->route('admin.participants.index')
            ->with('success', 'Peserta berhasil dihapus!');
    }
}
