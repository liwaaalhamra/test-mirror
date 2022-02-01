<?php

namespace App\Http\Controllers\Api;


use App\Models\Api\Conversation;
use App\Models\Api\ConversationMessage;
use App\Models\Api\UserPush;
use App\Models\Api\Users;
use App\Traits\HITrait;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;


class ChatController extends Controller
{
    use HITrait;

	/**
	 * Get Conversations
	 * @param Request $request
	 * @return \stdClass
	 */
	public function conversations(Request $request) {
        $user = $this->validate_user($request);
        $conversations = Conversation::whereFriendId($user->id)->orwhere('user_id','=',$user->id)
            //->orderby('messages.created_at','desc')
            ->get()
            ->map(function ($value, $key) use ($user) {
                $value['friend_name'] = $user->id == $value['friend_id'] ? $value['user']->full_name : $value['friend']->full_name;
                $value['friend_image'] = $user->id == $value['friend_id'] ? $value['user']->photo : $value['friend']->photo;
                unset($value['friend']);
                unset($value['user']);
                return $value;
            });
        $conversations = $conversations->sortByDesc('last_message_datetime');
        $limit = 15;
        $conversations = new LengthAwarePaginator(
            $conversations->forPage(LengthAwarePaginator::resolveCurrentPage(), $limit)->values(), $conversations->count(), $limit, LengthAwarePaginator::resolveCurrentPage(), ['path' => secure_url('/api/chat/conversations')]
        );
        return response()->json($conversations);
	}

	/**
	 * Get Friend List
	 * @param Request $request
	 * @return \stdClass
	 */
	public function friends(Request $request) {
        $user = $this->validate_user($request);
        $users = Users::where('id','<>',$user->id)->orderByRaw("LOWER(full_name) asc")->get();
        return response()->json($users);
	}

    /**
     * Get Conversation Messages
     * @param Request $request
     * @return \stdClass
     */
    public function messages(Request $request) {
        $user = $this->validate_user($request);
        if(Input::has('conversation_id')){
            if(!$conversation = Conversation::where('id','=',$request->conversation_id)
                ->where(function ($query) use($user) {
                    $query->whereFriendId($user->id)->orwhere('user_id','=',$user->id);
                })->first()){
                return parent::return_error("Invalid Conversation", 401, 'messages.invalid_parameters');
            }
        }
        if(Input::has('friend_id')){
            if(!$conversation = Conversation::where(function ($query) use($user,$request) {
                $query->where(function ($query) use($user,$request) {
                    $query->whereUserId($user->id)->whereFriendId($request->friend_id);
                })->orwhere(function ($query) use($user,$request) {
                    $query->whereUserId($request->friend_id)->whereFriendId($user->id);
                });
            })->first()){
                $messages['data'] = [];
                return response()->json($messages);
            }
        }

        $conversation->unread_messages = 0;
        $conversation->save();

        $messages = ConversationMessage::whereConversationId($conversation->id)->orderby('id','desc')
            ->get()
            ->map(function ($value, $key) use ($user) {
                $value['is_my_message'] = $user->id == $value['sender_id'] ? true : false;
                return $value;
            });
        $limit = 15;
        $messages = new LengthAwarePaginator(
            $messages->forPage(LengthAwarePaginator::resolveCurrentPage(), $limit)->values(), $messages->count(), $limit, LengthAwarePaginator::resolveCurrentPage(), ['path' => secure_url('/api/chat/messages')]
        );
        return response()->json($messages);
    }


    /**
     * Send Message
     * @param Request $request
     * @return \stdClass
     */
    public function send(Request $request) {
        $user = $this->validate_user($request);
        if(!Input::has('friend_id')){
            return parent::return_error("Missing Friend ID", 401, 'messages.missing_parameter');
        }
        if(!Input::has('message')){
            return parent::return_error("Missing Message", 401, 'messages.missing_parameter');
        }
        if(!$friend = Users::where('id','<>',$user->id)->where('id','=',$request->friend_id)->first()){
            return parent::return_error("Invalid Friend", 401, 'messages.invalid_parameters');
        }
        $conversation = Conversation::where(function ($query) use($user,$request) {
            $query->where(function ($query) use($user,$request) {
                $query->whereUserId($user->id)->whereFriendId($request->friend_id);
            })->orwhere(function ($query) use($user,$request) {
                $query->whereUserId($request->friend_id)->whereFriendId($user->id);
            });
        })->first();
        if(!$conversation){
            $conversation = Conversation::create([
                'user_id' => $user->id,
                'friend_id' => $request->friend_id,
                'unread_messages' => 1
            ]);
        }else{
            $conversation->unread_messages = $conversation->unread_messages + 1;
            $conversation->save();
        }
        $message = ConversationMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'receiver_id' => $request->friend_id,
            'message' => trim($request->message),
            //'is_my_message' => $conversation->user_id == $user->id ? 1 : 0
        ]);
        if($user_push = UserPush::whereUsersId($request->friend_id)->first()){
            $players_ids[] = $user_push->player_id;
            $contents = ["en" => trim($request->message)];
            $headings = ["en" => $user->full_name .' sent you a new message'];
            $info['contents'] = $contents;
            $info['headings'] = $headings;
            $info['player_ids'] = $players_ids;
            $conversation->friend_name = $user->id == $conversation->friend_id ? $conversation->friend->full_name : $conversation->user->full_name;
            $conversation->friend_image = $user->id == $conversation->friend_id ? $conversation->friend->photo : $conversation->user->photo;
            $info['data'] = ['conversation' => $conversation,'type' => 'chat'];
            $result = parent::oneSignal($info);

        }

        return response()->json($message);
    }
}
