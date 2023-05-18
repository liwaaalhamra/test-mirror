<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\Media
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $media_type
 * @property string $meta_name
 * @property string $meta_type
 * @property string $meta_id
 * @property string $media_name
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Media whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Media whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Media whereMediaType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Media whereMediaName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Media whereMetaName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Media whereMetaType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Media whereMetaId($value)
 * @mixin \Eloquent
 */
class Media extends Model
{
    protected $table = 'media';
    protected $fillable = ['media_type','media_name','meta_id','meta_name','user_id','height','width','meta_type'];
    protected $hidden = ['updated_at','meta_id','meta_type','media_type','user_id'];
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function getMediaNameAttribute($value)
    {
        if($this->user_id != null){
            return env('APP_ENV') == 'local' ? asset('public/uploads/'.$this->media_type.'/'.$this->user_id.'/'.$this->meta_name.DIRECTORY_SEPARATOR.$value) : secure_url('public/uploads/'.$this->media_type.'/'.$this->user_id.'/'.$this->meta_name.DIRECTORY_SEPARATOR.$value);
        }else{
            return env('APP_ENV') == 'local' ? asset('public/uploads/'.$this->media_type.'/'.$this->user_id.'/'.$this->meta_name.DIRECTORY_SEPARATOR.$value) : secure_url('public/uploads/'.$this->media_type.'/'.$this->user_id.'/'.$this->meta_name.DIRECTORY_SEPARATOR.$value);

        }
    }

    public function getMediaName(){
        return $this->attributes['media_name'];
    }

    public function meta(){
        return $this->belongsTo($this->meta_name);
    }

    public function user(){
        return $this->belongsTo(Users::class,'user_id');
    }
}
