<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // ✅ tambahkan import
use App\Models\Exam;

class HasilTes extends Model
{
    use HasFactory;

    protected $table = 'hasil_tes';

    // ✅ Perbaiki $fillable — sesuaikan dengan nama kolom di DB
    protected $fillable = [
        'user_id',
        'exam_id',
        'score',
        'correct_answers', // ✅ BENAR — sesuai DB
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

    // ✅ Perbaiki accessor — konsisten dengan API
    public function getJawabanBenarAttribute()
    {
        return $this->correct_answers; // ✅ sekarang correct_answers tersimpan
    }

    // ✅ Opsional: agar $hasilTes->jawaban_benar bisa di-set
    public function setJawabanBenarAttribute($value)
    {
        $this->attributes['correct_answers'] = $value;
    }
}