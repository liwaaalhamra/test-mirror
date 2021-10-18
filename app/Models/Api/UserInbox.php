<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\Api\UserInbox
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $user_id
 * @property integer $inbox_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UserInbox whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UserInbox whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UserInbox whereInboxId($value)
 */
class UserInbox extends Model
{
    protected $table = "user_inbox";
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(Users::class,'user_id');
    }
    public function inbox(){
        return $this->belongsTo(Inbox::class,'inbox_id');
    }



}
