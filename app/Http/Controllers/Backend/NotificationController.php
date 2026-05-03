<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Will return admin notifications
     * 
     * @return mixed
     */
    public function adminNotifications(): JsonResponse
    {
        try {
            $notifications = auth()->user()->unreadNotifications;
            $notifications = $notifications->map(function ($item) {
                return [
                    'id' => $item->id,
                    'message' => $item->data['message'],
                    'link' => $item->data['link'],
                    'time' => $item->created_at->diffForHumans()
                ];
            });

            return response()->json([
                'success' => true,
                'notifications' => $notifications
            ]);
        } catch (\Exception $e) {
            dd($e);
            return response()->json([
                'success' => false,
            ]);
        }
    }

    /**
     * Will mark as read single notification
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminNotificationMarkAsRead(Request $request)
    {
        try {
            $notification = auth()->user()->unreadNotifications()->where('id', $request['id'])->first();

            if ($notification != null) {
                $notification->markAsRead();

                $link = $notification->data['link'];
                $unread_notification = auth()->user()->unreadNotifications;
                $unread_notification = $unread_notification->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'message' => $item->data['message'],
                        'link' => $item->data['link'],
                        'time' => $this->notificationTime($item->created_at)
                    ];
                });

                return response()->json(
                    [
                        'success' => true,
                        'link' => $link,
                        'unread_notification' => $unread_notification
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false
                    ]
                );
            }
        } catch (\Exception $e) {
            dd($e);
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
    /**
     * Will mark as read all notifications
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminAllNotificationMarkAsRead(Request $request)
    {
        try {
            auth()->user()->unreadNotifications->markAsRead();
            return response()->json(
                [
                    'success' => true
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false
                ]
            );
        }
    }
}
