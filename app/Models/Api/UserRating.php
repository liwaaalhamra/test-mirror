<?php

namespace App\Models\Api;

use App\Http\Controllers\Controller;
use App\Traits\HITrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\Api\UserReservation
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class UserRating extends Model
{
    use HITrait;
    protected $table = "user_rating";
    protected $guarded = [];
    protected $hidden = ['user_id','updated_at','tags_like','tags_dislike','is_approved','in_explore','item_order'];
    protected $with = ['user'];
    protected $appends = ['dislike_list','like_list','is_liked'];

    public function getCreatedAtAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function getIsLikedAttribute(){
        $is_liked = false;
        if(request()->header('token')){
            $user = $this->validate_user(request());
            if(UserRatingLiker::whereLikerId($user->id)->whereRatingId($this->attributes['id'])->first()){
                $is_liked = true;
            }
        }
        return $is_liked;
    }

    public function user(){
        return $this->belongsTo(Users::class,'user_id');
    }
    public function restaurant(){
        return $this->belongsTo(Restaurant::class,'item_id');
    }

    public function getTagsLikeAttribute($value){
        return explode(',', $value);
    }

    public function getLikeListAttribute()
    {
        if (!$this->relationLoaded('like_list')) {
            $tagsL = ReviewTag::whereIsPublished(1)->whereType('like')->whereIn('id', $this->tags_like)->orderby('item_order','asc')->get();

            $this->setRelation('like_list', $tagsL);
        }

        return $this->getRelation('like_list');
    }

    public function like_list()
    {
        return ReviewTag::whereIsPublished(1)->whereType('like')->whereIn('id', $this->tags_like)->orderby('item_order','asc')->get();
    }

    public function getTagsDislikeAttribute($value){
        return explode(',', $value);
    }

    public function getDislikeListAttribute()
    {
        if (!$this->relationLoaded('dislike_list')) {
            $tagsD = ReviewTag::whereIsPublished(1)->whereType('dislike')->whereIn('id', $this->tags_dislike)->orderby('item_order','asc')->get();

            $this->setRelation('dislike_list', $tagsD);
        }

        return $this->getRelation('dislike_list');
    }

    public function dislike_list()
    {
        return ReviewTag::whereIsPublished(1)->whereType('dislike')->whereIn('id', $this->tags_dislike)->orderby('item_order','asc')->get();
    }

    public function setTagsLikeAttribute($value){
        $this->attributes['tags_like'] = $value == null ? null : implode(',', $value);
    }
    public function setTagsDislikeAttribute($value){
        $this->attributes['tags_dislike'] = $value == null ? null : implode(',', $value);
    }

    public function getImageAttribute($value){
        return ($value == null) ? null : secure_url('uploads/images/rating/'.$value);
    }

    public function getMediaName(){
        return $this->attributes['image'];
    }

}
