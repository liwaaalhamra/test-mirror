<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\Api\CmsUserCountry
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $user_id
 * @property integer $country_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\CmsUserCountry whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\CmsUserCountry whereCountryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\CmsUserCountry whereUserId($value)
 */

class CmsUserCountry extends Model
{
    protected $table = "cms_user_countries";
    protected $fillable = ['user_id','country_id'];

    protected $with = ['countries'];
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

}
