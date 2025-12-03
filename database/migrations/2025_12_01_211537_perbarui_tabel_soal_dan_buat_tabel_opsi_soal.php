<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // ← tambahkan ini

return new class extends Migration
{
    public function up(): void
{
    Schema::table('questions', function (Blueprint $table) {
        // ✅ Cek dulu apakah kolom sudah ada — jika belum, baru tambahkan
        if (!Schema::hasColumn('questions', 'jenis_soal')) {
            $table->enum('jenis_soal', [
                'pilihan_ganda',
                'esai',
                'benar_salah',
                'numerik',
            ])->default('pilihan_ganda')->after('question_text');
        }

        if (!Schema::hasColumn('questions', 'skor_maks')) {
            $table->unsignedTinyInteger('skor_maks')->default(1)->after('correct_answer');
        }

        if (!Schema::hasColumn('questions', 'aktif')) {
            $table->boolean('aktif')->default(true)->after('updated_at');
        }
    });

    // Rename & salin data hanya jika kolom lama masih ada
    if (Schema::hasColumn('questions', 'correct_answer') && !Schema::hasColumn('questions', 'jawaban_benar')) {
        Schema::table('questions', function (Blueprint $table) {
            $table->text('jawaban_benar')->nullable()->after('skor_maks');
        });

        DB::statement("UPDATE questions SET jawaban_benar = correct_answer WHERE correct_answer IS NOT NULL");

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('correct_answer');
        });
    }

    // Buat tabel question_options hanya jika belum ada
    if (!Schema::hasTable('question_options')) {
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->string('label_opsi', 10);
            $table->text('teks_opsi');
            $table->boolean('benar')->default(false);
            $table->tinyInteger('urutan')->default(0);
            $table->timestamps();
        });
    }
}

    public function down(): void
    {
        // Hapus tabel opsional dulu
        Schema::dropIfExists('question_options');

        // Kembalikan struktur lama
        Schema::table('questions', function (Blueprint $table) {
            // Tambah kembali kolom lama
            $table->string('correct_answer')->after('option_d');
            // Salin data kembali
            DB::statement("UPDATE questions SET correct_answer = jawaban_benar");
            // Hapus kolom baru
            $table->dropColumn(['jenis_soal', 'skor_maks', 'aktif', 'jawaban_benar']);
        });
    }
};