<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Api\UserCard
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $user_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UsersAccess whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UsersAccess whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UsersAccess whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UsersAccess whereUpdatedAt($value)
 */
class UserCard extends Model
{
	protected $table = 'app_user_cards';

	protected $guarded = [];

	protected $hidden = ["created_at", "updated_at"];

    public function user(){
        return $this->belongsTo(Users::class,'user_id');
    }
}
