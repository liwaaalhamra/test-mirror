<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\RestaurantService
 *
 * @property integer $id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\RestaurantService whereId($value)
 * @mixin \Eloquent
 */
class RestaurantService extends Model
{
    protected $table = 'restaurant_services';
    protected $guarded = [];
    protected $hidden = ['updated_at'];
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function item(){
        return $this->belongsTo(Restaurant::class,'item_id');
    }
}
