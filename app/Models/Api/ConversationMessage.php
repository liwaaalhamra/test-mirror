<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\Api\ConversationMessage
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $conversation_id
 * @property integer $sender_id
 * @property integer $receiver_id
 * @property integer $is_my_message
 * @property string $message
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\ConversationMessage whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\ConversationMessage whereConversationId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\ConversationMessage whereSenderId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\ConversationMessage whereReceiverId($value)
 */
class ConversationMessage extends Model
{
    protected $table = "conversation_messages";
    protected $guarded = [];
    protected $hidden = ['conversation_id','updated_at'];
    protected $casts = [
        'is_my_message' => 'boolean',
        'is_read' => 'boolean',
    ];
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function sender(){
        return $this->belongsTo(Users::class,'sender_id');
    }
    public function receiver(){
        return $this->belongsTo(Users::class,'receiver_id');
    }

    public function conversation(){
        return $this->belongsTo(Conversation::class,'conversation_id');
    }

    public function getIsReadAttribute($attr)
    {
        $access_token = request()->header("token");
    
        $user_access = UsersAccess::where("access_token", $access_token)->first();
        if (!$user_access) {
            return $attr;
        }
    
        $user = Users::find($user_access->user_id);
        if (!$user) {
            return $attr;
        }
    
        if ($this->getAttribute('sender_id') == $user->id) {
            return true;
        }

        $result = $attr == 0 ? false : true;
    
        return $result;
    }
}
