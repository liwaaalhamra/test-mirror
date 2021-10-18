<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\AboutUs
 *
 * @property integer $id
 * @property string $message
 * @property string $description
 * @property string $website
 * @property string $image
 * @property string $instagram
 * @property string $facebook
 * @property string $twitter
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\AboutUs whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\AboutUs whereImage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\AboutUs whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\AboutUs whereTwitter($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\AboutUs whereInstagram($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\AboutUs whereWebsite($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\AboutUs whereFacebook($value)
 * @mixin \Eloquent
 */
class AboutUs extends Model
{
    protected $table = 'about_us';
    protected $guarded = [];
    protected $hidden = ['updated_at','description_en','description_ar'];
    protected $appends = ['description'];

    public function getDescriptionAttribute(){
        $locale = App::getLocale();
        return $this->attributes['description_'.$locale];
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function getTwitterAttribute($value){
        return empty($value) ? null : $value;
    }
    public function getInstagramAttribute($value){
        return empty($value) ? null : $value;
    }
    public function getEmailAttribute($value){
        return empty($value) ? null : $value;
    }
    public function getFacebookAttribute($value){
        return empty($value) ? null : $value;
    }
    public function getWebsiteAttribute($value){
        return empty($value) ? null : $value;
    }
    public function getMessageAttribute($value){
        return empty($value) ? null : $value;
    }

    public function setTwitterAttribute($value){
        $this->attributes['twitter'] = empty(trim($value)) ? null : $value;
    }
    public function setMessageAttribute($value){
        $this->attributes['message'] = empty(trim($value)) ? null : $value;
    }
    public function setInstagramAttribute($value){
        $this->attributes['instagram'] = empty(trim($value)) ? null : $value;
    }
    public function setEmailAttribute($value){
        $this->attributes['email'] = empty(trim($value)) ? null : $value;
    }
    public function setFacebookAttribute($value){
        $this->attributes['facebook'] = empty(trim($value)) ? null : $value;
    }
    public function setWebsiteAttribute($value){
        $this->attributes['website'] = empty(trim($value)) ? null : $value;
    }
}
