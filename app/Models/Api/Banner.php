<?php

namespace App\Models\Api;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\Banner
 *
 * @property integer $id
 * @property integer $is_published
 * @property integer $banner_order
 * @property string $banner_image
 * @property string $banner_link
 * @property string $target
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Banner whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Banner whereTarget($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Banner whereIsPublished($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Banner whereBannerImage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Banner whereBannerLink($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Banner whereBannerOrder($value)
 * @mixin \Eloquent
 */
class Banner extends Model
{
    protected $table = 'banners';
    protected $guarded = [];
    protected $hidden = ['banner_order','updated_at','target','is_published','caption_en','caption_ar','description_en','description_ar'];
    protected $appends = ['caption','description'];

    public function getCaptionAttribute(){
        $locale = App::getLocale();
        return $this->attributes['caption_'.$locale];
    }

    public function getDescriptionAttribute(){
        $locale = App::getLocale();
        return $this->attributes['description_'.$locale];
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function getBannerImageAttribute($value){
        return ($value == null) ? null : secure_url('uploads/images/banners/'.$value);
    }

    public function getMediaName(){
        return $this->attributes['banner_image'];
    }
}
