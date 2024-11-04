<?php

namespace App\Models\Api;

use App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\Offer
 *
 * @property integer $id
 * @property string $target
 * @property integer $item_id
 * @property integer $is_published
 * @property integer $item_order
 * @property string $image
 * @property string $title_en
 * @property string $title_ar
 * @property string $subtitle_ar
 * @property string $subtitle_en
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Offer whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Offer whereItemId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Offer whereTarget($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Offer whereIsPublished($value)
 * @mixin \Eloquent
 */
class Offer extends Model
{
    protected $table = 'offers';
    protected $guarded = [];
    protected $hidden = ['updated_at','is_published','item_order','created_at',
        'target','title_en','title_ar','subtitle_en','subtitle_ar','unit_en','unit_ar','item_id'];

    protected $appends = ['title','subtitle'];
    protected $with = ['item'];

    public function getTitleAttribute(){
        $locale = App::getLocale();
        return $this->attributes['title_'.$locale];
    }

    public function getSubtitleAttribute(){
        $locale = App::getLocale();
        return $this->attributes['subtitle_'.$locale];
    }

    public function item(){
        return $this->belongsTo(Restaurant::class, 'item_id', 'id');
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function getImageAttribute($value){
        return ($value == null) ? null : secure_url('uploads/images/offers/'.$value);
    }

    public function getMediaName(){
        return $this->attributes['image'];
    }

}
