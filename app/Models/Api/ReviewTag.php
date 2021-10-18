<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\ReviewTag
 *
 * @property integer $id
 * @property string $name_en
 * @property string $name_ar
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\ReviewTag whereId($value)
 * @mixin \Eloquent
 */
class ReviewTag extends Model
{
    protected $table = 'review_tags_list';
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at','name_en','name_ar','is_published','item_order','type'];
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
