<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Api\UserPostViewed
 * @mixin \Eloquent
 */
class UserPostViewed extends Model
{
    protected $table = "user_posts_viewed";
    protected $guarded = [];
    protected $hidden = ['updated_at'];

    public function user(){
        return $this->belongsTo(Users::class, 'users_id', 'id');
    }
    public function item(){
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }
}
