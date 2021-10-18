<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\UserLocation
 *
 * @property integer $id
 * @property integer $users_id
 * @property integer $region_id
 * @property integer $city_id
 * @property integer $country_id
 * @property integer $is_default
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UserLocation whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UserLocation whereCityId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UserLocation whereRegionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UserLocation whereCountryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UserLocation whereIsDefault($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UserLocation whereUsersId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\UserLocation whereTag($value)
 * @mixin \Eloquent
 */
class UserLocation extends Model
{
    use SoftDeletes;
    protected $table = 'user_locations';
    protected $guarded = [];
    protected $hidden = ['updated_at','users_id','country_id','region_id','city_id',
        'raw_address','location_type','block','apartment','area','country_code','mobile_number','deleted_at'];
    protected $casts = [
        'is_default' => 'boolean'
    ];
    protected $with = ['city','region'];
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    /**
     * .
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user(){
        return $this->belongsTo(Users::class, 'users_id', 'id');
    }

    public function region(){
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }

    public function city(){
        return $this->belongsTo(Area::class, 'city_id', 'id');
    }

    public function country(){
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
