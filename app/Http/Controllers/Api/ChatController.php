<?php

namespace App\Http\Controllers\Api;

use App\Http\ApiResource\ChatCollection;
use App\Http\Controllers\Api\ApiController;
use App\Models\Ad;
use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends ApiController
{
    /**
     * Will create new chat
     */
    public function createChat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|max:500'
        ]);
        try {
            DB::beginTransaction();
            $chat = Chat::where('ad_id', $request['ad_id'])
                ->where('user_id', auth('jwt-customer')->user()->id)
                ->first();
            if ($chat != null) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => __tr('You already create a chat')
                ]);
            } else {
                $ad_details = Ad::find($request['ad_id']);
                if ($ad_details != null) {
                    //Create new chat
                    $new_chat = new Chat();
                    $new_chat->ad_id = $ad_details->id;
                    $new_chat->user_id = auth('jwt-customer')->user()->id;
                    $new_chat->receiver_user_id = $ad_details->user_id;
                    $new_chat->save();
                    //Store chat message
                    $chat_message = new ChatMessage();
                    $chat_message->sender_id = auth('jwt-customer')->user()->id;
                    $chat_message->chat_id = $new_chat->id;
                    $chat_message->message = xss_clean($request['message']);
                    $chat_message->save();

                    //send notification
                    // NotificationHandler::sendChatNotification($new_chat->id, $ad_details->user_id, auth('jwt-customer')->user()->id, $request['message']);
                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => __tr('Chat created successfully', session()->get('api_locale'))
                    ]);
                } else {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => __tr('Invalid ad')
                    ]);
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __tr('Something went wrong')
            ]);
        }
    }
    /**
     * Will return customer chat
     */
    public function chatList(Request $request): ChatCollection
    {
        $chats = Chat::with(['messages' => function ($q) {
            $q->with(['sender' => function ($q1) {
                $q1->select('id', 'name', 'image');
            }])->select(['message', 'id', 'chat_id', 'sender_id', 'created_at']);
        }, 'ad' => function ($q2) {
            $q2->with(['ad_translations'])
                ->where('status', config('settings.general_status.active'))
                ->select(['title', 'id', 'thumbnail_image']);
        }])
            ->where('receiver_user_id', auth('jwt-customer')->user()->id)
            ->orWhere('user_id', auth('jwt-customer')->user()->id)
            ->paginate(10);

        return new ChatCollection($chats);
    }
    /**
     * Will return Single chat details
     */
    public function chatDetails(Request $request): SingleChatResource
    {
        $chat_details = Chat::with(['messages' => function ($q) {
            $q->with(['sender' => function ($q1) {
                $q1->select('id', 'name', 'image');
            }])->select(['message', 'id', 'chat_id', 'sender_id', 'created_at']);
        }, 'ad' => function ($q2) {
            $q2->with(['ad_translations'])
                ->where('status', config('settings.general_status.active'))
                ->select(['title', 'id', 'thumbnail_image', 'uid']);
        }])
            ->where('uid', $request['uid'])->first();

        return new SingleChatResource($chat_details);
    }
    /**
     * Will store new message
     */
    public function storeNewMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|max:500'
        ]);

        try {
            DB::beginTransaction();
            $chat_details = Chat::where('uid', $request['uid'])->first();
            $chat_message = new ChatMessage();
            $chat_message->sender_id = auth('jwt-customer')->user()->id;
            $chat_message->chat_id = $chat_details->id;
            $chat_message->message = xss_clean($request['message']);
            $chat_message->save();

            //send notification
            $receiver_id = $chat_details->user_id;
            if ($chat_details->user_id == auth('jwt-customer')->user()->id) {
                $receiver_id = $chat_details->receiver_user_id;
            }


            NotificationHandler::sendChatNewMessageNotification($chat_details->id, $receiver_id, auth('jwt-customer')->user()->id, $request['message']);
            DB::commit();
            return $this->jsonSuccess();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonError();
        }
    }
    /**
     * Will remove a chat from user dashboard
     */
    public function deleteChat(Request $request): JsonResponse
    {
        try {
            $chat_details = Chat::find($request['id']);
            if ($chat_details != null) {
                DB::beginTransaction();
                if ($chat_details->user_id == auth('jwt-customer')->user()->id) {
                    $chat_details->user_id = NULL;
                    $chat_details->save();
                }

                if ($chat_details->receiver_user_id == auth('jwt-customer')->user()->id) {
                    $chat_details->receiver_user_id = NULL;
                    $chat_details->save();
                }

                DB::commit();
                return $this->jsonSuccess();
            }

            return $this->jsonError();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonError();
        }
    }
}
