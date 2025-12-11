@extends('admin.layouts.sidebar')

@section('content')
<div class="container mx-auto px-6 py-4">
    <h2 class="text-2xl font-semibold mb-4">Tambah Soal untuk: {{ $exam->nama_ujian }}</h2>

    <form action="{{ route('admin.questions.store', $exam->id) }}" 
        method="POST" 
        enctype="multipart/form-data" 
        class="bg-white p-6 rounded-lg shadow">
        
        @csrf

        {{-- Jenis Soal --}}
        <div class="mb-4">
            <label class="block font-medium">Jenis Soal</label>
            <select id="question_type" name="question_type" class="border rounded w-full p-2">
                <option value="multiple_choice">Pilihan Ganda</option>
                <option value="essay">Essay</option>
                <option value="true_false">Benar / Salah</option>
            </select>
        </div>

        {{-- Pertanyaan --}}
        <div class="mb-4">
            <label class="block font-medium">Pertanyaan (Text)</label>
            <textarea name="question_text" class="border rounded w-full p-2" rows="3"></textarea>
        </div>

        {{-- Upload File Soal --}}
        <div class="mb-4">
            <label class="block font-medium">Upload File Soal (Opsional)</label>
            <input type="file" name="question_file" class="border w-full p-2 rounded">
        </div>

        {{-- === PILIHAN GANDA === --}}
        <div id="multiple_choice_box">
            <label class="block font-medium mb-2">Opsi Jawaban</label>

            <div class="flex items-center mb-2">
                <input type="text" name="option_a" placeholder="Opsi A" class="border rounded w-full p-2">
                <input type="radio" name="correct_answer" value="A" class="ml-3"> A
            </div>

            <div class="flex items-center mb-2">
                <input type="text" name="option_b" placeholder="Opsi B" class="border rounded w-full p-2">
                <input type="radio" name="correct_answer" value="B" class="ml-3"> B
            </div>

            <div class="flex items-center mb-2">
                <input type="text" name="option_c" placeholder="Opsi C" class="border rounded w-full p-2">
                <input type="radio" name="correct_answer" value="C" class="ml-3"> C
            </div>

            <div class="flex items-center mb-2">
                <input type="text" name="option_d" placeholder="Opsi D" class="border rounded w-full p-2">
                <input type="radio" name="correct_answer" value="D" class="ml-3"> D
            </div>
        </div>

        {{-- === ESSAY === --}}
        <div id="essay_box" class="hidden">
            <label class="block font-medium mb-2">Jawaban Essay</label>
            <textarea name="essay_answer" class="border rounded w-full p-2" rows="3"></textarea>
        </div>

        {{-- === TRUE FALSE === --}}
        <div id="true_false_box" class="hidden">
            <label class="block font-medium mb-2">Jawaban</label>
            <label class="flex items-center mb-2">
                <input type="radio" name="correct_answer" value="true" class="mr-2"> Benar
            </label>
            <label class="flex items-center">
                <input type="radio" name="correct_answer" value="false" class="mr-2"> Salah
            </label>
        </div>

        <div class="flex justify-end space-x-2 mt-4">
            <a href="{{ route('admin.questions.index', $exam->id) }}"
            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Batal</a>

            <button type="submit"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Simpan
            </button>
        </div>
    </form>

    {{-- Script Dinamis --}}
    <script>
        const select = document.getElementById('question_type');
        const mcBox = document.getElementById('multiple_choice_box');
        const essayBox = document.getElementById('essay_box');
        const tfBox = document.getElementById('true_false_box');

        select.addEventListener('change', () => {
            let type = select.value;

            mcBox.classList.add('hidden');
            essayBox.classList.add('hidden');
            tfBox.classList.add('hidden');

            if (type === 'multiple_choice') mcBox.classList.remove('hidden');
            else if (type === 'essay') essayBox.classList.remove('hidden');
            else if (type === 'true_false') tfBox.classList.remove('hidden');
        });
    </script>

</div>
@endsection
