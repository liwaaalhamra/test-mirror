<?php

namespace App\Models\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Api\RestaurantTiming
 *
 * @property integer $id
 * @property integer $item_id
 * @property integer $day
 * @property string $type
 * @property \Carbon\Carbon $time_from
 * @property \Carbon\Carbon $time_to
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\RestaurantTiming whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\RestaurantTiming whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\RestaurantTiming whereItemId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\RestaurantTiming whereDay($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\RestaurantTiming whereTimeFrom($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\RestaurantTiming whereTimeTo($value)
 * @mixin \Eloquent
 */
class RestaurantTiming extends Model
{
    protected $table = "restaurant_timing";

    protected $guarded = [];
	
    protected $hidden  = ['created_at', 'updated_at','item_id','type'];
    protected $appends = ['day_name'];

    public function getDayNameAttribute(){
        return $this->attributes['day'] ? Controller::getDayName($this->attributes['day']) : 'n/a';
    }

    public function getCreatedAtAttribute($value) {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->timestamp;
    }
    public function getTimeFromAttribute($value) {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->timestamp;
    }
    public function getTimeToAttribute($value) {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->timestamp;
    }

    public function setTimeFromAttribute($item_datetime)
    {
        $this->attributes['time_from'] = $item_datetime == null ? null : Carbon::createFromTimestamp($item_datetime)->format('Y-m-d H:i:s');
    }

    public function setTimeToAttribute($item_datetime)
    {
        $this->attributes['time_to'] = $item_datetime == null ? null : Carbon::createFromTimestamp($item_datetime)->format('Y-m-d H:i:s');
    }

    public function item(){
        return $this->belongsTo(Restaurant::class,'item_id');
    }
}
