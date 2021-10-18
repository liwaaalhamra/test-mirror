<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\CuratedCollection
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $item_order
 * @property string $name_en
 * @property string $name_ar
 * @property string $description_en
 * @property string $description_ar
 * @property string $image
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\SubCategory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\SubCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\SubCategory whereItemOrder($value)
 * @mixin \Eloquent
 */
class CuratedCollection extends Model
{
    use SoftDeletes;
    protected $table = 'curated_collections';
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at','item_order','is_published','target',
        'category_id','name_en','name_ar','description_en','description_ar','deleted_at','is_featured'];
    protected $appends = ['name','description'];

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
        return $this->hasMany(Restaurant::class,'sub_category_id','id')->whereIsAvailable(1)->orderby('item_order');
    }

    public function supplier_sub_categories(){
        return $this->hasMany(SupplierSubCategory::class,'category_id','id')->whereIsPublished(1)->orderby('item_order');
    }

    public function category(){
        return $this->belongsTo(CollectionCategory::class,'category_id');
    }

    public function getImageAttribute($value){
        return ($value == null) ? null : secure_url('public/uploads/images/categories/'.$value);
    }

    public function getMediaName(){
        return $this->attributes['image'];
    }
}
