<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\ProductType
 *
 * @property integer $id
 * @property string $name_en
 * @property string $name_ar
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\ProductType whereId($value)
 * @mixin \Eloquent
 */
class ProductType extends Model
{
    protected $table = 'product_types';
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at','name_en','name_ar'];

    protected $appends = ['name'];

    public function getNameAttribute(){
        $locale = App::getLocale();
        return $this->attributes['name_'.$locale];
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }
}
