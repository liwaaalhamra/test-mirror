<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\Api\Rating
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $user_id
 * @property string $comment
 * @property string $full_name
 * @property string $email
 * @property integer $stars
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Rating whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Rating whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Rating whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Rating whereStars($value)
 */
class Rating extends Model
{
    protected $table = "rating";
    protected $guarded = [];
    protected $hidden = ['user_id','updated_at'];
    public function user(){
        return $this->belongsTo(Users::class,'user_id');
    }

}
