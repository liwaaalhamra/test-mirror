<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\CustomizationOption
 *
 * @property integer $id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\CustomizationOption whereId($value)
 * @mixin \Eloquent
 */
class CustomizationOption extends Model
{
    protected $table = 'customization_options';
    protected $guarded = [];
    protected $hidden = ['updated_at','name_en','name_ar','customization_id','is_published','item_order','pivot'];
    protected $appends = ['name','currency','price_formatted'];

    public function getCurrencyAttribute(){
        return env('APP_CURRENCY');
    }

    protected function getPriceFormattedAttribute(){
        return number_format($this->attributes['price'],0) . ' ' . env('APP_CURRENCY');
    }

    public function getNameAttribute(){
        $locale = App::getLocale();
        return $this->attributes['name_'.$locale];
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function customization(){
        return $this->belongsTo(ItemCustomization::class,'customization_id');
    }
}
