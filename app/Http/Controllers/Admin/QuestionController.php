<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    /**
     * Tampilkan daftar soal untuk ujian tertentu
     */
    public function index(Request $request, $exam_id)
    {
        $exam = Exam::findOrFail($exam_id);
        $questions = $exam->questions;

        // API request
        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Daftar soal berhasil diambil',
                'exam_id' => $exam->id,
                'data' => $questions,
            ]);
        }

        // Web (Blade)
        return view('admin.questions.index', compact('exam', 'questions'));
    }

    /**
     * Form tambah soal (web only)
     */
    public function create($exam_id)
    {
        $exam = Exam::findOrFail($exam_id);
        return view('admin.questions.create', compact('exam'));
    }

    /**
     * Simpan soal baru
     */
   public function store(Request $request, $exam_id)
{
    $type = $request->question_type;

    // Validasi dinamis
    if ($type == 'multiple_choice') {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|String|in:A,B,C,D',
            'question_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
    } 
    else if ($type == 'essay') {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'essay_answer' => 'required|string',
            'question_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
    }
    else if ($type == 'true_false') {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'correct_answer' => 'required|in:true,false',
            'question_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
    }

    $exam = Exam::findOrFail($exam_id);

    // Upload file
    if ($request->hasFile('question_file')) {
        $validated['question_file'] = 
            $request->file('question_file')->store('questions', 'public');
    }

    // Save question
    $validated['question_type'] = $type;

    $exam->questions()->create($validated);

    return redirect()
        ->route('admin.questions.index', $exam_id)
        ->with('success', 'Soal berhasil ditambahkan!');
}


    /**
     * Edit soal
     */
    public function edit($exam_id, $id)
    {
        $exam = Exam::findOrFail($exam_id);
        $question = Question::findOrFail($id);

        return view('admin.questions.edit', compact('exam', 'question'));
    }

    /**
     * Update soal
     */
    public function update(Request $request, $exam_id, $id)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'option_a' => 'required|string|max:255',
            'option_b' => 'required|string|max:255',
            'option_c' => 'required|string|max:255',
            'option_d' => 'required|string|max:255',
            'correct_answer' => 'required|in:A,B,C,D',
            'question_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $question = Question::findOrFail($id);

        // Replace file jika upload baru
        if ($request->hasFile('question_file')) {
            if ($question->question_file && Storage::disk('public')->exists($question->question_file)) {
                Storage::disk('public')->delete($question->question_file);
            }

            $validated['question_file'] = 
                $request->file('question_file')->store('questions', 'public');
        }

        $question->update($validated);

        return redirect()
            ->route('admin.questions.index', $exam_id)
            ->with('success', 'Soal berhasil diperbarui!');
    }

    /**
     * Hapus soal
     */
    public function destroy($exam_id, $id)
    {
        $question = Question::findOrFail($id);

        if ($question->question_file && Storage::disk('public')->exists($question->question_file)) {
            Storage::disk('public')->delete($question->question_file);
        }

        $question->delete();

        return redirect()
            ->route('admin.questions.index', $exam_id)
            ->with('success', 'Soal berhasil dihapus!');
    }
}
