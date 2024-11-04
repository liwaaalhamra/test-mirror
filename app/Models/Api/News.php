<?php

namespace App\Models\Api;

use App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\News
 *
 * @property integer $id
 * @property string $target
 * @property integer $admin_id
 * @property integer $item_id
 * @property integer $is_published
 * @property integer $redeemable
 * @property integer $news_order
 * @property string $media
 * @property string $media_type
 * @property string $thumbnail
 * @property string $caption_en
 * @property string $caption_ar
 * @property string $youtube_id
 * @property string $description_en
 * @property string $description_ar
 * @property \Carbon\Carbon $news_datetime
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\News whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\News whereItemId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\News whereTarget($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\News whereRedeemable($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\News whereAdminId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\News whereIsPublished($value)
 * @mixin \Eloquent
 */
class News extends Model
{
    protected $table = 'news';
    protected $guarded = [];
    protected $hidden = ['updated_at','admin_id','is_published','news_order','created_at',
        'target','thumbnail','news_datetime','youtube_id','redeemable','caption_en','caption_ar','description_en','description_ar'];
    protected $casts = [
        'redeemable' => 'boolean'
    ];

    protected $appends = ['description','caption'];

    public function getDescriptionAttribute(){
        $locale = App::getLocale();
        return $this->attributes['description_'.$locale];
    }

    public function getCaptionAttribute(){
        $locale = App::getLocale();
        return $this->attributes['caption_'.$locale];
    }

    public function item(){
        return $this->belongsTo(Restaurant::class, 'item_id', 'id');
    }

    public function admin(){
        return $this->belongsTo(User::class, 'admin_id', 'id');
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function getNewsDatetimeAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function setNewsDatetimeAttribute($item_datetime)
    {
        $this->attributes['news_datetime'] = $item_datetime == null ? null : Carbon::createFromTimestamp($item_datetime)->format('Y-m-d H:i:s');
    }

    public function getMediaAttribute($value){
        if($this->attributes['media_type'] == 'image'){
            return ($value == null) ? null : secure_url('uploads/images/news/'.$value);
        }else{
            return ($value == null) ? null : $value;
        }
    }

    public function getMediaName(){
        return $this->attributes['media'];
    }

}
