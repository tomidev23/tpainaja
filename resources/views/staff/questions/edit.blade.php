@extends('staff.layouts.app')

@section('content')
<div class="p-6 max-w-2xl mx-auto">
    <h2 class="text-xl font-semibold mb-4">Edit Soal</h2>
    <form action="{{ route('staff.questions.update', [$exam->id, $question->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Isi Soal</label>
            <textarea name="question_text" class="w-full border p-2 rounded" required>{{ $question->question_text }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div><label>Opsi A</label><input type="text" name="option_a" value="{{ $question->option_a }}" class="w-full border p-2 rounded"></div>
            <div><label>Opsi B</label><input type="text" name="option_b" value="{{ $question->option_b }}" class="w-full border p-2 rounded"></div>
            <div><label>Opsi C</label><input type="text" name="option_c" value="{{ $question->option_c }}" class="w-full border p-2 rounded"></div>
            <div><label>Opsi D</label><input type="text" name="option_d" value="{{ $question->option_d }}" class="w-full border p-2 rounded"></div>
        </div>

        <div class="mt-3">
            <label>Jawaban Benar</label>
            <select name="jawaban_benar" class="border p-2 rounded">
                <option value="A" {{ $question->jawaban_benar == 'A' ? 'selected' : '' }}>A</option>
                <option value="B" {{ $question->jawaban_benar == 'B' ? 'selected' : '' }}>B</option>
                <option value="C" {{ $question->jawaban_benar == 'C' ? 'selected' : '' }}>C</option>
                <option value="D" {{ $question->jawaban_benar == 'D' ? 'selected' : '' }}>D</option>
            </select>
        </div>

        <button class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">Update</button>
    </form>
</div>
@endsection
