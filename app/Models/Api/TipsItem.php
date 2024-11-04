<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\TipsItem
 *
 * @property integer $id
 * @property integer $item_order
 * @property integer $category_id
 * @property string $image
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\TipsItem whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\TipsItem whereItemOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\TipsItem whereCategoryId($value)
 * @mixin \Eloquent
 */
class TipsItem extends Model
{
    protected $table = 'tips_items';
    protected $guarded = [];
    protected $hidden = ['updated_at','category_id','item_order'];
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function category(){
        return $this->belongsTo(TipsCategory::class,'category_id');
    }

    public function getImageAttribute($value){
        return ($value == null) ? null : secure_url('uploads/images/tips/'.$value);
    }

    public function getImageName(){
        return $this->attributes['image'];
    }
}
