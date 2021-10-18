<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Api\CheckoutLog
 *
 * @property integer $id
 * @property integer $order_id
 * @property string $payment_response
 * @property string $payment_request
 * @property integer $transaction_id
 * @property string $payment_url
 * @property integer $payment_method_id
 * @property integer $users_id
 * @property integer $done
 * @property string $payment_status
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\CheckoutLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\CheckoutLog whereOrderId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\CheckoutLog whereTransactionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\CheckoutLog wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\CheckoutLog whereDone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\CheckoutLog wherePaymentStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\CheckoutLog whereUsersId($value)
 * @mixin \Eloquent
 */
class CheckoutLog extends Model
{
    use SoftDeletes;
    protected $table = 'checkout_logs';
    protected $guarded = [];
    protected $hidden = ['updated_at','reference','users_id','order_id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model)
        {
            $model->generateProductCode();
        });
    }

    public function generateProductCode(){
        $this->attributes['reference'] = sprintf('%04X%04X', mt_rand(32768, 49151), mt_rand(0, 65535));
    }

    public function user()
    {
        return $this->belongsTo(Users::class,'users_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }
}
