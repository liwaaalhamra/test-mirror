<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\Api\UserFavorite
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $user_id
 * @property integer $meta_id
 * @property string $meta_name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UserFavorite whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UserFavorite whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UserFavorite whereMetaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UserFavorite whereMetaName($value)
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
