<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
    'exam_id',
    'question_text',
    'question_file', 
    'option_a',
    'option_b',
    'option_c',
    'option_d',
    'jawaban_benar',
    'skor_maks',
    'jenis_soal',
    'aktif',
];


    // Relasi dengan model Exam
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    

}
