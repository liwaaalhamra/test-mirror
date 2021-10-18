<?php

namespace App\Models\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Api\Coupon
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $is_published
 * @property string $code
 * @property integer $max_uses
 * @property string $type
 * @property double $amount
 * @property \Carbon\Carbon $start_date
 * @property \Carbon\Carbon $end_date
 * @property \Carbon\Carbon $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Coupon whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Coupon whereIsPublished($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Coupon whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Coupon whereCode($value)
 */

class Coupon extends Model
{
    use SoftDeletes;

    protected $table = "promo_codes";
    protected $guarded = [];
    protected $hidden = ['updated_at','deleted_at'];

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function getStartDateAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function setStartDateAttribute($item_datetime)
    {
        $this->attributes['start_date'] = $item_datetime == null ? null : Carbon::createFromTimestamp($item_datetime)->format('Y-m-d H:i:s');
    }

    public function getEndDateAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function setEndDateAttribute($item_datetime)
    {
        $this->attributes['end_date'] = $item_datetime == null ? null : Carbon::createFromTimestamp($item_datetime)->format('Y-m-d H:i:s');
    }

}
