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
    protected $hidden = ['updated_at','deleted_at','pivot','item_order','meta_name','is_available','location_id','sub_region_id',
        'name_en','name_ar','description_en','description_ar','pdf',
        'share_link','country_id','latitude','longitude','address','country_code','phone_number','user','image','width','height'
    ];
    protected $casts = [
        'is_available' => 'boolean',
        'is_sale' => 'boolean',
    ];
    protected $with = ['location','images', 'sub_region'];
    protected $appends = ['name','user_fullname','description','discount_amount','currency',
        'price_formatted','discounted_price_formatted','discount_amount_formatted','barcode','user_image','price_from_formatted','price_to_formatted'];

    protected function getCurrencyAttribute(){
        return env('APP_CURRENCY');
    }

    protected function getUserFullnameAttribute(){
        return isset($this->user) ? $this->user->full_name : null;
    }

    protected function getUserImageAttribute(){
        return isset($this->user) ? $this->user->photo : null;
    }

    protected function getPriceFormattedAttribute(){
        return $this->attributes['price'] ? number_format($this->attributes['price'],0) . ' ' . env('APP_CURRENCY') : null;
    }
    protected function getPriceFromFormattedAttribute(){
        return $this->attributes['price_from'] ? number_format($this->attributes['price_from'],0) . ' ' . env('APP_CURRENCY') : null;
    }
    protected function getPriceToFormattedAttribute(){
        return $this->attributes['price_to'] ? number_format($this->attributes['price_to'],0) . ' ' . env('APP_CURRENCY') : null;
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

    public function getExpiredAtAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function setExpiredAtAttribute($item_datetime)
    {
        $this->attributes['expired_at'] = $item_datetime == null ? null : Carbon::createFromTimestamp($item_datetime)->format('Y-m-d H:i:s');
    }

    public function getDeliveryTimeAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function user(){
        return $this->belongsTo(Users::class,'user_id');
    }

    public function return_post(){
        return $this->belongsTo(Item::class,'return_id');
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

    public function sub_region(){
        return $this->belongsTo(SubRegion::class,'sub_region_id');
    }

    public function custom_fields(){
        return $this->hasMany(ItemCustomField::class,'item_id','id');
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

    public function getPdfAttribute($value){
        return ($value == null) ? null : secure_url('public/uploads/documents/items/'.$value);
    }

    public function getPdfName(){
        return $this->attributes['pdf'];
    }

    public function images(){
        return $this->hasMany(Media::class,'meta_id','id')->where('meta_name','items')->where('media_type','images');
    }

    public function getBarcodeAttribute()
    {
        return secure_url('barcode.php?f=png&s=code-128&sf=1&sy=0.5&sx=2&ts=4&th=20&p=20&pt=2&pr=0&pl=0&d='.$this->attributes['code']);
    }

    public function getBarcodeValue(){
        return $this->attributes['code'];
    }

}
