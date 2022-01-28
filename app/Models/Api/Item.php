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
        'name_en','name_ar','description_en','description_ar',
    ];
    protected $casts = [
        'is_available' => 'boolean',
    ];
    protected $appends = ['name','description','discount_amount','currency','price_formatted','discounted_price_formatted','discount_amount_formatted','barcode'];

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

    public function sub_category(){
        return $this->belongsTo(SubCategory::class,'sub_category_id');
    }
    public function sub_sub_category(){
        return $this->belongsTo(SubSubCategory::class,'sub_sub_category_id');
    }

    public function category(){
        return $this->belongsTo(Category::class,'category_id');
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
        $this->attributes['code'] = '113228'.Controller::number_generator(9);
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
