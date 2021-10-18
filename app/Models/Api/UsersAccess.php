<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Api\UsersAccess
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $user_id
 * @property string $access_token
 * @property \Carbon\Carbon $expired_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UsersAccess whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UsersAccess whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UsersAccess whereAccessToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UsersAccess whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UsersAccess whereUpdatedAt($value)
 */
class UsersAccess extends Model
{
    protected $table = 'users_access';

    protected $fillable = ['user_id','access_token','expired_at'];

    protected $hidden = ["created_at", "updated_at",'expired_at'];

    public function getExpiredAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value);
    }

    public function user(){
        return $this->belongsTo(Users::class,'user_id');
    }
}
