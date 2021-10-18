<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\ProfileType
 *
 * @property integer $id
 * @property string $name
 * @property string $start_color
 * @property string $end_color
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\ProfileType whereId($value)
 * @mixin \Eloquent
 */
class ProfileType extends Model
{
    protected $table = 'profile_types';
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at'];

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }
}
