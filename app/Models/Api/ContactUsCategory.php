<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\ContactUsCategory
 *
 * @property integer $id
 * @property string $name
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\ContactUsCategory whereId($value)
 * @mixin \Eloquent
 */
class ContactUsCategory extends Model
{
    protected $table = 'contact_us_categories';
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
