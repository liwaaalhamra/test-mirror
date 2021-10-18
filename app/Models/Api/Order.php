<?php

namespace App\Models\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Api\Order
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $payment_method_id
 * @property integer $user_id
 * @property integer $coupon_id
 * @property integer $location_id
 * @property string $status
 * @property string $delivery_status
 * @property string $code
 * @property string $order_time
 * @property string $tag
 * @property string $notes
 * @property string $payment_type
 * @property double $current_latitude
 * @property double $coupon_amount
 * @property double $current_longitude
 * @property string $location_notes
 * @property string $raw_address
 * @property double $latitude
 * @property double $longitude
 * @property double $price
 * @property double $delivery_charge
 * @property double $total_price
 * @property \Carbon\Carbon $order_date
 * @property \Carbon\Carbon $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Order whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Order wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Order whereLocationId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Order whereCouponId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Order wherePaymentType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Order whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Order whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Order whereDeliveryStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Order whereCode($value)
 */

class Order extends Model
{
    use SoftDeletes;

    protected $table = "orders";
    protected $guarded = [];
    protected $hidden = ['updated_at','deleted_at','pivot','user_id','current_latitude',
        'current_longitude','tag','location_notes','region_id','city_id','delivery_type','latitude','longitude','coupon_id',
        'building','street','raw_address','friend_name','friend_mobile_number',
        'location_id','tracking_code','flick_id','merchant_type','merchant_id','status_at'];

    protected $casts = [
        'bring_change' => 'boolean',
        'with_cutlery' => 'boolean',
    ];
    protected $with = ['user','items','location'];
    protected $appends = ['tracking_link','is_completed','status_list','discount_amount_formatted','coupon_amount_formatted','delivery_charge_formatted','total_price_formatted','sub_total_formatted','original_sub_total_formatted'];

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->getTimestamp();
    }

    protected function getDiscountAmountFormattedAttribute(){
        $discount_amount = $this->attributes['discount_amount'];
        return $discount_amount ? '-'.number_format($discount_amount,0) . ' ' . $this->attributes['currency'] : null ;
    }

    protected function getCouponAmountFormattedAttribute(){
        $coupon_amount = $this->attributes['coupon_amount'];
        return $coupon_amount ? '-'.number_format($coupon_amount,0) . ' ' . $this->attributes['currency'] : null ;
    }

    protected function getDeliveryChargeFormattedAttribute(){
        $delivery_charge = $this->attributes['delivery_charge'];
        return $delivery_charge ? number_format($delivery_charge,0) . ' ' . $this->attributes['currency'] : null ;
    }

    protected function getTotalPriceFormattedAttribute(){
        $total_price = $this->attributes['total_price'];
        return $total_price ? number_format($total_price,0) . ' ' . $this->attributes['currency'] : null ;
    }

    protected function getSubTotalFormattedAttribute(){
        $sub_total = $this->attributes['total_price'] - $this->attributes['delivery_charge'];
        return $sub_total ? number_format($sub_total,0) . ' ' . $this->attributes['currency'] : null ;
    }

    protected function getOriginalSubTotalFormattedAttribute(){
        $original_sub_total = $this->attributes['total_price'] - $this->attributes['delivery_charge'] + $this->attributes['coupon_amount'] + $this->attributes['discount_amount'];
        return $original_sub_total ? number_format($original_sub_total,0) . ' ' . $this->attributes['currency'] : null ;
    }

    public function getStatusListAttribute(){
        $order_statuses = OrderStatus::selectraw('label,status')->whereIsPublished(1)->orderby('item_order','asc')->get()->toArray();
        $status_selection = $this->attributes['status'] == 'new_order' ? false : true;
        foreach ($order_statuses as &$order_status){
            if($status_selection){
                $order_status['is_selected'] = true;
            }else{
                $order_status['is_selected'] = false;
            }
            if($order_status['status'] == $this->attributes['status']){
                $order_status['is_selected'] = true;
                $status_selection = false;
            }
        }
        return $order_statuses;
    }

    public function getIsCompletedAttribute(){
        return $this->attributes['status'] == 'delivered' ? true : false;
    }

    public function getStatusAtAttribute($value){
        return json_decode($value,true);
    }

    public function setStatusAtAttribute($value){
        $this->attributes['status_at'] = json_encode($value);
    }

    public function getTrackingLinkAttribute()
    {
        return isset($this->attributes['tracking_code']) && $this->attributes['tracking_code'] != null ? env('FLICK_TRACKING_BASE_URL').'/'.$this->attributes['tracking_code'] : null ;
    }

    public function getDeliveryDateAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function setDeliveryDateAttribute($item_datetime)
    {
        //2021-01-19 15:46:59
        $this->attributes['delivery_date'] = $item_datetime == null ? null : Carbon::createFromFormat('Y-m-d H:i:s',$item_datetime)->format('Y-m-d H:i:s');
    }

    public function getPickupDateAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function setPickupDateAttribute($item_datetime)
    {
        //2021-01-19 15:46:59
        $this->attributes['pickup_date'] = $item_datetime == null ? null : Carbon::createFromFormat('Y-m-d H:i:s',$item_datetime)->format('Y-m-d H:i:s');
    }

    public function getArrivedAtPartnerDateAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function setArrivedAtPartnerDateAttribute($item_datetime)
    {
        //2021-01-19 15:46:59
        $this->attributes['arrived_at_partner_date'] = $item_datetime == null ? null : Carbon::createFromFormat('Y-m-d H:i:s',$item_datetime)->format('Y-m-d H:i:s');
    }

    public function getOrderDateAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function setOrderDateAttribute($item_datetime)
    {
        $this->attributes['order_date'] = $item_datetime == null ? null : Carbon::createFromTimestamp($item_datetime)->format('Y-m-d H:i:s');
    }

    public function region(){
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }

    public function location(){
        return $this->belongsTo(UserLocation::class, 'location_id', 'id');
    }

    public function merchant(){
        return $this->attributes['merchant_type'] == 'restaurant' ? $this->belongsTo(Restaurant::class, 'merchant_id', 'id') : $this->belongsTo(SupplierFoodGroup::class, 'merchant_id', 'id');
    }

    public function coupon(){
        return $this->belongsTo(Coupon::class, 'coupon_id', 'id');
    }

    public function city(){
        return $this->belongsTo(Area::class, 'city_id', 'id');
    }

    public function getStatusInfoAttribute(){
        return OrderStatus::whereStatus($this->attributes['status'])->first();
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

    public function user(){
        return $this->belongsTo(Users::class,'user_id');
    }

    public function items(){
        return $this->hasMany(PackageItem::class,'order_id','id');
    }

    /**
     * the below will return items even if not linked yet to order_id (in case of online)
     */
    public function pre_items(){
        return $this->hasMany(PackageItem::class,'order_code','code')->whereNull('supplier_food_group_id');
    }

    public function supplier_pre_items(){
        return $this->hasMany(PackageItem::class,'order_code','code')->whereNotNull('supplier_food_group_id');
    }

    public function items1(){
        return $this->belongsToMany(Restaurant::class,'package_items','order_id','item_id')
            ->whereNotNull('package_items.order_id')
            ->whereNull('package_items.deleted_at')
//            ->withPivot('quantity as quantity_in_cart','price as final_price','item_size_id as size_id');
            ->withPivot('quantity as quantity_in_cart','price as final_price');
    }

}
