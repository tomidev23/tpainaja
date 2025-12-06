<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamResult;
use App\Models\Exam;
use App\Models\Participant;

class ReportController extends Controller
{
    // HALAMAN REPORT (Menampilkan seluruh hasil ujian)
    public function index()
    {
        $results = ExamResult::with(['participant.user', 'exam'])->orderBy('id', 'desc')->get();

        return view('admin.reports.index', compact('results'));
    }

    // DETAIL REPORT PER PESERTA (opsional, jika ingin)
    public function show($id)
    {
        $result = ExamResult::with(['participant.user', 'exam'])->findOrFail($id);

        return view('admin.reports.show', compact('result'));
    }
}
