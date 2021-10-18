<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\Merchant
 *
 * @property integer $id
 * @property string $description
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Merchant whereId($value)
 * @mixin \Eloquent
 */
class Merchant extends Model
{
    protected $table = 'merchants';
    protected $guarded = [];
    protected $hidden = ['updated_at'];
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }
}
