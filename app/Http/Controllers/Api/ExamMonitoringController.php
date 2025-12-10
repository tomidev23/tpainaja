<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExamMonitoring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class ExamMonitoringController extends Controller
{
    // Start monitoring - dipanggil saat verifikasi selesai
    public function startMonitoring(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'camera_verified' => 'required|boolean',
            'face_detected' => 'required|boolean',
            'rules_accepted' => 'required|boolean',
            'initial_photo' => 'required|string', // base64 image
        ]);

        // Save initial photo
        $photoPath = null;
        if ($request->initial_photo) {
            $photoPath = $this->saveBase64Image($request->initial_photo, 'initial');
        }

        $monitoring = ExamMonitoring::create([
            'user_id' => auth()->id(),
            'exam_id' => $validated['exam_id'],
            'camera_verified' => $validated['camera_verified'],
            'face_detected' => $validated['face_detected'],
            'rules_accepted' => $validated['rules_accepted'],
            'initial_photo' => $photoPath,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Monitoring started',
            'data' => [
                'monitoring_id' => $monitoring->id,
            ],
        ]);
    }

    // Update monitoring - dipanggil periodic saat ujian berlangsung
    public function updateMonitoring(Request $request, $monitoringId)
    {
        $monitoring = ExamMonitoring::findOrFail($monitoringId);

        $validated = $request->validate([
            'face_detected' => 'boolean',
            'snapshot' => 'nullable|string', // base64 image
            'violation_type' => 'nullable|string',
            'tab_switched' => 'boolean',
        ]);

        // Update counters
        if (isset($validated['face_detected']) && !$validated['face_detected']) {
            $monitoring->increment('face_lost_count');
        }

        if (isset($validated['tab_switched']) && $validated['tab_switched']) {
            $monitoring->increment('tab_switch_count');
        }

        // Save monitoring snapshot
        if ($request->snapshot) {
            $photoPath = $this->saveBase64Image($request->snapshot, 'monitoring');
            $photos = $monitoring->monitoring_photos ?? [];
            $photos[] = [
                'path' => $photoPath,
                'timestamp' => now()->toDateTimeString(),
                'face_detected' => $validated['face_detected'] ?? true,
            ];
            $monitoring->monitoring_photos = $photos;
        }

        // Log violations
        if ($request->violation_type) {
            $violations = $monitoring->security_violations ?? [];
            $violations[] = [
                'type' => $validated['violation_type'],
                'timestamp' => now()->toDateTimeString(),
            ];
            $monitoring->security_violations = $violations;
            $monitoring->status = 'violated';
        }

        $monitoring->save();

        return response()->json([
            'success' => true,
            'message' => 'Monitoring updated',
        ]);
    }

    // Finish monitoring - dipanggil saat submit ujian
    public function finishMonitoring(Request $request, $monitoringId)
    {
        $monitoring = ExamMonitoring::findOrFail($monitoringId);

        $validated = $request->validate([
            'hasil_tes_id' => 'required|exists:hasil_tes,id',
        ]);

        $monitoring->update([
            'hasil_tes_id' => $validated['hasil_tes_id'],
            'finished_at' => now(),
            'status' => $monitoring->status === 'violated' ? 'violated' : 'completed',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Monitoring finished',
        ]);
    }

    // Get monitoring detail for admin
    public function getMonitoring($monitoringId)
    {
        $monitoring = ExamMonitoring::with(['user', 'exam', 'hasilTes'])
            ->findOrFail($monitoringId);

        return response()->json([
            'success' => true,
            'data' => $monitoring,
        ]);
    }

    // Get all monitoring for specific exam (admin)
    public function getExamMonitoring($examId)
    {
        $monitoring = ExamMonitoring::with(['user'])
            ->where('exam_id', $examId)
            ->orderBy('started_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $monitoring,
        ]);
    }

    // Helper: Save base64 image
    private function saveBase64Image($base64String, $type)
    {
        try {
            // Remove header if present
            if (strpos($base64String, 'data:image') !== false) {
                $base64String = explode(',', $base64String)[1];
            }

            $image = base64_decode($base64String);
            $filename = $type . '_' . Str::random(20) . '_' . time() . '.jpg';
            $path = 'exam_monitoring/' . date('Y/m/d') . '/' . $filename;

            Storage::disk('public')->put($path, $image);

            return $path;
        } catch (\Exception $e) {
            \Log::error('Failed to save image: ' . $e->getMessage());
            return null;
        }
    }
}