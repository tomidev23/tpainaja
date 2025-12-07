<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\HasilTes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;


class ExamController extends Controller
{
    /**
     * Get list of all exams
     */
    public function index()
    {
        Log::info('Fetching all exams with questions.');

        // Get exams with questions and their options
        $exams = Exam::with('questions.options')->get();

        return response()->json([
            'status' => 'success',
            'data' => $exams,
        ], 200);
    }

    /**
     * Get specific exam detail
     */
    public function show($id)
    {
        $exam = Exam::with('questions.options')->find($id);

        if (!$exam) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exam not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $exam,
        ], 200);
    }

    /**
     * Get questions for specific exam
     */
public function getQuestions($examId)
{
    $exam = Exam::with('questions')->find($examId);

    if (!$exam) {
        return response()->json([
            'status' => 'error',
            'message' => 'Exam not found',
        ], 404);
    }

    // Jika `questions` kosong, kirimkan array kosong
    $questions = $exam->questions ?: [];

    return response()->json([
        'status' => 'success',
        'data' => [
            'exam' => [
                'id' => $exam->id,
                'title' => $exam->title,
                'duration' => $exam->duration,
                'exam_type'=> $exam->exam_type,
            ],
            'questions' => $questions,  // Pastikan selalu berupa array
        ],
    ], 200);
}

    /**
     * Submit exam result
     */
  public function submitResult(Request $request)
{
    $user = $request->user();

    if (!$user) {
        return response()->json([
            'message' => 'Unauthenticated',
        ], 401);
    }

    $validator = Validator::make($request->all(), [
        'exam_id' => 'required|exists:exams,id',
        'answers' => 'required|array',
        'answers.*.question_id' => 'required|exists:questions,id',
        'answers.*.chosen_option' => 'required|string|in:a,b,c,d,option_a,option_b,option_c,option_d',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);
    }

    $examId = $request->exam_id;
    $answers = $request->answers;

    // Enkripsi jawaban peserta
    $encryptedAnswers = Crypt::encryptString(json_encode($answers)); // Enkripsi jawaban

    // Hitung skor
    $totalScore = 0;
    $correctAnswers = 0;
    $totalQuestions = count($answers);

    foreach ($answers as $answer) {
        $question = Question::find($answer['question_id']);
        
        if ($question && $question->correct_answer === $answer['chosen_option']) {
            $correctAnswers++;
        }
    }

    if ($totalQuestions > 0) {
        $totalScore = round(($correctAnswers / $totalQuestions) * 100, 2);
    }

    try {
        $hasilTes = HasilTes::create([
            'user_id' => $user->id,
            'exam_id' => $examId,
            'score' => $totalScore,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'answers' => $encryptedAnswers, // Simpan jawaban yang sudah dienkripsi
            'submitted_at' => now(),
        ]);

        Log::info('Exam result submitted', [
            'user_id' => $user->id,
            'exam_id' => $examId,
            'score' => $totalScore,
        ]);

        return response()->json([
            'message' => 'Exam submitted successfully',
            'hasil_tes_id' => $hasilTes->id,
            'score' => $totalScore,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
        ], 200);
    } catch (\Exception $e) {
        Log::error('Error submitting exam result: ' . $e->getMessage());

        return response()->json([
            'message' => 'Error occurred while saving the exam result',
        ], 500);
    }
}

    /**
     * Get exam result detail
     */
    public function getHasilUjian($hasilTesId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $hasilTes = HasilTes::with(['exam.questions', 'user'])
            ->where('id', $hasilTesId)
            ->where('user_id', $user->id) // Ensure user can only see their own result
            ->first();

        if (!$hasilTes) {
            return response()->json([
                'message' => 'Exam result not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $hasilTes->id,
                'title' => $hasilTes->exam->nama_ujian ?? 'No title',
                'image' => $hasilTes->exam->questions->first()->logo ?? '', // Check for null
                'total_questions' => $hasilTes->total_questions,
                'score' => $hasilTes->score,
                'correct_answers' => $hasilTes->correct_answers,
                'submitted_at' => $hasilTes->submitted_at->format('d M Y H:i'),
                'exam' => $hasilTes->exam,
            ],
        ], 200);
    }

    /**
     * Get user's exam history
     */
  /**
 * Get current user's exam history
 */
public function getUserHistory(Request $request)
{
    $user = $request->user(); // ✅ dari token, otomatis

    $history = HasilTes::with('exam')
        ->where('user_id', $user->id) // ✅ aman: hanya data milik user ini
        ->orderBy('submitted_at', 'desc')
        ->get()
        ->map(function ($hasil) {
            return [
                'id' => $hasil->id,
                'exam_id' => $hasil->exam_id,
                'title' => $hasil->exam->nama_ujian ?? 'Ujian Tanpa Judul',
                'questions_logo' => $hasil->exam->logo ?? '',
                'score' => (int) round($hasil->score),
                'correct_answers' => (int) $hasil->correct_answers,
                'total_questions' => (int) $hasil->total_questions,
                'submitted_at' => $hasil->submitted_at?->toIso8601String() ?? '',
            ];
        });

    return response()->json([
        'status' => 'success',
        'data' => $history,
    ]);
}

}