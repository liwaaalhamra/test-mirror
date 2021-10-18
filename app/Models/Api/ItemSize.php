<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\ItemSize
 *
 * @property integer $id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\ItemSize whereId($value)
 * @mixin \Eloquent
 */
class ItemSize extends Model
{
    protected $table = 'item_sizes';
    protected $guarded = [];
    protected $hidden = ['updated_at'];
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }
}
