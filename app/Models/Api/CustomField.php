<?php

namespace App\Models\Api;

use App;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Api\CustomField
 *
 * @mixin \Eloquent
 * @property integer $id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\CustomField whereId($value)
 */

class CustomField extends Model
{
    protected $table = "custom_fields";
    protected $guarded = [];
    protected $hidden = ['updated_at','category_id','sub_category_id','sub_sub_category_id','in_return','in_filters'];
    protected $casts = [
        'has_extra_field' => 'boolean',
    ];

    public function getCreatedAtAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function sub_category(){
        return $this->belongsTo(SubCategory::class,'sub_category_id');
    }
    public function sub_sub_category(){
        return $this->belongsTo(SubSubCategory::class,'sub_sub_category_id');
    }

    public function category(){
        return $this->belongsTo(Category::class,'category_id');
    }

    public function location(){
        return $this->belongsTo(Location::class,'location_id');
    }

}
