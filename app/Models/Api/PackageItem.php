<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Api\PackageItem
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property \Carbon\Carbon $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */

class PackageItem extends Model
{
    use SoftDeletes;

    protected $table = "package_items";
    protected $guarded = [];
    protected $hidden = ['updated_at','deleted_at','item_id','order_id','user_id','item_size_id','restaurant_id','category_id','sub_category_id','supplier_food_group_id'];
    protected $with = ['selected_options','item'];
    protected $appends = ['currency','price_formatted','discounted_price_formatted','discount_amount_formatted','customizations_price_formatted','final_price','final_price_formatted'];

    protected function getCurrencyAttribute(){
        return env('APP_CURRENCY');
    }

    protected function getPriceFormattedAttribute(){
        return number_format($this->attributes['price'],0) . ' ' . env('APP_CURRENCY');
    }

    protected function getFinalPriceAttribute(){
        $price = $this->attributes['discounted_price'] ? $this->attributes['discounted_price'] : $this->attributes['price'];
        $price = $this->attributes['customizations_price'] ? $price + $this->attributes['customizations_price'] : $price;
        $price = $price * $this->attributes['quantity'];
        return  $price;
    }

    protected function getFinalPriceFormattedAttribute(){
        $price = $this->attributes['discounted_price'] ? $this->attributes['discounted_price'] : $this->attributes['price'];
        $price = $this->attributes['customizations_price'] ? $price + $this->attributes['customizations_price'] : $price;
        $price = $price * $this->attributes['quantity'];

        return number_format($price,0) . ' ' . env('APP_CURRENCY');
    }

    protected function getDiscountedPriceFormattedAttribute(){
        $discounted_price = $this->attributes['discounted_price'];
        return $discounted_price ? number_format($discounted_price,0) . ' ' . env('APP_CURRENCY') : null ;
    }

    protected function getCustomizationsPriceFormattedAttribute(){
        $customizations_price = $this->attributes['customizations_price'];
        return $customizations_price ? number_format($customizations_price,0) . ' ' . env('APP_CURRENCY') : null ;
    }

    protected function getDiscountAmountFormattedAttribute(){
        $discount_amount = $this->attributes['discounted_price'] ? $this->attributes['price'] - $this->attributes['discounted_price'] : null;
        return $discount_amount ? '-'.number_format($discount_amount,0) . ' ' . env('APP_CURRENCY') : null ;
    }


    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function order(){
        return $this->belongsTo(Order::class,'order_id');
    }

    public function item(){
        return $this->belongsTo(Item::class,'item_id')->withTrashed();
    }

    public function restaurant(){
        return $this->belongsTo(Restaurant::class,'restaurant_id')->withTrashed();
    }

    public function category(){
        return $this->belongsTo(CollectionCategory::class,'category_id')->withTrashed();
    }

    public function sub_category(){
        return $this->belongsTo(CuratedCollection::class,'sub_category_id')->withTrashed();
    }

    public function supplier_food_group(){
        return $this->belongsTo(SupplierFoodGroup::class,'supplier_food_group_id')->withTrashed();
    }

    public function options(){
        return $this->hasMany(PackageItemOption::class,'package_item_id','id');
    }

    public function selected_options(){
        return $this->belongsToMany(CustomizationOption::class,'package_item_options','package_item_id','customization_option_id');
    }

    public function getOptionIdsAttribute(){
        return $this->hasMany(PackageItemOption::class,'package_item_id','id')->orderby('customization_option_id','asc')->pluck('customization_option_id')->toarray();
    }

    public function user(){
        return $this->belongsTo(Users::class,'user_id');
    }
}
