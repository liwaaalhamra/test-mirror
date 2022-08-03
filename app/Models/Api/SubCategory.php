<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\SubCategory
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $item_order
 * @property string $name_en
 * @property string $name_ar
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\SubCategory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\SubCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\SubCategory whereItemOrder($value)
 * @mixin \Eloquent
 */
class SubCategory extends Model
{
    protected $table = 'sub_categories';
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at','item_order','category_id','name_en','name_ar','is_published','subtitle_en','subtitle_ar','category_type'];
    protected $appends = ['name'];
    protected $with = ['sub_sub_categories'];

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
        return $this->hasMany(Item::class,'sub_category_id','id')->whereIsAvailable(1)->orderby('item_order','asc');
    }

    public function sub_sub_categories(){
        return $this->hasMany(SubSubCategory::class,'sub_category_id','id')->whereIsPublished(1)->orderByRaw("LOWER(name_en) asc");
    }

    public function category(){
        return $this->belongsTo(Category::class,'category_id');
    }

}
