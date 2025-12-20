<?php

namespace App\Imports;

use App\Models\Question;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\{
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsEmptyRows
};

class QuestionsImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    protected int $examId;

    public function __construct(int $examId)
    {
        $this->examId = $examId;
    }

    public function model(array $row)
    {
        return new Question([
            'exam_id'        => $this->examId,
            'question_text' => $row['question_text'],
            'option_a'      => $row['option_a'],
            'option_b'      => $row['option_b'],
            'option_c'      => $row['option_c'],
            'option_d'      => $row['option_d'],
            'jawaban_benar' => strtoupper($row['jawaban_benar']),
            'skor_maks'     => $row['skor_maks'] ?? 1,
            'jenis_soal'    => 'pilihan_ganda',
            'aktif'         => 1,
        ]);
    }

    public function rules(): array
    {
        return [
            'question_text' => 'required',
            'option_a'      => 'required',
            'option_b'      => 'required',
            'option_c'      => 'required',
            'option_d'      => 'required',
            'jawaban_benar' => ['required', Rule::in(['A','B','C','D'])],
        ];
    }
}
