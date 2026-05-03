<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Notifications\DatabaseNotification;
use App\Http\ApiResource;

\CustomerNotificationCollection;

class NotificationController extends ApiController
{
    /**
     * Will return customer all notifications
     */
    public function customerAllNotifications(Request $request): CustomerNotificationCollection
    {
        $notifications = auth('jwt-customer')->user()->notifications()->paginate(10);

        return new CustomerNotificationCollection($notifications);;
    }
    /**
     * Will update notification status as read
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request): JsonResponse
    {
        try {
            $notification = auth('jwt-customer')->user()->unreadNotifications()->where('id', $request['id'])->first();

            if ($notification) {
                $notification->markAsRead();
                $unread_notification = new CustomerNotificationCollection(auth('jwt-customer')->user()->unreadNotifications);
                return response()->json(
                    [
                        'success' => true,
                        'unread_notification' => $unread_notification
                    ]
                );
            } else {
                return $this->jsonError();
            }
        } catch (\Exception $e) {
            return $this->jsonError();
        }
    }

    /**
     * Will delete a notification
     */
    public function deleteNotification(Request $request): JsonResponse
    {
        try {
            $notification = DatabaseNotification::find($request['id']);
            if ($notification != null) {
                $notification->delete();
                return $this->jsonSuccess();
            }
            return $this->jsonError();
        } catch (\Exception $e) {
            return $this->jsonError();
        }
    }

    /**
     * Will update all notification status as read
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsReadAllNotification(Request $request): JsonResponse
    {
        try {
            auth('jwt-customer')->user()->unreadNotifications->markAsRead();
            return $this->jsonSuccess();
        } catch (\Exception $e) {
            return $this->jsonError();
        }
    }
}
