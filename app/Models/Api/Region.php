<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\Region
 *
 * @property integer $id
 * @property integer $country_id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Region whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Region whereCountryId($value)
 * @mixin \Eloquent
 */
class Region extends Model
{
    protected $table = 'regions';
    protected $guarded = [];
    protected $hidden = ['updated_at','region_id','country_id','name_en','name_ar'];
    protected $appends = ['name'];

    public function getNameAttribute(){
        $locale = App::getLocale();
        return $this->attributes['name_'.$locale];
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function country(){
        return $this->belongsTo(Country::class,'country_id');
    }
    public function areas(){
        return $this->hasMany(Area::class,'region_id','id');
    }
}
