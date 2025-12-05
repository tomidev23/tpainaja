<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'exam_id',
        'correct_answers',
        'wrong_answers',
        'empty_answers',
        'score',
        'duration_used',
    ];

    // Relasi ke peserta (Participant)
    public function participant()
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    // Relasi ke ujian
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }
}
