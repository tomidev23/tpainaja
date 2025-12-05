@extends('admin.layouts.sidebar')

@section('title', 'Tambah Staff')

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- HEADER KUNING --}}
    <div class="bg-[#F7C948] text-gray-900 font-semibold px-6 py-3 rounded-t-lg w-full">
        Tambah Staff
    </div>

    {{-- CARD FORM --}}
    <div class="bg-white shadow-md rounded-b-lg px-8 py-10 border">

        <form action="{{ route('admin.staff.store') }}" method="POST">
            @csrf

            {{-- NAMA --}}
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-1">Nama</label>
                <input type="text" name="name"
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                       placeholder="Masukkan nama staff" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- EMAIL --}}
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-1">Email</label>
                <input type="email" name="email"
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                       placeholder="Masukkan email staff" required>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- PASSWORD --}}
            <div class="mb-8">
                <label class="block text-gray-700 font-semibold mb-1">Password</label>
                <input type="password" name="password"
                       class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                       placeholder="Minimal 6 karakter" required>
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- BUTTON --}}
            <div class="flex justify-end gap-4">

                {{-- Tombol Batal --}}
                <a href="{{ route('admin.staff.index') }}"
                   class="px-6 py-2 bg-red-500 text-white font-semibold rounded-md shadow hover:bg-red-600 transition">
                    Batal
                </a>

                {{-- Tombol Simpan (pakai SweetAlert dari sidebar karena ada class btn-save) --}}
                <button type="submit"
                        class="px-6 py-2 bg-blue-500 text-white font-semibold rounded-md shadow hover:bg-blue-600 transition btn-save">
                    Simpan
                </button>

            </div>

        </form>

    </div>
</div>

@endsection
