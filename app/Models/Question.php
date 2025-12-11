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
        'option_a',   // Opsional jika tetap menggunakan schema sebelumnya
        'option_b',   // Opsional
        'option_c',   // Opsional
        'option_d',   // Opsional
        'correct_option',
    ];

    // Relasi dengan model Exam
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    // Relasi dengan model Option
     public function options()
    {
        return $this->hasMany(Option::class);  // Relasi hasMany dengan Option
    }
}
