<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ParticipantController extends Controller
{
    // Display all participants
    public function index()
    {
        // Ambil semua user dengan role peserta
        $participants = User::where('role', 'peserta')->get();
        return view('admin.participants.index', compact('participants'));
    }

    // Show the form for creating a new participant
    public function create()
    {
        return view('admin.participants.create');
    }

    // Store a new participant
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
    ]);

    // 1. Simpan user dulu
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'peserta',
    ]);

    // 2. Buat detail peserta
    Participant::create([
        'user_id' => $user->id,
        'total_score' => 0,
        'exam_taken' => null,
        'status' => 'gagal',
    ]);

    return redirect()->route('admin.participants.index')
        ->with('success', 'Peserta berhasil ditambahkan!');
}


    // Show the form for editing an existing participant
    public function edit($id)
    {
        $participant = User::where('role', 'peserta')->where('id', $id)->firstOrFail();
        return view('admin.participants.edit', compact('participant'));
    }

    // Update participant info
    public function update(Request $request, $id)
    {
        $participant = User::where('role', 'peserta')->where('id', $id)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,$id",
        ]);

        $participant->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('admin.participants.index')
                         ->with('success', 'Peserta berhasil diperbarui');
    }

    // Delete participant
    public function destroy($id)
    {
        $participant = User::where('role', 'peserta')->where('id', $id)->firstOrFail();

        // Hapus user akan otomatis menghapus participant (jika foreign key cascade)
        $participant->delete();

        return redirect()->route('admin.participants.index')
                        ->with('success', 'Peserta berhasil dihapus!');
    }
}
