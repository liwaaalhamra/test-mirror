<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\Setting
 *
 * @property integer $id
 * @property string $meta_name
 * @property string $meta_value
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Setting whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Setting whereMetaName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Setting whereMetaValue($value)

 * @mixin \Eloquent
 */
class Setting extends Model
{
    protected $table = 'settings';
    protected $guarded = [];
    protected $hidden = ['updated_at'];
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }
}
