<?php

namespace App\Models\Api;

use App\Http\Controllers\Controller;
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
class UserRatingLiker extends Model
{
    protected $table = "user_rating_likers";
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(Users::class,'user_id');
    }
    public function liker(){
        return $this->belongsTo(Users::class,'liker_id');
    }
    public function rating(){
        return $this->belongsTo(UserRating::class,'rating_id');
    }

}
