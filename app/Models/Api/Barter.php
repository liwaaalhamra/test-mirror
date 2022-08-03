<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\Barter
 *
 * @mixin \Eloquent
 */
class Barter extends Model
{
    protected $table = 'barters';
    protected $guarded = [];
    protected $hidden = ['updated_at','sender_id','receiver_id','sender_post_id','receiver_post_id'];
    protected $with = [
        'sender','receiver','sender_post','receiver_post'
    ];

    public function getNameAttribute(){
        $locale = App::getLocale();
        return $this->attributes['name_'.$locale];
    }

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

    public function sender_post(){
        return $this->belongsTo(Item::class,'sender_post_id');
    }

    public function receiver_post(){
        return $this->belongsTo(Item::class,'receiver_post_id');
    }


    public function getStatusAtAttribute($value){
        return json_decode($value,true);
    }

    public function setStatusAtAttribute($value){
        $this->attributes['status_at'] = json_encode($value);
    }
}
