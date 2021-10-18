<?php

namespace App\Models\Api;

use App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\SupplierFoodGroup
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $item_order
 * @property string $name_en
 * @property string $name_ar
 * @property string $description_en
 * @property string $description_ar
 * @property string $image
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\SupplierFoodGroup whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\SupplierFoodGroup whereCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\SupplierFoodGroup whereItemOrder($value)
 * @mixin \Eloquent
 */
class SupplierFoodGroup extends Model
{
    use SoftDeletes;
    protected $table = 'supplier_food_groups';
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at','item_order','is_published','target',
        'category_id','name_en','name_ar','description_en','description_ar','deleted_at','username','password','expired_at','access_token','reset_at'];
    protected $appends = ['name','description'];
    protected $casts = [
        'is_closed' => 'boolean'
    ];

    public function getNameAttribute(){
        $locale = App::getLocale();
        return $this->attributes['name_'.$locale];
    }

    public function country(){
        return $this->belongsTo(Country::class,'country_id');
    }

    public function getExpiredAtAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value);
    }

    public function getResetAtAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function setResetAtAttribute($item_datetime)
    {
        $this->attributes['reset_at'] = $item_datetime == null ? null : Carbon::createFromTimestamp($item_datetime)->format('Y-m-d H:i:s');
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
        return $this->hasMany(Item::class,'supplier_food_group_id','id')->whereIsAvailable(1)->orderby('item_order');
    }

    public function category(){
        return $this->belongsTo(SupplierSubCategory::class,'category_id');
    }

    public function getImageAttribute($value){
        return ($value == null) ? null : secure_url('public/uploads/images/food_groups/'.$value);
    }

    public function getMediaName(){
        return $this->attributes['image'];
    }

    public function getPdfAttribute($value){
        return ($value == null) ? null : secure_url('public/uploads/documents/food_groups/'.$value);
    }

    public function getPdfName(){
        return $this->attributes['pdf'];
    }

    public function images(){
        return $this->hasMany(Media::class,'meta_id','id')->where('meta_name','food_groups')->where('media_type','images');
    }

}
