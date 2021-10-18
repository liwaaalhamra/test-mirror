<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Api\PackageItemOption
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property \Carbon\Carbon $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */

class PackageItemOption extends Model
{
    protected $table = "package_item_options";
    protected $guarded = [];
    protected $hidden = ['updated_at','package_item_id','customization_option_id'];

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function package_item(){
        return $this->belongsTo(PackageItem::class,'package_item_id')->withTrashed();
    }

    public function customization_option(){
        return $this->belongsTo(CustomizationOption::class,'customization_option_id');
    }
}
