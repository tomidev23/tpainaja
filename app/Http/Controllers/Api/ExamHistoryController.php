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
        $history = HasilTes::with('exam.questions')
            ->where('user_id', Auth::user()?->id)
            ->findOrFail($id);

        $answersArray = is_string($history->answers) 
            ? json_decode($history->answers, true) 
            : ($history->answers ?? []);

        $userAnswersMap = collect($answersArray)->keyBy('question_id');

        return response()->json([
            'data' => [
                'id' => $history->id,
                'nama_ujian' => $history->exam?->nama_ujian ?? 'Ujian',
                'score' => (int) $history->score,
                'jawaban_benar' => (int) $history->correct_answers,
                'total_questions' => (int) $history->total_questions,
                'submitted_at' => $history->submitted_at,
                'questions' => $history->exam?->questions?->map(function ($q, $index) use ($userAnswersMap) {
                    // Ambil jawaban user
                    $userAnswer = $userAnswersMap->get($q->id);
                    $userLetter = strtoupper(trim($userAnswer['chosen_option'] ?? ''));
                    
                    // Ambil jawaban benar
                    $correctLetter = strtoupper($q->jawaban_benar ?? 'A');
                    if (!in_array($correctLetter, ['A','B','C','D'])) {
                        $correctLetter = 'A';
                    }

                    // Konversi huruf ke index (A=0, B=1, C=2, D=3)
                    $letterToIndex = ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3];
                    $correctIndex = $letterToIndex[$correctLetter];
                    $userIndex = $letterToIndex[$userLetter] ?? null;

                    return [
                        'number' => $index + 1,
                        'question_text' => $q->question_text ?? 'Soal tidak tersedia',
                        'options' => [
                            $q->option_a ?? 'Opsi A',
                            $q->option_b ?? 'Opsi B',
                            $q->option_c ?? 'Opsi C',
                            $q->option_d ?? 'Opsi D',
                        ],
                        'correct_option_index' => $correctIndex,
                        'user_answer_index' => $userIndex,
                        'is_correct' => $userLetter === $correctLetter,
                    ];
                })->values() ?? [],
            ]
        ]);
    }
}