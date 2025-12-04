<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use Illuminate\Support\Facades\Storage;

class ExamController extends Controller
{
    // ========================
    // INDEX
    // ========================
    public function index()
    {
        $exams = Exam::latest()->get();
        return view('admin.exam.index', compact('exams'));
    }

    // ========================
    // CREATE
    // ========================
    public function create()
    {
        return view('admin.exam.create');
    }

    // ========================
    // STORE
    // ========================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_ujian'      => 'required|string|max:255',
            'question_count'  => 'required|integer|min:1',
            'weight'          => 'required|numeric|min:0',
            'duration'        => 'required|integer|min:1',
            'exam_type'       => 'required|in:tpa,cbt',
            'exam_date'       => 'required|date',
            'logo'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        Exam::create($validated);

        return redirect()->route('admin.exam.index')
                         ->with('success', 'Ujian berhasil ditambahkan!');
    }

    // ========================
    // EDIT
    // ========================
    public function edit($id)
    {
        $exam = Exam::findOrFail($id);
        return view('admin.exam.edit', compact('exam'));
    }

    // ========================
    // UPDATE
    // ========================
    public function update(Request $request, $id)
    {
        // Pastikan lower-case supaya validasi cocok
        $request->merge([
            'exam_type' => strtolower($request->exam_type),
        ]);

        $validated = $request->validate([
            'nama_ujian'      => 'required|string|max:255',
            'question_count'  => 'required|integer|min:1',
            'weight'          => 'required|numeric|min:0',
            'duration'        => 'required|integer|min:1',
            'exam_type'       => 'required|in:tpa,cbt',
            'exam_date'       => 'required|date',
            'logo'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $exam = Exam::findOrFail($id);

        // Upload logo baru
        if ($request->hasFile('logo')) {

            if ($exam->logo && Storage::disk('public')->exists($exam->logo)) {
                Storage::disk('public')->delete($exam->logo);
            }

            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        } else {
            $validated['logo'] = $exam->logo;
        }

        $exam->update($validated);

        return redirect()->route('admin.exam.index')
                         ->with('success', 'Ujian berhasil diperbarui!');
    }

    // ========================
    // DELETE
    // ========================
    public function destroy($id)
    {
        $exam = Exam::findOrFail($id);

        if ($exam->logo && Storage::disk('public')->exists($exam->logo)) {
            Storage::disk('public')->delete($exam->logo);
        }

        $exam->delete();

        return redirect()->route('admin.exam.index')
                         ->with('success', 'Ujian berhasil dihapus!');
    }

    // ========================
    // ADD SOAL PAGE (WAJIB ADA)
    // ========================
   public function questions($id)
{
    $exam = Exam::findOrFail($id);

    $questions = \App\Models\Question::where('exam_id', $id)->get();

    return view('admin.questions.index', compact('exam', 'questions'));
}

}
