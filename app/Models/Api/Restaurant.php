<?php

namespace App\Models\Api;

use App;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Api\Restaurant
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $sub_category_id
 * @property integer $category_id
 * @property integer $brand_id
 * @property integer $product_type_id
 * @property integer $is_gift
 * @property integer $item_order
 * @property integer $is_available
 * @property double $price
 * @property double $discounted_price
 * @property double $quantity
 * @property string $name_en
 * @property string $name_ar
 * @property string $meta_name
 * @property string $code
 * @property string $description_en
 * @property string $description_ar
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $expiry_date
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Restaurant whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Restaurant whereBrandId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Restaurant whereProductTypeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Restaurant whereCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Restaurant whereSubCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Restaurant whereIsGift($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Restaurant whereIsAvailable($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Restaurant whereQuantity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Restaurant whereItemOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Restaurant whereMetaName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Api\Restaurant whereCode($value)
 */

class Restaurant extends Model
{
    use SoftDeletes;

    protected $table = "restaurants";
    protected $guarded = [];
    protected $hidden = ['updated_at','deleted_at','pivot','item_order','meta_name','is_available','is_gift','sub_category_id',
        'name_en','name_ar','description_en','description_ar','category_id','brand_id','product_type_id',
        'item_size_id','cuisine_ids','tag_ids','craving_ids','username','password','expired_at','access_token','reset_at'];
    protected $casts = [
        'is_available' => 'boolean',
        'is_closed' => 'boolean',
        'is_partner' => 'boolean',
        'has_reserve_table' => 'boolean',
        'has_delivery' => 'boolean',
    ];
    protected $appends = ['name','description',
        'reviews_dining_total','reviews_delivery_total','reviews_total',
        'reviews_dining_average_stars','reviews_delivery_average_stars','reviews_average_stars',
        'cuisines','tags','cost_caption','top_cravings','barcode'
    ];

    protected $with = ['category','event_types','services','current_delivery_hours',
        'current_opening_hours','opening_hours','delivery_hours','images','all_reviews','dining_reviews','delivery_reviews','country'];

    public function getNameAttribute(){
        $locale = App::getLocale();
        return $this->attributes['name_'.$locale];
    }

    public function country(){
        return $this->belongsTo(Country::class,'country_id');
    }

    public function getExpiredAtAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value);
    }

    public function getResetAtAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function setResetAtAttribute($item_datetime)
    {
        $this->attributes['reset_at'] = $item_datetime == null ? null : Carbon::createFromTimestamp($item_datetime)->format('Y-m-d H:i:s');
    }

    public function getCostCaptionAttribute(){
        $cost = $this->attributes['cost'] ? number_format($this->attributes['cost'],0,'.',',') : 0;
        return env('APP_CURRENCY').' '.$cost.' for one';
    }

    public function getReviewsTotalAttribute()
    {
        return $this->hasMany(UserRating::class,'item_id','id')->whereIsApproved(1)->count();
    }
    public function getReviewsDiningTotalAttribute()
    {
        return $this->hasMany(UserRating::class,'item_id','id')->whereIsApproved(1)->whereType('dining')->count();
    }
    public function getReviewsDeliveryTotalAttribute()
    {
        return $this->hasMany(UserRating::class,'item_id','id')->whereIsApproved(1)->whereType('delivery')->count();
    }

    public function getReviewsAverageStarsAttribute()
    {
        return round($this->hasMany(UserRating::class,'item_id','id')->whereIsApproved(1)->avg('stars'),2);
    }
    public function getReviewsDiningAverageStarsAttribute()
    {
        return round($this->hasMany(UserRating::class,'item_id','id')->whereIsApproved(1)->whereType('dining')->avg('stars'),2);
    }
    public function getReviewsDeliveryAverageStarsAttribute()
    {
        return round($this->hasMany(UserRating::class,'item_id','id')->whereIsApproved(1)->whereType('delivery')->avg('stars'),2);
    }

    public function getTablePersonsAttribute($value){
        return $value == null ? [] : explode(',', $value);
    }

    public function setTablePersonsAttribute($value){
        $this->attributes['table_persons'] = $value == null ? null : implode(',', $value);
    }

    public function getCuisineIdsAttribute($value){
        return explode(',', $value);
    }
    public function setCuisineIdsAttribute($value){
        $this->attributes['cuisine_ids'] = $value == null ? null : implode(',', $value);
    }

    public function getCuisinesAttribute()
    {
        if (!$this->relationLoaded('cuisines')) {
            $cuisines = Cuisine::whereIsPublished(1)->whereIn('id', $this->cuisine_ids)->orderby('item_order','asc')->get();

            $this->setRelation('cuisines', $cuisines);
        }

        return $this->getRelation('cuisines');
    }

    public function cuisines()
    {
        return Cuisine::whereIsPublished(1)->whereIn('id', $this->cuisine_ids)->orderby('item_order','asc')->get();
    }

    public function cuisinesName()
    {
        return implode(',', Cuisine::whereIsPublished(1)->whereIn('id', $this->cuisine_ids)->orderby('item_order','asc')->pluck('name_en')->toarray());
    }

    public function getTagIdsAttribute($value){
        return explode(',', $value);
    }
    public function setTagIdsAttribute($value){
        $this->attributes['tag_ids'] = $value == null ? null : implode(',', $value);
    }

    public function getTagsAttribute()
    {
        if (!$this->relationLoaded('tags')) {
            $tags = Tag::whereIsPublished(1)->whereIn('id', $this->tag_ids)->orderby('item_order','asc')->get();

            $this->setRelation('tags', $tags);
        }

        return $this->getRelation('tags');
    }

    public function tags()
    {
        return Tag::whereIsPublished(1)->whereIn('id', $this->tag_ids)->orderby('item_order','asc')->get();
    }

    public function tagsName()
    {
        return implode(',', Tag::whereIsPublished(1)->whereIn('id', $this->tag_ids)->orderby('item_order','asc')->pluck('name_en')->toarray());
    }

    public function getCravingIdsAttribute($value){
        return explode(',', $value);
    }
    public function setCravingIdsAttribute($value){
        $this->attributes['craving_ids'] = $value == null ? null : implode(',', $value);
    }

    public function getTopCravingsAttribute()
    {
        if (!$this->relationLoaded('top_cravings')) {
            $cravings = Craving::whereIsPublished(1)->whereIn('id', $this->craving_ids)->orderby('item_order','asc')->get();

            $this->setRelation('top_cravings', $cravings);
        }

        return $this->getRelation('top_cravings');
    }

    public function top_cravings()
    {
        return Craving::whereIsPublished(1)->whereIn('id', $this->craving_ids)->orderby('item_order','asc')->get();
    }

    public function cravingsName()
    {
        return implode(',', Craving::whereIsPublished(1)->whereIn('id', $this->craving_ids)->orderby('item_order','asc')->pluck('name_en')->toarray());
    }

    public function getDescriptionAttribute(){
        $locale = App::getLocale();
        return $this->attributes['description_'.$locale];
    }

    public function getCreatedAtAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }

    public function getExpiryDateAttribute($value)
    {
        return $value == null ? null : \Carbon\Carbon::parse($value)->getTimestamp();
    }
    public function setExpiryDateAttribute($item_datetime)
    {
        $this->attributes['expiry_date'] = $item_datetime == null ? null : Carbon::createFromTimestamp($item_datetime)->format('Y-m-d H:i:s');
    }

    public function event_types(){
        return $this->hasMany(RestaurantEventType::class,'item_id','id')->whereIsPublished(1)->orderby('item_order');
    }

    public function menu_categories(){
        return $this->hasMany(SubCategory::class,'category_id','id')->whereCategoryType('restaurant')->whereIsPublished(1)->orderby('item_order');
    }

    public function opening_hours(){
        return $this->hasMany(RestaurantTiming::class,'item_id','id')->whereType('opening')->orderby('day');
    }
    public function delivery_hours(){
        return $this->hasMany(RestaurantTiming::class,'item_id','id')->whereType('delivery')->orderby('day');
    }

    public function current_opening_hours(){
        $week_day = Carbon::now()->dayOfWeek;
        $now = Carbon::now()->format('H:i:s');
        return $this->hasOne(RestaurantTiming::class,'item_id','id')->whereType('opening')->where('day','=',$week_day)->where(function($query) use ($now){
            $query->whereRaw("DATE_FORMAT(time_from,'%H:%i:%s') <= '$now'")
                ->whereRaw("DATE_FORMAT(time_to,'%H:%i:%s') >= '$now'");
        });

    }

    public function current_delivery_hours(){
        $week_day = Carbon::now()->dayOfWeek;
        return $this->hasOne(RestaurantTiming::class,'item_id','id')->whereType('delivery')->where('day','=',$week_day);
    }

    public function sub_category(){
        return $this->belongsTo(CuratedCollection::class,'sub_category_id');
    }

    public function category(){
        return $this->belongsTo(CollectionCategory::class,'category_id');
    }

    public function services(){
        return $this->belongsToMany(Service::class,'restaurant_services','item_id','service_id')->whereIsPublished(1)->orderby('item_order','asc');
    }

    public function servicesName()
    {
        return implode(',', $this->belongsToMany(Service::class,'restaurant_services','item_id','service_id')->whereIsPublished(1)->orderby('item_order','asc')->pluck('name_en')->toarray());
    }

    public function all_reviews(){
        return $this->hasMany(UserRating::class,'item_id','id')->whereIsApproved(1);
    }
    public function dining_reviews(){
        return $this->hasMany(UserRating::class,'item_id','id')->whereIsApproved(1)->whereType('dining');
    }
    public function delivery_reviews(){
        return $this->hasMany(UserRating::class,'item_id','id')->whereIsApproved(1)->whereType('delivery');
    }

    public function product_type(){
        return $this->belongsTo(ProductType::class,'product_type_id');
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
        $this->attributes['code'] = '113228'.Controller::number_generator(9);
    }

    public function getImageAttribute($value){
       return ($value == null) ? null : secure_url('public/uploads/images/restaurants/'.$value);
    }

    public function getMediaName(){
        return $this->attributes['image'];
    }

    public function getPdfAttribute($value){
        return ($value == null) ? null : secure_url('public/uploads/documents/restaurants/'.$value);
    }

    public function getPdfName(){
        return $this->attributes['pdf'];
    }

    public function images(){
        return $this->hasMany(Media::class,'meta_id','id')->where('meta_name','restaurants')->where('media_type','images');
    }

    public function getBarcodeAttribute()
    {
        return secure_url('barcode.php?f=png&s=code-128&sf=1&sy=0.5&sx=2&ts=4&th=20&p=20&pt=2&pr=0&pl=0&d='.$this->attributes['code']);
    }

    public function getBarcodeValue(){
        return $this->attributes['code'];
    }

}
