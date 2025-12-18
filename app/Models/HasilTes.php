<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Exam;

class HasilTes extends Model
{
    use HasFactory;

    protected $table = 'hasil_tes';

    protected $fillable = [
        'user_id',
        'exam_id',
        'score',
        'correct_answers',
        'total_questions',
        'answers',
        'submitted_at',
    ];

    protected $casts = [
        'answers' => 'array',  
        'submitted_at' => 'datetime',
        'score' => 'float',
    ];

    /**
     * Relationship: HasilTes belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: HasilTes belongs to Exam
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    // Hapus relasi 'answers' karena bukan relasi ke model lain, tapi kolom JSON
    public function getAnswersAttribute($value)
    {
        return json_decode($value);  // Mengembalikan answers sebagai array
    }
    
}
