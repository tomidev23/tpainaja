@extends('admin.layouts.sidebar')

@section('content')
<div class="p-6 max-w-2xl mx-auto">
    <h2 class="text-xl font-semibold mb-4">Edit Soal</h2>

    <form action="{{ route('admin.questions.update', [$exam->id, $question->id]) }}" 
          method="POST" 
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Isi Soal --}}
        <div class="mb-3">
            <label class="font-semibold">Isi Soal</label>
            <textarea name="question_text" class="w-full border p-2 rounded" required>{{ $question->question_text }}</textarea>
        </div>

        {{-- Tampilkan file soal lama jika ada --}}
        @if ($question->question_file)
            <div class="mb-4">
                <label class="font-semibold block">File Soal Saat Ini:</label>

                @php
                    $ext = pathinfo($question->question_file, PATHINFO_EXTENSION);
                @endphp

                {{-- Gambar --}}
                @if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                    <img src="{{ asset('storage/'.$question->question_file) }}" class="w-40 rounded border mb-2">
                
                {{-- PDF --}}
                @elseif ($ext === 'pdf')
                    <a href="{{ asset('storage/'.$question->question_file) }}" 
                       target="_blank" 
                       class="text-blue-600 underline">
                        Lihat File PDF
                    </a>

                {{-- Audio --}}
                @elseif (in_array(strtolower($ext), ['mp3', 'wav', 'ogg']))
                    <audio controls class="mt-1">
                        <source src="{{ asset('storage/'.$question->question_file) }}">
                    </audio>

                {{-- File Lain --}}
                @else
                    <a href="{{ asset('storage/'.$question->question_file) }}" 
                       target="_blank" 
                       class="text-blue-600 underline">
                        Download File
                    </a>
                @endif
            </div>
        @endif

        {{-- Upload file baru --}}
        <div class="mb-4">
            <label class="font-semibold">Upload File Baru (Opsional)</label>
            <input type="file" name="question_file" class="w-full border p-2 rounded">
            <p class="text-xs text-gray-500">Bisa upload gambar, PDF, audio, dll</p>
        </div>

        {{-- Opsi Jawaban --}}
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label>Opsi A</label>
                <input type="text" name="option_a" value="{{ $question->option_a }}" class="w-full border p-2 rounded">
            </div>

            <div>
                <label>Opsi B</label>
                <input type="text" name="option_b" value="{{ $question->option_b }}" class="w-full border p-2 rounded">
            </div>

            <div>
                <label>Opsi C</label>
                <input type="text" name="option_c" value="{{ $question->option_c }}" class="w-full border p-2 rounded">
            </div>

            <div>
                <label>Opsi D</label>
                <input type="text" name="option_d" value="{{ $question->option_d }}" class="w-full border p-2 rounded">
            </div>
        </div>

        {{-- Jawaban Benar --}}
        <div class="mt-3">
            <label class="font-semibold">Jawaban Benar</label>
            <select name="jawaban_benar" class="border p-2 rounded">
                <option value="A" {{ $question->jawaban_benar == 'A' ? 'selected' : '' }}>A</option>
                <option value="B" {{ $question->jawaban_benar == 'B' ? 'selected' : '' }}>B</option>
                <option value="C" {{ $question->jawaban_benar == 'C' ? 'selected' : '' }}>C</option>
                <option value="D" {{ $question->jawaban_benar == 'D' ? 'selected' : '' }}>D</option>
            </select>
        </div>

        <button class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">Update</button>

        <a href="{{ route('admin.questions.index', $exam->id) }}" 
           class="ml-4 text-gray-600 hover:text-gray-800">
            Batal
        </a>
    </form>
</div>
@endsection
