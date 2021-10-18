<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\TalkToUsCategory
 *
 * @property integer $id
 * @property string $name
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\TalkToUsCategory whereId($value)
 * @mixin \Eloquent
 */
class TalkToUsCategory extends Model
{
    protected $table = 'talk_to_us_categories';
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at'];

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }
}
