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
class UserReservation extends Model
{
    protected $table = "user_reservations";
    protected $hidden = ['user_id','item_id','event_type_id','updated_at','accepted_at','rejected_at','canceled_at','expired_at'];
    protected $guarded = [];
    protected $appends = ['persons_label'];
    protected $with = ['user','event_type'];

    public function getCreatedAtAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function getAcceptedAtAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function setAcceptedAtAttribute($item_datetime)
    {
        $this->attributes['accepted_at'] = $item_datetime == null ? null : Carbon::createFromTimestamp($item_datetime)->format('Y-m-d H:i:s');
    }

    public function getRejectedAtAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function setRejectedAtAttribute($item_datetime)
    {
        $this->attributes['rejected_at'] = $item_datetime == null ? null : Carbon::createFromTimestamp($item_datetime)->format('Y-m-d H:i:s');
    }

    public function getCanceledAtAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function setCanceledAtAttribute($item_datetime)
    {
        $this->attributes['canceled_at'] = $item_datetime == null ? null : Carbon::createFromTimestamp($item_datetime)->format('Y-m-d H:i:s');
    }

    public function getExpiredAtAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function setExpiredAtAttribute($item_datetime)
    {
        $this->attributes['expired_at'] = $item_datetime == null ? null : Carbon::createFromTimestamp($item_datetime)->format('Y-m-d H:i:s');
    }

    public function getPersonsLabelAttribute(){
        return $this->attributes['persons'] ? 'Reservation for '.$this->attributes['persons'].' People' : 'n/a';
    }

    public function user(){
        return $this->belongsTo(Users::class,'user_id');
    }
    public function item(){
        return $this->belongsTo(Restaurant::class,'item_id');
    }
    public function event_type(){
        return $this->belongsTo(RestaurantEventType::class,'event_type_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model)
        {
            $model->generateCode();
        });
    }

    public function generateCode(){
        $this->attributes['code'] = Controller::number_generator(6);
    }

    public function getReservationDatetimeAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function setReservationDatetimeAttribute($item_datetime)
    {
        $this->attributes['reservation_datetime'] = $item_datetime == null ? null : Carbon::createFromTimestamp($item_datetime)->format('Y-m-d H:i:s');
    }



}
