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
            $table->string('nama_ujian'); // ✅ Nama ujian
            $table->integer('jumlah_soal')->default(0); // ✅ Jumlah soal
            $table->decimal('bobot_nilai', 5, 2)->default(100); // ✅ Bobot nilai
            $table->integer('waktu_ujian')->nullable(); // ✅ Durasi ujian (menit)
            $table->string('logo')->nullable(); // ✅ Logo ujian
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
