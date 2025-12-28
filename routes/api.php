<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\ExamMonitoringController;
use App\Http\Controllers\Api\SecurityController;
use App\Http\Controllers\Api\ExamHistoryController;
use App\Http\Controllers\Api\NotificationController;



// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/auth/google', [AuthController::class, 'googleLogin']);

Route::get('/api/exam/{exam}', [ExamController::class, 'show']);

    Route::get('/exam', [ExamController::class, 'index']);
Route::get('/exam/{id}', [ExamController::class, 'show']);


Route::middleware('auth:sanctum')->group(function () {

    // Auth endpoints
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Exam endpoints
    Route::get('/exam/{examId}/questions', [ExamController::class, 'getQuestions']);
    Route::post('/exam/submitResult', [ExamController::class, 'submitResult']);
    
    // Hasil ujian endpoints
    Route::get('/hasil-ujian/{hasilTesId}', [ExamController::class, 'getHasilUjian']);
    Route::get('/exam-history', [ExamController::class, 'getUserHistory']);

    //Detail Hasil Ujian
    Route::get('/exam-history/{id}', [ExamHistoryController::class, 'show']); 
    
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});


// Default route for testing
Route::get('/', function () {
    return response()->json([
        'message' => 'TPAinaja API',
        'version' => '1.0.0',
        'status' => 'active',
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    
    // Exam monitoring routes
    Route::prefix('monitoring')->group(function () {
        Route::post('/start', [ExamMonitoringController::class, 'startMonitoring']);
        Route::put('/{monitoringId}', [ExamMonitoringController::class, 'updateMonitoring']);
        Route::post('/{monitoringId}/finish', [ExamMonitoringController::class, 'finishMonitoring']);
        Route::get('/{monitoringId}', [ExamMonitoringController::class, 'getMonitoring']);
        Route::get('/exam/{examId}', [ExamMonitoringController::class, 'getExamMonitoring']);
    });
});

// Security routes
Route::middleware('auth:sanctum')->group(function () {
    //Keamanan profil
    Route::get('/security/status', [SecurityController::class, 'status']);
    Route::post('/security/2fa/enable', [SecurityController::class, 'enable2FA']);
    Route::post('/security/2fa/disable', [SecurityController::class, 'disable2FA']);
    Route::post('/security/2fa/verify', [SecurityController::class, 'verify2FA']); // saat setup
    Route::post('/security/logout-other-devices', [SecurityController::class, 'logoutOtherDevices']);

    //Ubah password (dari Ubahpasswordscreen)
    Route::post('/change-password', [SecurityController::class, 'changePassword']);

    //Update profil (dari EditProfileScreen)
    Route::put('/profile', [SecurityController::class, 'updateProfile']);
});