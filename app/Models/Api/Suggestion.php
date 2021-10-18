<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
/**
 * App\Models\Api\Suggestion
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $user_id
 * @property string $message
 * @property string $email
 * @property string $title
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Suggestion whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Suggestion whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Suggestion whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Suggestion whereTitle($value)
 */
class Suggestion extends Model
{
    protected $table = "suggestions";
    protected $fillable = ['user_id','message','title','email'];
    public function user(){
        return $this->belongsTo(Users::class,'user_id');
    }

}
