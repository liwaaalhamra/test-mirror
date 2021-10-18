<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\SupplierSubCategory
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $item_order
 * @property string $name_en
 * @property string $name_ar
 * @property string $description_en
 * @property string $description_ar
 * @property string $image
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\SupplierSubCategory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\SupplierSubCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\SupplierSubCategory whereItemOrder($value)
 * @mixin \Eloquent
 */
class SupplierSubCategory extends Model
{
    use SoftDeletes;
    protected $table = 'supplier_sub_categories';
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at','item_order','is_published','target',
        'category_id','name_en','name_ar','description_en','description_ar','deleted_at'];
    protected $appends = ['name','description'];
//    protected $with = ['images'];

    public function getNameAttribute(){
        $locale = App::getLocale();
        return $this->attributes['name_'.$locale];
    }

    public function getDescriptionAttribute(){
        $locale = App::getLocale();
        return $this->attributes['description_'.$locale];
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function items(){
        return $this->hasMany(SupplierFoodGroup::class,'category_id','id')->whereIsPublished(1)->orderby('item_order');
    }


//    public function images(){
//        return $this->hasMany(Media::class,'meta_id','id')->where('meta_name','supplier_sub_categories')->where('media_type','images');
//    }

    public function category(){
        return $this->belongsTo(CuratedCollection::class,'category_id');
    }

}
