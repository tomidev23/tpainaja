@extends('admin.layouts.sidebar')

@section('title', 'Edit Ujian')

@section('content')
<div class="min-h-screen bg-[#F8FAFC] px-10 py-10">

    <div class="max-w-4xl mx-auto bg-white rounded-[12px] border border-[#E5E7EB] shadow-sm px-10 py-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Edit Ujian</h2>

        <form action="{{ route('admin.exam.update', $exam->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Judul --}}
            <div>
                <label class="block font-medium text-gray-700 mb-1">Judul Ujian</label>
                <input type="text" name="nama_ujian"
                    value="{{ old('nama_ujian', $exam->nama_ujian) }}"
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:ring-2 focus:ring-yellow-400 outline-none" required>
            </div>

            {{-- Jumlah Soal --}}
            <div>
                <label class="block font-medium text-gray-700 mb-1">Jumlah Soal</label>
                <input type="number" name="question_count" min="1"
                    value="{{ old('question_count', $exam->question_count) }}"
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:ring-2 focus:ring-yellow-400 outline-none" required>
            </div>

            {{-- Bobot --}}
            <div>
                <label class="block font-medium text-gray-700 mb-1">Bobot Nilai</label>
                <input type="number" step="0.01" name="weight"
                    value="{{ old('weight', $exam->weight) }}"
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:ring-2 focus:ring-yellow-400 outline-none" required>
            </div>

            {{-- Waktu --}}
            <div>
                <label class="block font-medium text-gray-700 mb-1">Waktu Ujian (menit)</label>
                <input type="number" name="duration" min="1"
                    value="{{ old('duration', $exam->duration) }}"
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:ring-2 focus:ring-yellow-400 outline-none" required>
            </div>

            {{-- 🔥 Tipe Ujian - sudah FIX --}}
            <div>
                <label class="block font-medium text-gray-700 mb-1">Tipe Ujian</label>
                <select name="exam_type" required
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:ring-2 focus:ring-yellow-400 outline-none">
                    
                    <option value="tpa" {{ old('exam_type', $exam->exam_type) == 'tpa' ? 'selected' : '' }}>TPA</option>
                    <option value="cbt" {{ old('exam_type', $exam->exam_type) == 'cbt' ? 'selected' : '' }}>CBT</option>
                </select>

                @error('exam_type')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal --}}
            <div>
                <label class="block font-medium text-gray-700 mb-1">Tanggal Ujian</label>
                <input type="date" name="exam_date"
                    value="{{ old('exam_date', $exam->exam_date) }}"
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:ring-2 focus:ring-yellow-400 outline-none" required>
            </div>

            {{-- Logo --}}
            <div>
                <label class="block font-medium text-gray-700 mb-1">Masukkan Logo</label>
                <input type="file" name="logo" accept="image/*" class="block">

                @if($exam->logo)
                    <img src="{{ asset('storage/'.$exam->logo) }}" class="h-14 mt-2">
                @endif
            </div>

            {{-- Tombol --}}
            <div class="flex justify-end gap-4 pt-4">
                <a href="{{ route('admin.exam.index') }}"
                   class="px-6 py-3 bg-red-500 text-white rounded-md hover:bg-red-600">Batal</a>

                <button type="submit"
                    class="px-6 py-3 bg-blue-500 text-white rounded-md hover:bg-blue-600">Simpan</button>
            </div>

        </form>
    </div>
</div>
@endsection
