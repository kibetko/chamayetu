<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get the authenticated user's notifications.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->latest()
            ->get()
            ->map(function ($notification) {
                $data = $notification->data ?? [];

                return [
                    'id' => $notification->id,

                    'title' =>
                        $data['title']
                        ?? 'Notification',

                    'message' =>
                        $data['message']
                        ?? $data['body']
                        ?? '',

                    'type' =>
                        $data['type']
                        ?? 'general',

                    'icon' =>
                        $data['icon']
                        ?? null,

                    'read_at' =>
                        $notification->read_at,

                    'is_read' =>
                        $notification->read_at !== null,

                    'created_at' =>
                        $notification->created_at,

                    'created_at_human' =>
                        $notification->created_at
                            ? $notification->created_at->diffForHumans()
                            : null,
                ];
            });

        return response()->json([
            'success' => true,

            'notifications' =>
                $notifications,

            'unread_count' =>
                $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark one notification as read.
     */
    public function markAsRead(
        Request $request,
        string $id
    ) {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found.',
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
            'unread_count' =>
                $request->user()
                    ->unreadNotifications()
                    ->count(),
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(
        Request $request
    ) {
        $request->user()
            ->unreadNotifications
            ->markAsRead();

        return response()->json([
            'success' => true,
            'message' =>
                'All notifications marked as read.',

            'unread_count' => 0,
        ]);
    }

    /**
     * Delete one notification.
     */
    public function destroy(
        Request $request,
        string $id
    ) {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found.',
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted.',
        ]);
    }
}