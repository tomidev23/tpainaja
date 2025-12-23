<?php

namespace App\Http\Controllers\staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Question;

class QuestionController extends Controller
{
    /**
     * Menampilkan daftar soal untuk ujian tertentu
     * Bisa digunakan untuk API (Flutter) maupun web.
     */
    public function index(Request $request, $exam_id)
    {
        $exam = Exam::findOrFail($exam_id);
        $questions = $exam->questions ?? [];

        // Jika request berasal dari API (Accept: application/json)
        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Daftar soal berhasil diambil',
                'exam_id' => $exam->id,
                'data' => $questions
            ]);
        }

        // Jika dari web (Blade)
        return view('staff.questions.index', compact('exam', 'questions'));
    }

    /**
     * Form tambah soal untuk ujian tertentu (web only)
     */
    public function create($exam_id)
    {
        $exam = Exam::findOrFail($exam_id);
        return view('staff.questions.create', compact('exam'));
    }

    /**
     * Simpan soal baru
     * Bisa digunakan untuk API maupun web.
     */
    public function store(Request $request, $exam_id)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'option_1' => 'required|string',
            'option_2' => 'required|string',
            'option_3' => 'required|string',
            'option_4' => 'required|string',
            'jawaban_benar' => 'required|string',
        ]);

        $exam = Exam::findOrFail($exam_id);
        $question = $exam->questions()->create($validated);

        // Jika dari API
        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Soal berhasil ditambahkan',
                'data' => $question
            ], 201);
        }

        // Jika dari web
        return redirect()->route('staff.questions.index', $exam_id)
                         ->with('success', 'Soal berhasil ditambahkan!');
    }
}


