<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\FuelType
 * @mixin \Eloquent
 */
class FuelType extends Model
{
    protected $table = 'fuel_types';
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at','name_en','name_ar'];
    protected $appends = ['name'];

    public function getNameAttribute(){
        $locale = App::getLocale();
        return $this->attributes['name_'.$locale];
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }
}
