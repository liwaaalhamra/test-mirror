<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\CollectionCategory
 *
 * @property integer $id
 * @property integer $is_supplier
 * @property integer $item_order
 * @property string $name_en
 * @property string $name_ar
 * @property string $target
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\CollectionCategory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\CollectionCategory whereItemOrder($value)
 * @mixin \Eloquent
 */
class CollectionCategory extends Model
{
    use SoftDeletes;
    protected $table = 'collection_categories';
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at','item_order','name_en','name_ar','is_published','deleted_at','target'];
    protected $appends = ['name'];
    protected $casts = [
        'is_supplier' => 'boolean'
    ];

    public function getNameAttribute(){
        $locale = App::getLocale();
        return $this->attributes['name_'.$locale];
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function getImageAttribute($value){
        return ($value == null) ? null : secure_url('uploads/images/categories/'.$value);
    }

    public function getMediaName(){
        return $this->attributes['image'];
    }


    public function collections(){
        return $this->hasMany(CuratedCollection::class,'category_id','id')->whereIsPublished(1)->orderby('item_order');
    }
}
