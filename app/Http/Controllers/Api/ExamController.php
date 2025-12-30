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

    if (!$user) {
        return response()->json([
            'message' => 'Unauthenticated',
        ], 401);
    }

    $validator = Validator::make($request->all(), [
        'exam_id' => 'required|exists:exams,id',
        'answers' => 'required|array',
        'answers.*.question_id' => 'required|exists:questions,id',
        'answers.*.chosen_option' => 'required|string|in:a,b,c,d,option_a,option_b,option_c,option_d,A,B,C,D',
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
    $encryptedAnswers = json_encode($answers);
    
    // Hitung skor
    $totalScore = 0;
    $correctAnswers = 0;
    $totalQuestions = count($answers);

    foreach ($answers as $answer) {
        $question = Question::find($answer['question_id']);
        
        if ($question) {
            // ✅ Normalisasi ke uppercase untuk perbandingan case-insensitive
            $correctAnswer = strtoupper(trim($question->jawaban_benar ?? ''));
            $userAnswer = strtoupper(trim($answer['chosen_option'] ?? ''));
            
            // ✅ Konversi opsi teks ke huruf (jika dikirim "option_a", ubah jadi "A")
            if (strpos($userAnswer, 'OPTION_') === 0) {
                $userAnswer = strtoupper(substr($userAnswer, 7, 1)); // ambil huruf setelah "option_"
            }
            
            if ($correctAnswer === $userAnswer) {
                $correctAnswers++;
            }
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
            'answers' => $encryptedAnswers,
            'submitted_at' => now(),
        ]);

        // ✅ Update is_completed di tabel exams untuk exam ini
        $exam = Exam::find($examId);
        if ($exam) {
            $exam->update(['is_completed' => true]);
        }

        Log::info('Exam result submitted', [
            'user_id' => $user->id,
            'exam_id' => $examId,
            'score' => $totalScore,
            'correct_answers' => $correctAnswers,
        ]);

        return response()->json([
            'message' => 'Exam submitted successfully',
            'hasil_tes_id' => $hasilTes->id,
            'score' => $totalScore,
            'jawaban_benar' => $correctAnswers,
            'total_questions' => $totalQuestions,
        ], 200);

    } catch (\Exception $e) {
        Log::error('Error submitting exam result: ' . $e->getMessage(), [
            'exam_id' => $examId,
            'user_id' => $user->id,
            'answers_count' => count($answers),
        ]);

        return response()->json([
            'message' => 'Error occurred while saving the exam result',
            'debug' => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
}
    

public function getExamDetail($hasilTesId)
{
    // Ambil hasil tes berdasarkan ID
    $hasilTes = HasilTes::with('exam') // Memuat relasi 'exam' yang tepat
        ->where('id', $hasilTesId)
        ->first();

    if (!$hasilTes) {
        return response()->json([
            'message' => 'Hasil tes tidak ditemukan',
        ], 404);
    }

    // Kembalikan data ujian terkait dengan hasil tes
    return response()->json([
        'status' => 'success',
        'data' => $hasilTes->exam, // Kembalikan data exam yang terkait
    ], 200);
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
            ->where('user_id', $user->id) 
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
                'jawaban_benar' => (int) ($hasilTes->correct_answers ?? 0), 
                'submitted_at' => $hasilTes->submitted_at->format('d M Y H:i'),
                'exam' => $hasilTes->exam,
                'answers' => json_decode($hasilTes->answers), // Add this line to decode answers if they are JSON

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