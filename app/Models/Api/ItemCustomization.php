<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\ItemCustomization
 *
 * @property integer $id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\ItemCustomization whereId($value)
 * @mixin \Eloquent
 */
class ItemCustomization extends Model
{
    protected $table = 'item_customizations';
    protected $guarded = [];
    protected $hidden = ['updated_at','name_en','name_ar','description_en','description_ar','item_id','is_published','item_order'];
    protected $appends = ['name','description'];
    protected $with = ['options'];

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

    public function item(){
        return $this->belongsTo(Item::class,'item_id');
    }

    public function options(){
        return $this->hasMany(CustomizationOption::class,'customization_id','id')->whereIsPublished(1)->orderby('item_order');
    }
}
