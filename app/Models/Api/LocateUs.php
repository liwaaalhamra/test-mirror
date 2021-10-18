<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\LocateUs
 *
 * @property integer $id
 * @property integer $in_pickup_list
 * @property string $title
 * @property string $address
 * @property double $latitude
 * @property double $longitude
 * @property string $image
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\LocateUs whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\LocateUs whereInPickupList($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\LocateUs whereLatitude($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\LocateUs whereLongitude($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\LocateUs whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\LocateUs whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\LocateUs whereImage($value)
 * @mixin \Eloquent
 */
class LocateUs extends Model
{
    protected $table = 'locate_us';
    protected $guarded = [];
    protected $hidden = ['updated_at','in_pickup_list'];
    protected $casts = [
        'in_pickup_list' => 'boolean'
    ];
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function scopeClosest($query, $point) {
        if(is_array($point)) {
            $lat = $point['lat'];
            $lng = $point['lng'];
            $unit = isset($point['unit']) && $point['unit'] ? $point['unit'] : 'mi';
            $radius = isset($point['radius']) && $point['radius'] ? $point['radius'] : 5;
            $order = isset($point['order']) && $point['order'] ? $point['order'] : 'asc';

            if($unit == 'km'){
                $unit_query = '/ 1000';
            } elseif($unit == 'mi'){
                $unit_query = '* 0.000621371';
            }
        }

        if(isset($lat) && isset($lng)) {
            $query->selectRaw(
                "*, CONCAT(ROUND(ST_DISTANCE_SPHERE(Point(lat, lng), Point(?, ?)) $unit_query, 2), ' ', ?) as distance",
                [$lat, $lng, $unit]
            )
                ->havingRaw("distance < ?", [$radius])
                ->orderByRaw("distance $order");
        }
    }
}
