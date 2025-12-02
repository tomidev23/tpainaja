<?php

namespace App\Http\Controllers\staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Question; 
use Illuminate\Support\Facades\Storage;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::latest()->get();
        return view('staff.exam.index', compact('exams'));
    }

    public function create()
    {
        return view('staff.exam.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_ujian'      => 'required|string|max:255',
            'question_count'  => 'required|integer|min:1',
            'weight'          => 'required|numeric|min:0',
            'duration'        => 'required|integer|min:1',
            'exam_type'       => 'required|in:tpa,cbt',    // ⬅️ sudah benar
            'exam_date'       => 'required|date',
            'logo'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        Exam::create($validated);

        return redirect()->route('staff.exam.index')
                         ->with('success', 'Ujian berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $exam = Exam::findOrFail($id);
        return view('staff.exam.edit', compact('exam'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_ujian'      => 'required|string|max:255',
            'question_count'  => 'required|integer|min:1',  // ⬅️ sudah diperbaiki!
            'weight'          => 'required|numeric|min:0',
            'duration'        => 'required|integer|min:1',
            'exam_type'       => 'required|in:tpa,cbt',      // ⬅️ FIX TERPENTING!
            'exam_date'       => 'required|date',
            'logo'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $exam = Exam::findOrFail($id);

        if ($request->hasFile('logo')) {
            if ($exam->logo && Storage::disk('public')->exists($exam->logo)) {
                Storage::disk('public')->delete($exam->logo);
            }

            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        } else {
            $validated['logo'] = $exam->logo;
        }

        $exam->update($validated);

        return redirect()->route('staff.exam.index')
                         ->with('success', 'Ujian berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $exam = Exam::findOrFail($id);

        if ($exam->logo && Storage::disk('public')->exists($exam->logo)) {
            Storage::disk('public')->delete($exam->logo);
        }

        $exam->delete();

        return redirect()->route('staff.exam.index')
                         ->with('success', 'Ujian berhasil dihapus!');
    }

    public function questions($id)
    {
        $exam = Exam::findOrFail($id);

        $questions = Question::where('exam_id', $exam->id)->get();

        return view('staff.questions.index', compact('exam', 'questions'));
    }
}
