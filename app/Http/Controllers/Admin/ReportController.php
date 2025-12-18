<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HasilTes;

class ReportController extends Controller
{
    public function index()
    {
        $results = HasilTes::with(['user', 'exam'])
            ->orderByDesc('submitted_at')
            ->get();

        return view('admin.reports.index', compact('results'));
    }

    public function show($id)
    {
        $result = HasilTes::with(['user', 'exam'])
            ->findOrFail($id);

        return view('admin.reports.show', compact('result'));
    }
}