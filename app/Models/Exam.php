<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $table = 'exams';

    protected $fillable = [
        'nama_ujian',
        'question_count',
        'questions_files',
        'weight',
        'duration',
        'exam_type',
        'exam_date',
        'logo',
    ];

   public function questions()
{
    return $this->hasMany(Question::class);
}


    // Relasi dengan HasilTes
    public function hasilTes()
    {
        return $this->hasMany(HasilTes::class);
    }

    // Mendapatkan data dengan soal dan pilihan jawaban terkait
    public function getExamWithQuestions($examId)
    {
        return $this->with('questions.options')->find($examId);
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }
}