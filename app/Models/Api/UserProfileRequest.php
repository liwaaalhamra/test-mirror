<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Api\UserProfileRequest
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 * @property string $department
 * @property string $primary_phone
 * @property string $secondary_phone
 * @property string $gender
 * @property string $dob
 * @property string $address
 * @property string $street
 * @property string $building
 * @property string $floor
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UserProfileRequest whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UserProfileRequest whereUserId($value)
 * @mixin \Eloquent
 */
class UserProfileRequest extends Model
{
    protected $table = "profile_requests";
    protected $guarded = [];
    protected $hidden = ['updated_at'];

    public function user(){
        return $this->belongsTo(Users::class, 'users_id', 'id');
    }
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }
}
