<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\Area
 *
 * @property integer $id
 * @property integer $region_id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Area whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Area whereRegionId($value)
 * @mixin \Eloquent
 */
class Area extends Model
{
    protected $table = 'areas';
    protected $guarded = [];
    protected $hidden = ['updated_at','region_id','name_en','name_ar'];
    protected $appends = ['name'];

    public function getNameAttribute(){
        $locale = App::getLocale();
        return $this->attributes['name_'.$locale];
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function region(){
        return $this->belongsTo(Region::class,'region_id');
    }
}
