<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\RestaurantEventType
 *
 * @property integer $id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\RestaurantEventType whereId($value)
 * @mixin \Eloquent
 */
class RestaurantEventType extends Model
{
    protected $table = 'restaurant_event_types';
    protected $guarded = [];
    protected $hidden = ['updated_at','name_en','name_ar','is_published','item_order'];
    protected $appends = ['name'];

    public function getNameAttribute(){
        $locale = App::getLocale();
        return $this->attributes['name_'.$locale];
    }


    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function item(){
        return $this->belongsTo(Restaurant::class,'item_id');
    }
}
