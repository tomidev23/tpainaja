@extends('admin.layouts.sidebar')

@section('content')
<div class="p-6 max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-800">Edit Soal</h2>
            <p class="text-sm text-gray-500">Ujian: {{ $exam->nama_ujian ?? $exam->name }}</p>
        </div>

        <form action="{{ route('admin.questions.update', [$exam->id, $question->id]) }}" 
              method="POST" 
              enctype="multipart/form-data"
              class="p-6">
            @csrf
            @method('PUT')

            {{-- Tampilkan Error Validasi Global --}}
            @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Isi Soal --}}
            <div class="mb-6">
                <label class="block font-semibold text-gray-700 mb-2">Isi Soal</label>
                <textarea name="question_text" rows="4" 
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 transition" 
                          required>{{ old('question_text', $question->question_text) }}</textarea>
                @error('question_text')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- File Soal (Gambar/PDF/Audio) --}}
            <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block font-semibold text-gray-700 mb-2">File Soal (Gambar/PDF/Audio)</label>
                
                {{-- Preview File Lama --}}
                @if ($question->question_file)
                    <div class="mb-3">
                        <p class="text-xs text-gray-500 mb-1">File saat ini:</p>
                        @php
                            $ext = pathinfo($question->question_file, PATHINFO_EXTENSION);
                        @endphp

                        @if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                            <img src="{{ Storage::url($question->question_file) }}"  class="h-32 rounded border bg-white object-cover">
                        @elseif ($ext === 'pdf')
                            <a href="{{ Storage::url($question->question_file) }}" target="_blank" class="flex items-center text-blue-600 hover:underline">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                Lihat PDF
                            </a>
                        @elseif (in_array(strtolower($ext), ['mp3', 'wav', 'ogg']))
                            <audio controls class="w-full mt-1">
                                <source src="{{ asset('storage/'.$question->question_file) }}">
                            </audio>
                        @else
                            <a href="{{ asset('storage/'.$question->question_file) }}" target="_blank" class="text-blue-600 underline">Download File</a>
                        @endif
                    </div>
                @endif

                {{-- Input Upload Baru --}}
                <input type="file" name="question_file" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100">
                <p class="text-xs text-gray-400 mt-1">Upload untuk mengganti file lama. Biarkan kosong jika tidak ingin mengubah.</p>
                @error('question_file')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Logika Tampilan Berdasarkan Jenis Soal --}}
            @if($question->jenis_soal == 'pilihan_ganda' || !$question->jenis_soal)
                {{-- Opsi Jawaban (Hanya untuk Pilihan Ganda) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    @foreach(['a', 'b', 'c', 'd'] as $opt)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Opsi {{ strtoupper($opt) }}</label>
                        <input type="text" name="option_{{ $opt }}" 
                               value="{{ old('option_'.$opt, $question->{'option_'.$opt}) }}" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 transition">
                    </div>
                    @endforeach
                </div>

                {{-- Kunci Jawaban Pilihan Ganda --}}
                <div class="mb-6">
                    <label class="block font-semibold text-gray-700 mb-2">Kunci Jawaban</label>
                    <select name="jawaban_benar" class="w-full md:w-1/3 border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 transition">
                        @foreach(['A', 'B', 'C', 'D'] as $key)
                            <option value="{{ $key }}" {{ old('jawaban_benar', $question->jawaban_benar) == $key ? 'selected' : '' }}>
                                {{ $key }}
                            </option>
                        @endforeach
                    </select>
                </div>

            @elseif($question->jenis_soal == 'benar_salah')
                {{-- Kunci Jawaban Benar/Salah --}}
                <div class="mb-6">
                    <label class="block font-semibold text-gray-700 mb-2">Kunci Jawaban</label>
                    <select name="jawaban_benar" class="w-full md:w-1/3 border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 transition">
                        <option value="true" {{ old('jawaban_benar', $question->jawaban_benar) == 'true' ? 'selected' : '' }}>Benar (True)</option>
                        <option value="false" {{ old('jawaban_benar', $question->jawaban_benar) == 'false' ? 'selected' : '' }}>Salah (False)</option>
                    </select>
                </div>

            @elseif($question->jenis_soal == 'esai')
                {{-- Kunci Jawaban Esai --}}
                <div class="mb-6">
                    <label class="block font-semibold text-gray-700 mb-2">Kunci Jawaban / Kata Kunci</label>
                    <textarea name="jawaban_benar" rows="3" 
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 transition">{{ old('jawaban_benar', $question->jawaban_benar) }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Masukkan jawaban yang diharapkan.</p>
                </div>
            @endif

            {{-- Tombol Aksi --}}
            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.questions.index', $exam->id) }}" 
                   class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-5 py-2.5 bg-yellow-500 text-white font-semibold rounded-lg shadow-md hover:bg-yellow-600 focus:ring-4 focus:ring-yellow-300 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
