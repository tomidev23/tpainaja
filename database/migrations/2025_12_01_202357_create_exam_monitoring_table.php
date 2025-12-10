<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_monitoring', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('hasil_tes_id')->nullable()->constrained('hasil_tes')->onDelete('cascade');
            
            // Security verification data
            $table->boolean('camera_verified')->default(false);
            $table->boolean('face_detected')->default(false);
            $table->boolean('rules_accepted')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            
            // Monitoring snapshots
            $table->text('security_violations')->nullable(); // JSON array of violations
            $table->integer('tab_switch_count')->default(0);
            $table->integer('face_lost_count')->default(0);
            
            // Images
            $table->string('initial_photo')->nullable(); // Foto saat verifikasi awal
            $table->text('monitoring_photos')->nullable(); // JSON array foto monitoring
            
            $table->enum('status', ['verified', 'in_progress', 'completed', 'violated'])->default('verified');
            
            $table->timestamps();
            
            $table->index(['user_id', 'exam_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_monitoring');
    }
};