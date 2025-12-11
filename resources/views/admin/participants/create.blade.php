@extends('admin.layouts.sidebar')

@section('title', 'Tambah Peserta')

@section('content')
<div class="min-h-screen bg-[#F8FAFC] px-10 py-10">

    <div class="max-w-4xl mx-auto bg-white rounded-[12px] border border-[#E5E7EB] shadow-sm overflow-hidden">

        {{-- Header Kuning --}}
        <div class="bg-[#FACC15] px-8 py-3">
            <h2 class="text-lg font-semibold text-gray-800">
                Tambah Peserta
            </h2>
        </div>

        {{-- Form --}}
        <div class="px-10 py-8">

            <form action="{{ route('admin.participants.store') }}" method="POST">
                @csrf

                {{-- Nama --}}
                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">Nama</label>
                    <input 
                        type="text"
                        name="name"
                        placeholder="Masukkan nama peserta"
                        class="w-full border border-[#CFCFCF] rounded-lg px-4 py-3 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                        required
                    >
                </div>

                {{-- Email --}}
                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">Email</label>
                    <input 
                        type="email"
                        name="email"
                        placeholder="Masukkan email peserta"
                        class="w-full border border-[#CFCFCF] rounded-lg px-4 py-3 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                        required
                    >
                </div>

                {{-- Password --}}
                <div class="mb-8">
                    <label class="block text-gray-700 font-medium mb-2">Password</label>
                    <input 
                        type="password"
                        name="password"
                        placeholder="Masukkan password peserta"
                        class="w-full border border-[#CFCFCF] rounded-lg px-4 py-3 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                        required
                    >
                </div>

                {{-- Tombol --}}
                <div class="flex justify-end gap-4">
                    <a href="{{ route('admin.participants.index') }}"
                        class="bg-red-500 text-white font-medium px-6 py-3 rounded-md hover:bg-red-600 transition">
                        Batal
                    </a>

                    <button type="button" class="btn-save bg-blue-500 text-white px-5 py-2 rounded-md hover:bg-blue-600 transition">
    Simpan
</button>

                </div>

            </form>
        </div>
    </div>

</div>
@endsection
