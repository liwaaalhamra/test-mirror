<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\TipsCategory
 *
 * @property integer $id
 * @property integer $item_order
 * @property string $name
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\TipsCategory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\TipsCategory whereItemOrder($value)
 * @mixin \Eloquent
 */
class TipsCategory extends Model
{
    protected $table = 'tips_categories';
    protected $guarded = [];
    protected $hidden = ['updated_at','item_order'];
    protected $with = ['items'];
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function items(){
        return $this->hasMany(TipsItem::class,'category_id','id')->orderby('item_order','asc');
    }
}
