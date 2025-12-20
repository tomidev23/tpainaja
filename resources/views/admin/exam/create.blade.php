@extends('admin.layouts.sidebar')

@section('title', 'Buat Ujian')

@section('content')
<div class="min-h-screen bg-[#F8FAFC] px-10 py-10">

    <div class="max-w-4xl mx-auto bg-white rounded-xl border border-[#E5E7EB] shadow-sm px-10 py-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Buat Ujian</h2>

        <form action="{{ route('admin.exam.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Judul Ujian --}}
            <div>
                <label class="block font-medium text-gray-700 mb-1">Judul Ujian</label>
                <input
                    type="text"
                    name="nama_ujian"
                    value="{{ old('nama_ujian') }}"
                    required
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:ring-2 focus:ring-yellow-400 outline-none"
                >
                @error('nama_ujian')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Jumlah Soal --}}
            <div>
                <label class="block font-medium text-gray-700 mb-1">Jumlah Soal</label>
                <input
                    type="number"
                    name="question_count"
                    min="1"
                    value="{{ old('question_count') }}"
                    required
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:ring-2 focus:ring-yellow-400 outline-none"
                >
                @error('question_count')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Bobot Nilai --}}
            <div>
                <label class="block font-medium text-gray-700 mb-1">Bobot Nilai</label>
                <input
                    type="number"
                    step="0.01"
                    name="weight"
                    value="{{ old('weight') }}"
                    required
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:ring-2 focus:ring-yellow-400 outline-none"
                >
                @error('weight')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Waktu Ujian --}}
            <div>
                <label class="block font-medium text-gray-700 mb-1">Waktu Ujian (menit)</label>
                <input
                    type="number"
                    name="duration"
                    min="1"
                    value="{{ old('duration') }}"
                    required
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:ring-2 focus:ring-yellow-400 outline-none"
                >
                @error('duration')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tipe Ujian --}}
            <div>
                <label class="block font-medium text-gray-700 mb-1">Tipe Ujian</label>
                <select
                    name="exam_type"
                    required
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:ring-2 focus:ring-yellow-400 outline-none"
                >
                    <option value="tpa" {{ old('exam_type') == 'tpa' ? 'selected' : '' }}>TPA</option>
                    <option value="cbt" {{ old('exam_type') == 'cbt' ? 'selected' : '' }}>CBT</option>
                </select>
                @error('exam_type')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal Ujian --}}
            <div>
                <label class="block font-medium text-gray-700 mb-1">Tanggal Ujian</label>
                <input
                    type="date"
                    name="exam_date"
                    value="{{ old('exam_date') }}"
                    required
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:ring-2 focus:ring-yellow-400 outline-none"
                >
                @error('exam_date')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Logo Ujian --}}
            <div>
                <label class="block font-medium text-gray-700 mb-1">Masukkan Logo</label>
                <input
                    type="file"
                    name="logo"
                    accept=".jpg,.jpeg,.png"
                    class="block w-full text-sm text-gray-700"
                >
                @error('logo')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol --}}
            <div class="flex justify-end gap-4 pt-4">
                <a href="{{ route('admin.exam.index') }}"
                   class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-medium rounded-md shadow-sm">
                    Batal
                </a>
              <button type="button" class="btn-save px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-md shadow">
    Simpan
</button>

            </div>
        </form>
    </div>
</div>
@endsection
