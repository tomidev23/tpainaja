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
        $exams = Exam::with('questions')
         ->where('is_completed', false)
         ->get();

        return response()->json([
            'status' => 'success',
            'data' => $exams,
        ], 200);
    }

    /**
     * Get specific exam detail
     */
   // ExamController.php
public function show($id)
{
    try {
        // Ambil exam tanpa eager loading dulu (test dasar)
        $exam = Exam::where('id', $id)->first();
        if (!$exam) {
            return response()->json([
                'message' => 'Ujian tidak ditemukan',
            ], 404);
        }

        // Ambil questions terpisah (lebih aman)
        $questions = Question::where('exam_id', $exam->id)
            ->where('aktif', 1)
            ->get();

        // Bangun data manual — hindari asset() di null
        $examData = [
            'id' => $exam->id,
            'nama_ujian' => $exam->nama_ujian,
            'duration' => (int) $exam->duration,
            'question_count' => $questions->count(),
            'logo' => $exam->logo ? "storage/{$exam->logo}" : null,
            'questions' => $questions->map(function ($q) {
                return [
                    'id' => $q->id,
                    'question_text' => $q->question_text,
                    'question_file' => $q->question_file ? "storage/{$q->question_file}" : null,
                    'option_a' => $q->option_a,
                    'option_b' => $q->option_b,
                    'option_c' => $q->option_c,
                    'option_d' => $q->option_d,
                    'jawaban_benar' => $q->jawaban_benar,
                ];
            })->toArray(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $examData,
        ]);

    } catch (\Exception $e) {
        Log::error("Exam show error (id=$id): " . $e->getMessage());
        return response()->json([
            'message' => 'Gagal memuat data ujian',
            'debug' => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
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
    if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

    $validator = Validator::make($request->all(), [
        'exam_id' => 'required|exists:exams,id',
        'answers' => 'required|array|min:1',
        'answers.*.question_id' => 'required|integer|exists:questions,id',
        'answers.*.chosen_option' => 'required|string|in:a,b,c,d,A,B,C,D,option_a,option_b,option_c,option_d',
    ]);

    if ($validator->fails()) {
        Log::warning('Validation failed', ['errors' => $validator->errors()]);
        return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
    }

    try {
        $examId = $request->exam_id;
        $answers = $request->answers;
        $correctAnswers = 0;
        $totalQuestions = count($answers);

        // Hitung skor dengan aman
        foreach ($answers as $answer) {
            $question = Question::find($answer['question_id'] ?? 0);
            if (!$question) continue;

            $correct = strtoupper(trim($question->jawaban_benar ?? ''));
            $userAns = strtoupper(trim($answer['chosen_option'] ?? ''));

            if (strpos($userAns, 'OPTION_') === 0) {
                $userAns = substr($userAns, 7, 1);
            }

            if ($correct === $userAns && in_array($correct, ['A','B','C','D'])) {
                $correctAnswers++;
            }
        }

        $totalScore = $totalQuestions ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;

        // ✅ SIMPAN DENGAN NULL-SAFE
        $hasilTes = new HasilTes();
        $hasilTes->user_id = $user->id;
        $hasilTes->exam_id = $examId;
        $hasilTes->score = $totalScore;
        $hasilTes->correct_answers = $correctAnswers;
        $hasilTes->total_questions = $totalQuestions;
        $hasilTes->answers = json_encode($answers);
        $hasilTes->submitted_at = now();
        $hasilTes->save();

        // Update exam status
        Exam::where('id', $examId)->update(['is_completed' => true]);

        Log::info('Submit success', ['id' => $hasilTes->id, 'correct' => $correctAnswers]);

        return response()->json([
            'message' => 'Success',
            'hasil_tes_id' => $hasilTes->id,
            'score' => $totalScore,
            'jawaban_benar' => $correctAnswers,
            'total_questions' => $totalQuestions,
        ], 200);

    } catch (\Exception $e) {
        Log::error('Submit failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return response()->json(['message' => 'Server error'], 500);
    }
}
    /**
     * Get exam detail from exam result
     */ 

public function getExamDetail($hasilTesId)
{
    $user = Auth::user();
    if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

    $hasilTes = HasilTes::with(['exam.questions'])
        ->where('id', $hasilTesId)
        ->where('user_id', $user->id)
        ->first();

    if (!$hasilTes) return response()->json(['message' => 'Not found'], 404);

    // ✅ Decode jawaban user
    $answers = is_string($hasilTes->answers) 
        ? json_decode($hasilTes->answers, true) 
        : $hasilTes->answers;

    // ✅ Bangun data soal dengan jawaban user
    $questionsData = [];
    foreach ($hasilTes->exam->questions as $index => $q) {
        $userAns = collect($answers)->firstWhere('question_id', $q->id);
        $userOption = $userAns['chosen_option'] ?? '';

        // Normalisasi ke huruf
        $userLetter = strtoupper(trim($userOption));
        if (strpos($userLetter, 'OPTION_') === 0) {
            $userLetter = substr($userLetter, 7, 1);
        }

        // Cari index opsi (A=0, B=1, C=2, D=3)
        $userIndex = array_search($userLetter, ['A','B','C','D']);
        $correctIndex = array_search(strtoupper($q->jawaban_benar ?? 'A'), ['A','B','C','D']);

        $questionsData[] = [
            'number' => $index + 1,
            'question_text' => $q->question_text,
            'options' => [
                $q->option_a,
                $q->option_b,
                $q->option_c,
                $q->option_d,
            ],
            'correct_option_index' => $correctIndex !== false ? $correctIndex : 0,
            'user_answer_index' => $userIndex !== false ? $userIndex : null,
            'is_correct' => $userLetter === strtoupper($q->jawaban_benar ?? ''),
        ];
    }

    return response()->json([
        'data' => [
            'id' => $hasilTes->id,
            'title' => $hasilTes->exam->nama_ujian ?? 'Ujian',
            'exam_logo_url' => $hasilTes->exam->logo 
                ? "https://tpainaja-main-yyhqxv.laravel.cloud/storage/{$hasilTes->exam->logo}"
                : '',
            'score' => (int) $hasilTes->score,
            'correct_answers' => (int) $hasilTes->correct_answers,
            'total_questions' => (int) $hasilTes->total_questions,
            'submitted_at' => $hasilTes->submitted_at?->toIso8601String() ?? '',
            'questions' => $questionsData,
        ],
    ], 200);
}
    /**
     * Get exam result detail
     */
  public function getHasilUjian($hasilTesId)
{
    $user = Auth::user();
    if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

    $hasilTes = HasilTes::with(['exam.questions', 'user'])
        ->where('id', $hasilTesId)
        ->where('user_id', $user->id)
        ->first();

    if (!$hasilTes) return response()->json(['message' => 'Not found'], 404);

    return response()->json([
        'status' => 'success',
        'data' => [
            'id' => $hasilTes->id,
            'title' => $hasilTes->exam?->nama_ujian ?? 'Ujian',
            'image' => $hasilTes->exam?->logo ?? '', // ✅ lebih aman
            'total_questions' => (int) $hasilTes->total_questions,
            'score' => (float) $hasilTes->score,
            'jawaban_benar' => (int) $hasilTes->correct_answers,
            'submitted_at' => $hasilTes->submitted_at?->format('d M Y H:i') ?? '',
            'exam' => $hasilTes->exam,
            'answers' => $hasilTes->answers, // ✅ tanpa json_decode
        ],
    ], 200);
}
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
                'jawaban_benar' => (int) ($hasil->correct_answers ?? 0), // ✅                'total_questions' => (int) $hasil->total_questions,
                'submitted_at' => $hasil->submitted_at?->toIso8601String() ?? '',
            ];
        });

    return response()->json([
        'status' => 'success',
        'data' => $history,
    ]);
}

}