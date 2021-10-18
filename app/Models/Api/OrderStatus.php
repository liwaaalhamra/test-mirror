<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\OrderStatus
 *
 * @property integer $id
 * @property string $label
 * @property string $status
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\OrderStatus whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\OrderStatus whereStatus($value)
 * @mixin \Eloquent
 */
class OrderStatus extends Model
{
    protected $table = 'order_status';
    protected $guarded = [];
    protected $hidden = ['updated_at','is_published','item_order'];
    protected $appends = ['is_selected'];

    protected function getIsSelectedAttribute(){
        return false;
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }
}
