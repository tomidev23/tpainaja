<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\ProblemReport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // ReportController.php
    public function reportProblem(Request $request)
    {
        $request->validate([
        'description' => 'required|string|min:10',
        'email' => 'nullable|email',
        'screenshot' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $report = new ProblemReport();
    $report->user_id = $request->user()?->id;
    $report->email = $request->email ?? $request->user()?->email;
    $report->description = $request->description;
    
    if ($request->hasFile('screenshot')) {
        $path = $request->file('screenshot')->store('reports', 'public');
        $report->screenshot = $path;
    }

    $report->save();

    return response()->json([
        'message' => "Laporan berhasil diterima. ID: #RP" . str_pad($report->id, 6, '0', STR_PAD_LEFT)
    ], 201);
}
}