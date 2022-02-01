<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\Api\Conversation
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $user_id
 * @property integer $friend_id
 * @property integer $unread_messages
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Conversation whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Conversation whereFriendId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Conversation whereUserId($value)
 */
class Conversation extends Model
{
    protected $table = "conversations";
    protected $guarded = [];
    protected $hidden = ['updated_at'];
    protected $appends = [/*'friend_name','friend_image',*/'last_message','last_message_datetime'];

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function user(){
        return $this->belongsTo(Users::class,'user_id');
    }
    public function friend(){
        return $this->belongsTo(Users::class,'friend_id');
    }

//    public function getFriendNameAttribute(){
//        return $this->belongsTo(Users::class,'friend_id')->first()->full_name;
//    }
//    public function getFriendImageAttribute(){
//        return $this->belongsTo(Users::class,'friend_id')->first()->photo;
//    }
    public function getLastMessageAttribute(){
        $last_message = $this->hasMany(ConversationMessage::class,'conversation_id','id')->orderby('id','desc')->first();
        if($last_message){
            return $last_message->message;
        }
        return null;
    }
    public function getLastMessageDatetimeAttribute(){
        $last_message = $this->hasMany(ConversationMessage::class,'conversation_id','id')->orderby('id','desc')->first();
        if($last_message){
            return $last_message->created_at;
        }
        return null;
    }
    public function messages(){
        return $this->hasMany(ConversationMessage::class,'conversation_id','id');
    }

}
