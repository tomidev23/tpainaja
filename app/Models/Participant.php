<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $fillable = [
        'user_id',
        'total_score',
        'exam_taken',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
