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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ujian'); // Nama ujian
            $table->integer('question_count')->default(0); // Jumlah soal
            $table->decimal('weight', 5, 2)->default(100); // Bobot nilai
            $table->integer('duration')->nullable(); // Durasi ujian (menit)
            $table->enum('exam_type', ['cbt', 'tpa']); // ← FIX DI SINI
            $table->string('logo')->nullable(); // Logo ujian
                        $table->string('exam_date'); // Nama ujian

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
