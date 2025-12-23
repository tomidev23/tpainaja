<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // GET /api/notifications
    public function index()
    {
        $userId = Auth::id();

        $notifications = Notification::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->orWhereNull('user_id'); // broadcast
        })
        ->orderBy('created_at', 'desc')
        ->limit(50)
        ->get()
        ->map(function ($notif) {
            return [
                'id' => $notif->id,
                'title' => $notif->title,
                'body' => $notif->body,
                'time' => $notif->created_at->diffForHumans(), // "2 jam yang lalu"
                'is_read' => $notif->is_read,
                'icon' => $notif->icon,
                'color' => $notif->color_hex,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    // PATCH /api/notifications/{id}/read
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where(function ($query) {
                $query->where('user_id', Auth::id())
                      ->orWhereNull('user_id');
            })
            ->firstOrFail();

        $notification->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    // PATCH /api/notifications/read-all
    public function markAllAsRead()
    {
        Notification::where(function ($query) {
            $query->where('user_id', Auth::id())
                  ->orWhereNull('user_id');
        })
        ->where('is_read', false)
        ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    // [ADMIN ONLY] POST /api/notifications
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:100',
            'body' => 'required|string|max:255',
            'icon' => 'nullable|string|in:calendar,barChart2,bookOpen,clock,refreshCw,lightbulb,award,bell',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $notif = Notification::create([
            'user_id' => $request->user_id, // null = broadcast
            'title' => $request->title,
            'body' => $request->body,
            'icon' => $request->icon ?? 'bell',
            'color_hex' => $request->color ?? '#1565C0',
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'data' => $notif,
        ], 201);
    }
}