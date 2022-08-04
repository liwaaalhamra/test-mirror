<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\CustomFieldList
 * @mixin \Eloquent
 */
class Location extends Model
{
    protected $table = 'locations';
    protected $guarded = [];
    protected $hidden = ['updated_at','name_en','name_ar','country_id'];
    protected $appends = ['name'];
    protected $with = ['country'];

    public function getNameAttribute(){
        $locale = App::getLocale();
        return $this->attributes['name_'.$locale];
    }

    public function country(){
        return $this->belongsTo(Country::class,'country_id');
    }

    public function getCreatedAtAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }
}
