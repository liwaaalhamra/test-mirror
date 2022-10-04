<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\CustomFieldList
 * @mixin \Eloquent
 */
class SubRegion extends Model
{
    protected $table = 'sub_regions';
    protected $guarded = [];
    protected $hidden = ['updated_at','name_en','name_ar','city_id'];
    protected $appends = ['name'];
    protected $with = ['city'];

    public function getNameAttribute(){
        $locale = App::getLocale();
        return $this->attributes['name_'.$locale];
    }

    public function city(){
        return $this->belongsTo(Location::class,'city_id');
    }

    public function getCreatedAtAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }
}
