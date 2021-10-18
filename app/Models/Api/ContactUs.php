<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\Api\ContactUs
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
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\ContactUs whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\ContactUs whereCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\ContactUs whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\ContactUs whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\ContactUs whereMessage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\ContactUs whereTitle($value)
 */
class ContactUs extends Model
{
    protected $table = "contact_us";
    protected $fillable = ['user_id','message','title','email','category_id'];
    public function user(){
        return $this->belongsTo(Users::class,'user_id');
    }
    public function category(){
        return $this->belongsTo(ContactUsCategory::class,'category_id');
    }

}
