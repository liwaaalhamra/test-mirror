<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\SubSubCategory
 * @mixin \Eloquent
 */
class SubSubCategory extends Model
{
    protected $table = 'sub_sub_categories';
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at','item_order','sub_category_id','name_en','name_ar','is_published'];
    protected $appends = ['name'];

    public function getNameAttribute(){
        $locale = App::getLocale();
        return $this->attributes['name_'.$locale];
    }

//    public function getSubtitleAttribute(){
//        $locale = App::getLocale();
//        return $this->attributes['subtitle_'.$locale];
//    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function items(){
        return $this->hasMany(Item::class,'sub_sub_category_id','id')->whereIsAvailable(1)->orderby('item_order','asc');
    }

    public function sub_category(){
        return $this->belongsTo(SubCategory::class,'sub_category_id');
    }

}
