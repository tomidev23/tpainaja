<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionsImport implements ToModel, WithHeadingRow
{
    protected $examId;

    public function __construct($examId)
    {
        $this->examId = $examId;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // 1. Cari kolom SOAL (bisa 'soal', 'pertanyaan', 'question')
        $soal = $row['soal'] ?? $row['pertanyaan'] ?? $row['question'] ?? null;

        // 2. Cari kolom KUNCI JAWABAN (bisa 'jawaban_benar', 'kunci', 'answer')
        $kunci = $row['jawaban_benar'] ?? $row['kunci'] ?? $row['kunci_jawaban'] ?? $row['answer'] ?? null;

        // Jika soal atau kunci tidak ada, skip baris ini
        if (!$soal || !$kunci) {
            return null;
        }

        return new Question([
            'exam_id'       => $this->examId,
            'question_text' => $soal,
            
            // Mapping Pilihan A
            'option_a'      => $row['pilihan_a'] ?? $row['a'] ?? $row['opsi_a'] ?? $row['option_a'] ?? null,
            
            // Mapping Pilihan B
            'option_b'      => $row['pilihan_b'] ?? $row['b'] ?? $row['opsi_b'] ?? $row['option_b'] ?? null,
            
            // Mapping Pilihan C
            'option_c'      => $row['pilihan_c'] ?? $row['c'] ?? $row['opsi_c'] ?? $row['option_c'] ?? null,
            
            // Mapping Pilihan D
            'option_d'      => $row['pilihan_d'] ?? $row['d'] ?? $row['opsi_d'] ?? $row['option_d'] ?? null,
            
            // Mapping Pilihan E (jika ada)
            'option_e'      => $row['pilihan_e'] ?? $row['e'] ?? $row['opsi_e'] ?? $row['option_e'] ?? null,
            
            'jawaban_benar' => $kunci,
            'jenis_soal'    => 'pilihan_ganda', // Default set ke pilihan ganda
            'skor_maks'     => 1,
            'aktif'         => 1,
        ]);
    }
}
