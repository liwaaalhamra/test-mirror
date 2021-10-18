<?php

namespace App\Models\Api;

use App;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Api\Item
 *
 * @mixin \Eloquent
 * @property integer $id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Item whereId($value)
 */

class Item extends Model
{
    use SoftDeletes;

    protected $table = "items";
    protected $guarded = [];
    protected $hidden = ['updated_at','deleted_at','pivot','item_order','meta_name','is_available',
        'is_gift','sub_category_id','name_en','name_ar','description_en','description_ar',
        'category_id','brand_id','product_type_id','item_size_id','supplier_food_group_id'];
    protected $casts = [
        'is_available' => 'boolean',
    ];
    protected $appends = ['name','description','discount_amount','currency','price_formatted','discounted_price_formatted','discount_amount_formatted'];

    protected $with = ['customizations'];

    protected function getCurrencyAttribute(){
        return env('APP_CURRENCY');
    }

    protected function getPriceFormattedAttribute(){
        return number_format($this->attributes['price'],0) . ' ' . env('APP_CURRENCY');
    }

    protected function getDiscountedPriceFormattedAttribute(){
        $discounted_price = $this->attributes['discounted_price'];
        return $discounted_price ? number_format($discounted_price,0) . ' ' . env('APP_CURRENCY') : null ;
    }

    protected function getDiscountAmountFormattedAttribute(){
        $discount_amount = $this->attributes['discounted_price'] ? $this->attributes['price'] - $this->attributes['discounted_price'] : null;
        return $discount_amount ? '-'.number_format($discount_amount,0) . ' ' . env('APP_CURRENCY') : null ;
    }

    public function getNameAttribute(){
        $locale = App::getLocale();
        return $this->attributes['name_'.$locale];
    }

    public function getDiscountAmountAttribute(){
        return $this->attributes['discounted_price'] ? $this->attributes['price'] - $this->attributes['discounted_price'] : null;
    }

    public function getDescriptionAttribute(){
        $locale = App::getLocale();
        return $this->attributes['description_'.$locale];
    }

    public function getCreatedAtAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function getExpiryDateAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function setExpiryDateAttribute($item_datetime)
    {
        $this->attributes['expiry_date'] = $item_datetime == null ? null : Carbon::createFromTimestamp($item_datetime)->format('Y-m-d H:i:s');
    }

    public function sub_category(){
        return $this->belongsTo(SubCategory::class,'sub_category_id');
    }

    public function supplier_food_group(){
        return $this->belongsTo(SupplierFoodGroup::class,'supplier_food_group_id');
    }

    public function restaurant(){
        return $this->belongsTo(Restaurant::class,'category_id');
    }

    public function customizations(){
        return $this->hasMany(ItemCustomization::class,'item_id','id')->whereIsPublished(1)->orderby('item_order');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model)
        {
            $model->generateCode();
        });
    }

    public function generateCode(){
        $this->attributes['code'] = Controller::number_generator(6);
    }

    public function getImageAttribute($value){
       return ($value == null) ? null : secure_url('public/uploads/images/items/'.$value);
    }

    public function getMediaName(){
        return $this->attributes['image'];
    }

}
