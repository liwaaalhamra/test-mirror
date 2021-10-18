<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\Api\Support
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $user_id
 * @property integer $category_id
 * @property string $message
 * @property string $email
 * @property string $title
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Support whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Support whereCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Support whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Support whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Support whereMessage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Support whereTitle($value)
 */
class Support extends Model
{
    protected $table = "support";
    protected $fillable = ['user_id','message','title','email','category_id'];
    public function user(){
        return $this->belongsTo(Users::class,'user_id');
    }
    public function category(){
        return $this->belongsTo(TalkToUsCategory::class,'category_id');
    }

}
