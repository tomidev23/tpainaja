<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\HasilTes;
use Illuminate\Support\Facades\Auth;

class ExamHistoryController extends Controller
{
  /**
 * Display the specified exam history details.
 */
public function show($id)
{
    $history = HasilTes::with([
        'exam',
        'exam.questions.options', 
    
    ])
        ->where('user_id', Auth::user()?->id)
        ->findOrFail($id);

   
// Pastikan answers adalah array, lalu wrap dengan collect()
$answersArray = $history->answers ?? []; // ini array, bukan relasi
$userAnswersMap = collect($answersArray)->keyBy('question_id');

    return response()->json([
        'data' => [
            'id' => $history->id,
            'nama_ujian' => $history->exam?->nama_ujian ?? 'Ujian',
            'score' => (int) $history->score,
            'jawaban_benar' => (int) $history->jawaban_benar,
            'total_questions' => (int) $history->total_questions,
            'submitted_at' => $history->submitted_at,
            'questions' => $history->exam?->questions?->map(function ($q) use ($userAnswersMap) {
                $userAnswer = $userAnswersMap->get($q->id);

                return [
                    'number' => (int) $q->number,
                    'question_text' => $q->text,
                    'options' => $q->options?->pluck('text')->toArray() ?? [],
                    'correct_option_index' => (int) $q->correct_option_index,
                    'user_answer_index' => $userAnswer?->selected_option_index, // bisa null
                    'is_correct' => $userAnswer?->is_correct ?? false,
                ];
            })->values() ?? [],
        ]
    ]);
}
}