<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hasil_tes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->decimal('score', 5, 2)->default(0); // Score out of 100
            $table->integer('correct_answers')->default(0);
            $table->integer('total_questions')->default(0);
            $table->json('answers')->nullable(); // Store all answers as JSON
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            // Index for faster queries
            $table->index(['user_id', 'exam_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_tes');
    }
};