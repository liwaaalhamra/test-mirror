<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\Category
 *
 * @property integer $id
 * @property integer $item_order
 * @property string $name_en
 * @property string $name_ar
 * @property string $image
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Category whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Category whereItemOrder($value)
 * @mixin \Eloquent
 */
class Category extends Model
{
    protected $table = 'categories';
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at','item_order','name_en','name_ar','type','is_published'];
    protected $appends = ['name'];

    public function getNameAttribute(){
        $locale = App::getLocale();
        return $this->attributes['name_'.$locale];
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function getImageAttribute($value){
        return ($value == null) ? null : secure_url('public/uploads/images/categories/'.$value);
    }

    public function getMediaName(){
        return $this->attributes['image'];
    }

    public function sub_categories(){
        return $this->hasMany(SubCategory::class,'category_id','id');
    }

    public function items(){
        return $this->hasMany(Item::class,'category_id','id')->whereIsAvailable(1)->orderby('item_order','asc');
    }
}
