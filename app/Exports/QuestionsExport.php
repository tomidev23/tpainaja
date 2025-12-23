<?php
use App\Models\Question;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QuestionsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Question::with('options')->get()->map(function ($q) {
            return [
                'exam_id'  => $q->exam_id,
                'type'     => $q->type,
                'question' => $q->question_text,
                'A' => optional($q->options->get(0))->option_text,
                'B' => optional($q->options->get(1))->option_text,
                'C' => optional($q->options->get(2))->option_text,
                'D' => optional($q->options->get(3))->option_text,
                'correct' => $q->options
                    ->where('is_correct', true)
                    ->keys()
                    ->map(fn($i) => chr(65 + $i))
                    ->first(),
            ];
        });
    }

    public function headings(): array
    {
        return ['exam_id','type','question','A','B','C','D','correct'];
    }
}
