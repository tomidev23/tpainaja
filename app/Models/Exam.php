<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question;
use App\Models\HasilTes;
use App\Models\Option;

class Exam extends Model
{
    use HasFactory;

    protected $table = 'exams';

    protected $fillable = [
        'nama_ujian',
        'question_count',
        'weight',
        'duration',
        'exam_type',
        'exam_date',
        'logo',
    ];

    // Relasi dengan Question
  public function questions()
{
    return $this->hasMany(Question::class);
}

    // Relasi dengan HasilTes
    public function hasilTes()
    {
        return $this->hasMany(HasilTes::class);  // Relasi hasMany dengan HasilTes
    }

    // Mendapatkan data dengan soal dan pilihan jawaban terkait
    public function getExamWithQuestions($examId)
    {
        // Mengambil data exam beserta soal dan pilihan jawabannya
        return $this->with('questions.options')->find($examId);
    }

    public function results()
{
    return $this->hasMany(ExamResult::class);
}

}