<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Get notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->latest()
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,

                    'title' =>
                        $notification->data['title']
                        ?? 'Notification',

                    'message' =>
                        $notification->data['message']
                        ?? $notification->data['body']
                        ?? '',

                    'type' =>
                        $notification->data['type']
                        ?? 'general',

                    'read' =>
                        !is_null($notification->read_at),

                    'read_at' =>
                        $notification->read_at,

                    'created_at' =>
                        $notification->created_at,

                    'created_at_human' =>
                        $notification->created_at
                            ? $notification->created_at->diffForHumans()
                            : null,

                    'data' =>
                        $notification->data,
                ];
            });

        return response()->json([
            'success' => true,

            'notifications' => $notifications,

            'unread_count' =>
                $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark one notification as read.
     */
    public function markAsRead(
        Request $request,
        string $notification
    ) {
        $user = $request->user();

        $notificationModel = $user->notifications()
            ->where('id', $notification)
            ->first();

        if (!$notificationModel) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found.',
            ], 404);
        }

        if (!$notificationModel->read_at) {
            $notificationModel->markAsRead();
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();

        $user->unreadNotifications->each(
            function ($notification) {
                $notification->markAsRead();
            }
        );

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
            'unread_count' => 0,
        ]);
    }
}