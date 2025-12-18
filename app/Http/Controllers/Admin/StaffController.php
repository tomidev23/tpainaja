<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    // ===============================
    // List Staff
    // ===============================
    public function index()
    {
        $staff = User::where('role', 'staff')->get();

        return view('admin.staff.index', compact('staff'));
    }

    // ===============================
    // Show create form
    // ===============================
    public function create()
    {
        return view('admin.staff.create');
    }

    // ===============================
    // Store new staff
    // ===============================
    public function store(Request $request)
    {
        $request->validate(
            [
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|min:6',
            ],
            [
                'email.unique' => 'Email sudah digunakan, silakan gunakan email lain.',
            ]
        );

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'staff',
        ]);

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Staff baru berhasil ditambahkan.');
    }

    // ===============================
    // Show edit form
    // ===============================
    public function edit($id)
    {
        $staff = User::where('role', 'staff')
            ->where('id', $id)
            ->firstOrFail();

        return view('admin.staff.edit', compact('staff'));
    }

    // ===============================
    // Update staff
    // ===============================
    public function update(Request $request, $id)
    {
        $staff = User::where('role', 'staff')
            ->where('id', $id)
            ->firstOrFail();

        $request->validate(
            [
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email,' . $staff->id,
                'password' => 'nullable|min:6',
            ],
            [
                'email.unique' => 'Email sudah digunakan oleh akun lain.',
            ]
        );

        $staff->name  = $request->name;
        $staff->email = $request->email;

        if ($request->filled('password')) {
            $staff->password = Hash::make($request->password);
        }

        $staff->save();

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Data staff berhasil diperbarui.');
    }

    // ===============================
    // Delete staff
    // ===============================
    public function destroy($id)
    {
        $staff = User::where('role', 'staff')
            ->where('id', $id)
            ->firstOrFail();

        $staff->delete();

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Staff berhasil dihapus.');
    }
}
