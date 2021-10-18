<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\Cuisine
 *
 * @property integer $id
 * @property string $name_en
 * @property string $name_ar
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Cuisine whereId($value)
 * @mixin \Eloquent
 */
class Cuisine extends Model
{
    protected $table = 'cuisines';
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at','name_en','name_ar','is_published','item_order'];
    protected $appends = ['name'];

    public function getNameAttribute(){
        $locale = App::getLocale();
        return $this->attributes['name_'.$locale];
    }

    public function getCreatedAtAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }
}
