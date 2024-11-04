<?php

namespace App\Models\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\Inbox
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $is_read
 * @property string $user_type
 * @property string $code
 * @property string $type
 * @property string $image
 * @property string $title
 * @property string $message
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Inbox whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Inbox whereUserType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Inbox whereIsRead($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Inbox whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Inbox whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Inbox whereType($value)
 * @mixin \Eloquent
 */
class Inbox extends Model
{
    protected $table = 'push_inbox';
    protected $guarded = [];
    protected $hidden = ['updated_at','code','type','user_id','user_type'];
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function push_read(){
        return $this->hasMany(UserInbox::class,'inbox_id','id');
    }

    public function user(){
        if($this->attributes['user_type'] == 'restaurant'){
            return $this->belongsTo(Restaurant::class,'user_id');
        }else{
            return $this->belongsTo(Users::class,'user_id');
        }
    }

    public function getImageAttribute($value){
        return ($value == null) ? null : secure_url('uploads/images/push/'.$value);
    }

    public function getImageName(){
        return $this->attributes['image'];
    }
}
