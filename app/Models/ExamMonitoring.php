<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamMonitoring extends Model
{
    protected $table = 'exam_monitoring';
    
    protected $fillable = [
        'user_id',
        'exam_id',
        'hasil_tes_id',
        'camera_verified',
        'face_detected',
        'rules_accepted',
        'started_at',
        'finished_at',
        'security_violations',
        'tab_switch_count',
        'face_lost_count',
        'initial_photo',
        'monitoring_photos',
        'status',
    ];

    protected $casts = [
        'camera_verified' => 'boolean',
        'face_detected' => 'boolean',
        'rules_accepted' => 'boolean',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'security_violations' => 'array',
        'monitoring_photos' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function hasilTes()
    {
        return $this->belongsTo(HasilTes::class);
    }
}