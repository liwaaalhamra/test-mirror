<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\Api\UserFavorite
 *
 * @mixin \Eloquent
 */
class UserFavorite extends Model
{
    protected $table = "user_favorites";
    protected $guarded = [];
    protected $hidden = ['meta_id','user_id','updated_at'];

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function user(){
        return $this->belongsTo(Users::class,'user_id');
    }

}
