<?php

namespace App\Models\Api;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Hash;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Api\Users
 *
 * @property integer $id
 * @property integer $coupon_id
 * @property string $account_type
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Users whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Users whereIsVerified($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Users whereIsBlocked($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Users whereCouponId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Users whereAccountType($value)
 * @mixin \Eloquent
 */
class Users extends Model
{
    use SoftDeletes;
	protected $table = 'users';
    //public $timestamps = false;
	protected $guarded = [];

    public $preventAttrSet = false;

    protected $hidden = ['updated_at','password','reset_code','activation_code','udid','cart_delivery_type','first_name','last_name',
        'fathers_name','mothers_name','password_clear_text','barcode','is_verified','description','is_blocked','department','cost_center','job_title',
        'address','street','building','floor','secondary_phone','country_id','verified_at','google_id','facebook_id','apple_id',
        'code','created_at','pin','registration','unread_memos_count','profile_type_id','employee_id','total_points','blood_type',
        'device_name','browser_name','browser_version','platform_name','platform_version','is_mobile','is_tablet','is_desktop','user_agent','ip',
    'snapchat_username','instagram_username','facebook_username','registration_number','company_name','coupon_amount','coupon_type','coupon_id',
        'profile_registration','profile_pin','profile_verified_at','deleted_at','cart_restaurant_id','cart_supplier_id'];

    protected $casts = [
        'is_new_user' => 'boolean',
        'is_profile_verified' => 'boolean',
    ];
    protected $appends = ['age','currency'];

    protected $with = ['country'];

	/**
	 * A user may have a push token.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function pushToken()
	{
		return $this->hasOne(UserPush::class);
	}

    public function all_reviews(){
        return $this->hasMany(UserRating::class,'user_id','id')->whereIsApproved(1);
    }
    public function dining_reviews(){
        return $this->hasMany(UserRating::class,'user_id','id')->whereIsApproved(1)->whereType('dining');
    }
    public function delivery_reviews(){
        return $this->hasMany(UserRating::class,'user_id','id')->whereIsApproved(1)->whereType('delivery');
    }

	public function getCurrencyAttribute(){
	    return $this->country ? $this->country->currency_code : null;
    }

    public function cart(){
	    return $this->hasMany(PackageItem::class,'user_id','id')->whereNull('order_id')->whereNull('supplier_food_group_id');
    }

    public function supplier_cart(){
	    return $this->hasMany(PackageItem::class,'user_id','id')->whereNull('order_id')->whereNotNull('supplier_food_group_id');
    }

    public function cart1(){
        return $this->belongsToMany(Item::class,'package_items','user_id','item_id')
            ->whereNull('package_items.order_id')
            ->whereNull('package_items.deleted_at')
//            ->withPivot('quantity as quantity_in_cart','price as final_price','item_size_id as size_id');
            ->withPivot('quantity as quantity_in_cart','price as final_price');
    }

    public function cart_item($item_id){
        return $this->belongsToMany(Item::class,'package_items','user_id','item_id')
            ->where('package_items.id','=',$item_id)
            ->whereNull('package_items.order_id')
            ->whereNull('package_items.deleted_at')
//            ->withPivot('quantity as quantity_in_cart','price as final_price','item_size_id as size_id');
            ->withPivot('quantity as quantity_in_cart','price as final_price');
    }

	public function getAgeAttribute(){
        return !isset($this->attributes['dob']) || $this->attributes['dob'] == null ? null : Carbon::parse($this->attributes['dob'])->age;
    }
    public function country(){
        return $this->belongsTo(Country::class,'country_id');
    }
    public function coupon(){
        return $this->belongsTo(Coupon::class,'coupon_id');
    }
    public function points()
    {
        return $this->hasMany(UserPoints::class,'users_id','id');
    }

    public function locations()
    {
        return $this->hasMany(UserLocation::class,'users_id','id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class,'user_id','id');
    }

    public function favorites($search = null){
	    if($search){
            return $this->belongsToMany(Item::class,'user_favorites','user_id','meta_id')
                ->where('expired_at','>',Carbon::now())
                ->where('user_favorites.meta_name','=','items')
                ->where(function($query) use ($search){
                    $query->whereRaw("LOWER(name_en) like ? ", [ "%".strtolower($search)."%"])
                        ->orwhereRaw("LOWER(name_ar) like ? ", [ "%".strtolower($search)."%"]);
                })->with('return_post')
                ->get();
        }
        //whereDoesntHave('barter')whereDoesntHave('barterReturn')
        return $this->belongsToMany(Item::class,'user_favorites','user_id','meta_id')->where('user_favorites.meta_name','=','items')->with('return_post')->where('expired_at','>',Carbon::now())->get();
    }

    /**
     * A user may has an access token.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function access_token()
    {
        return $this->hasOne(UsersAccess::class,'user_id','id');
    }

    public function getPhotoAttribute($value)
    {
        return ($value == null || !file_exists(public_path('uploads/images/profiles/'.$value))) ? null : secure_url('uploads/images/profiles').DIRECTORY_SEPARATOR.$value;
    }

    public function getPhotoName(){
        return $this->attributes['photo'];
    }

    public function getFullNameAttribute(){
        $full_name = $this->attributes['full_name'] ? $this->attributes['full_name'] : $this->attributes['first_name'].' '.$this->attributes['last_name'];
        return $full_name ;
    }

    public function setPasswordAttribute($value) {
        $this->attributes['password'] = bcrypt($value);
    }
    public function setPasswordClearTextAttribute($value) {
        if ($this->preventAttrSet) {
            // Ignore Mutator
            $this->attributes['password_clear_text'] = $value;
        } else {
            $this->attributes['password_clear_text'] = hash('sha256', $value);
        }
    }


}
