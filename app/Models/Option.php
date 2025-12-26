<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'value', // A, B, C, D
        'text',  // Teks pilihan
    ];

    // Relasi dengan Question
   public function question()
    {
        return $this->belongsTo(Question::class);  // Relasi belongsTo dengan Question
    }
}