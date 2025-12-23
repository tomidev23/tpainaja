@extends('staff.layouts.app')

@section('content')
<div class="container mx-auto px-6 py-4">
    <h2 class="text-2xl font-semibold mb-4">Tambah Soal untuk: {{ $exam->nama_ujian }}</h2>

    <form action="{{ route('staff.questions.store', $exam->id) }}" method="POST" class="bg-white p-6 rounded-lg shadow">
        @csrf

        <div class="mb-4">
            <label class="block font-medium">Pertanyaan</label>
            <textarea name="question" class="border rounded w-full p-2" rows="3"></textarea>
        </div>

        <div class="mb-4">
            <label class="block font-medium mb-2">Opsi Jawaban</label>

            @for ($i = 1; $i <= 4; $i++)
                <div class="flex items-center mb-2">
                    <input type="text" name="option_{{ $i }}" placeholder="Option {{ $i }}" class="border rounded w-full p-2">
                    <label class="flex items-center ml-3">
                        <input type="radio" name="jawaban_benar" value="option_{{ $i }}" class="mr-1">
                        <span class="text-gray-600 text-sm">Jawaban Benar</span>
                    </label>
                </div>
            @endfor
        </div>

        <div class="flex justify-end space-x-2 mt-4">
            <a href="{{ route('staff.questions.index', $exam->id) }}" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Batal</a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
        </div>
    </form>
</div>
@endsection
