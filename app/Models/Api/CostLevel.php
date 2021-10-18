<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\CostLevel
 *
 * @property integer $id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\CostLevel whereId($value)
 * @mixin \Eloquent
 */
class CostLevel extends Model
{
    protected $table = 'cost_levels';
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at','id'];


    public function getCreatedAtAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }
}
