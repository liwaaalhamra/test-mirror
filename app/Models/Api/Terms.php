<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\Terms
 *
 * @property integer $id
 * @property string $description_en
 * @property string $description_ar
 * @property string $privacy_en
 * @property string $privacy_ar
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Terms whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Terms whereDescription($value)
 * @mixin \Eloquent
 */
class Terms extends Model
{
    protected $table = 'terms';
    protected $guarded = [];
    protected $hidden = ['updated_at'];
    protected $appends = ['description','privacy'];
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function getDescriptionAttribute(){
        $locale = App::getLocale();
        return $this->attributes['description_'.$locale];
    }
    public function getPrivacyAttribute(){
        $locale = App::getLocale();
        return $this->attributes['privacy_'.$locale];
    }

    public function getImageAttribute($value){
        return ($value == null) ? null : secure_url('public/uploads/images/terms/'.$value);
    }

    public function getImageName(){
        return $this->attributes['image'];
    }
}
