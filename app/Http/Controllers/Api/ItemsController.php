<?php

namespace App\Http\Controllers\Api;


use App\Models\Api\Barter;
use App\Models\Api\Category;
use App\Models\Api\CustomField;
use App\Models\Api\CustomFieldList;
use App\Models\Api\Inbox;
use App\Models\Api\Item;
use App\Models\Api\ItemCustomField;
use App\Models\Api\Location;
use App\Models\Api\Restaurant;
use App\Models\Api\RestaurantEventType;
use App\Models\Api\ReviewTag;
use App\Models\Api\SubCategory;
use App\Models\Api\UserFavorite;
use App\Models\Api\UserPostViewed;
use App\Models\Api\UserPush;
use App\Models\Api\UserRating;
use App\Models\Api\UserRatingLiker;
use App\Models\Api\UserReservation;
use App\Traits\HITrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Validator;
use Propaganistas\LaravelPhone\PhoneNumber;

/**
 * Class ItemsController
 * @package App\Http\Controllers\Api
 */
class ItemsController extends Controller
{
    use HITrait;

    public function price_limit(Request $request) {
        if(!Input::has('price')){
            return parent::return_error('Missing Price',401,'messages.missing_parameter');
        }
        $price = $request->price;
        $limits = [];
        if($price < 3000){
            $limits['minimum'] = 0;
            $limits['maximum'] = 5000;
        }else{
            $limits['minimum'] = $price * 0.5;
            $limits['maximum'] = floatval($price) * 2;
        }
        return response()->json($limits);
    }

    public function lists($type, $parent_id = null){
        $data = CustomFieldList::when($parent_id, function ($query) use ($parent_id) {
                return $query->whereParentId($parent_id);
        })->whereFieldName($type)->get();
        return response()->json($data);
    }

    public function locations(Request $request){
        $country_id = isset($request->country_id) && !empty($request->country_id) ? $request->country_id : 784;
        $data = Location::without('country')->whereCountryId($country_id)->orderByRaw("LOWER(name_en) asc")->get();
        return response()->json($data);
    }

    public function all_categories(Request $request) {
        $query = Category::whereIsPublished(1)->orderBy('item_order','asc');
        $categories = $query->get()->groupBy('type');
        return response()->json($categories);
    }

    public function products(Request $request) {
        $query = Category::whereType('products')->whereIsPublished(1)->orderBy('item_order','asc');
        if(Input::has('search')){
            $search = Input::get('search');
            $query->where(function($query) use ($search){
                $query->whereRaw("LOWER(name_en) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(name_ar) like ? ", [ "%".strtolower($search)."%"]);
            });
        }
        $categories = $query->get();
        return response()->json($categories);
    }

    public function services(Request $request) {
        $query = Category::whereType('services')->whereIsPublished(1)->orderBy('item_order','asc');
        if(Input::has('search')){
            $search = Input::get('search');
            $query->where(function($query) use ($search){
                $query->whereRaw("LOWER(name_en) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(name_ar) like ? ", [ "%".strtolower($search)."%"]);
            });
        }
        $categories = $query->get();
        return response()->json($categories);
    }

    public function explore_reviews(Request $request) {
        $query = UserRating::whereInExplore(1)->whereIsApproved(1)->orderBy('item_order','asc');
        if(Input::has('search')){
            $search = Input::get('search');
            $query->where(function($query) use ($search){
                $query->whereRaw("LOWER(name_en) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(name_ar) like ? ", [ "%".strtolower($search)."%"]);
            });
        }
        $reviews = $query->paginate(20);

        foreach ($reviews as $review){
            $restaurant = $review->restaurant;
            $review->restaurant_name = $restaurant ? $restaurant->name_en : null;
            $review->restaurant_image = $restaurant ? $restaurant->image : null;
        }
        return response()->json($reviews);
    }

    public function category_subs(Request $request) {
        $query = SubCategory::whereIsPublished(1)->orderByRaw("LOWER(name_en) asc");
        if(Input::has('category_id')){
            
            $category_id = Input::get('category_id');
            $query->where(function($query) use ($category_id){
                $query->whereCategoryId($category_id);
            });
        }else{
            $query->whereIsFeatured(1);
        }
        $categories = $query->get();

        foreach($categories as $category){
            foreach($category->sub_sub_categories as $sub_category){
                $car_brand = CustomFieldList::whereRaw("LOWER(name_en) = ?",[ strtolower($sub_category->name_en)])->first();
                $sub_category->parent_id = $car_brand ? $car_brand->id : null;
            }
        }

        return response()->json($categories);
    }

    public function home_top_categories(Request $request) {
        $user = null;
        if($request->header('token')){
            $user = $this->validate_user($request);
        }
        $items_viewed = [];
        $latitude = isset($request->latitude) ? $request->latitude : null;
        $longitude = isset($request->longitude) ? $request->longitude : null;
        if($request->header('token')){
            $user = $this->validate_user($request);
            $items_viewed = UserPostViewed::whereUserId($user->id)->orderby('updated_at','desc')->pluck('item_id')->toarray();
        }
        $categories = [];
        $recently_viewed_items = [];
        $new_arrival_items = [];
        $latest_services_items = [];
        $nearby_items = [];
        $matches = [];
        if($latitude && $longitude){
            $query = Item::whereType('offer')->whereIsAvailable(1)->orderbyRaw("(6371 * 2 * ASIN(SQRT( POWER(SIN(( $latitude - latitude) *  pi()/180 / 2), 2) +COS( $latitude * pi()/180) * COS(latitude * pi()/180) * POWER(SIN(( $longitude - longitude) * pi()/180 / 2), 2) ))) asc");
            $currency = env('APP_CURRENCY');
            $nearby_items = $query->take(10)->get()->map(function ($value, $key) use ($currency,$user) {
                $value->is_favorite = false;
                if($user){
                    if(UserFavorite::whereMetaName('items')->whereMetaId($value->id)->whereUserId($user->id)->first()){
                        $value->is_favorite = true;
                    }
                }
                unset($value->quantity);
                $value->post_type = $value->category->type;
                unset($value->category);
                $value->currency = $currency;
                return $value;
            })->values();
        }



        $query = Item::wherein('id',$items_viewed)->whereType('offer')->whereIsAvailable(1)->orderby('created_at','desc');
        $currency = env('APP_CURRENCY');
        $recently_viewed_items = $query->take(10)->get()->map(function ($value, $key) use ($currency,$user) {
            $value->is_favorite = false;
            if($user){
                if(UserFavorite::whereMetaName('items')->whereMetaId($value->id)->whereUserId($user->id)->first()){
                    $value->is_favorite = true;
                }
            }
            unset($value->quantity);
            $value->currency = $currency;
            $value->post_type = $value->category->type;
            unset($value->category);
            return $value;
        })->values();

        $query = Item::whereType('offer')->whereHas('category', function ($q) {
            $q->whereType('products');
        })->whereIsAvailable(1)->orderby('created_at','desc');
        $currency = env('APP_CURRENCY');
        $new_arrival_items = $query->take(10)->get()->map(function ($value, $key) use ($currency,$user) {
            $value->is_favorite = false;
            if($user){
                if(UserFavorite::whereMetaName('items')->whereMetaId($value->id)->whereUserId($user->id)->first()){
                    $value->is_favorite = true;
                }
            }
            unset($value->quantity);
            $value->post_type = "products";
            $value->currency = $currency;
            return $value;
        })->values();

        $query = Item::whereType('offer')->whereHas('category', function ($q) {
            $q->whereType('services');
        })->whereIsAvailable(1)->orderby('created_at','desc');
        $currency = env('APP_CURRENCY');
        $latest_services_items = $query->take(10)->get()->map(function ($value, $key) use ($currency,$user) {
            $value->is_favorite = false;
            if($user){
                if(UserFavorite::whereMetaName('items')->whereMetaId($value->id)->whereUserId($user->id)->first()){
                    $value->is_favorite = true;
                }
            }
            $value->currency = $currency;
            $value->post_type = "services";
            return $value;
        })->values();

        $query = Item::whereType('offer')->whereIsAvailable(1)->orderby('created_at','desc');
        $currency = env('APP_CURRENCY');
        $matches = $query->take(10)->get()->map(function ($value, $key) use ($currency,$user) {
            $value->is_favorite = false;
            if($user){
                if(UserFavorite::whereMetaName('items')->whereMetaId($value->id)->whereUserId($user->id)->first()){
                    $value->is_favorite = true;
                }
            }
            $value->currency = $currency;
            $value->post_type = "services";
            return $value;
        })->values();


        $category1 = [
            'slug' => 'trending-services',
            'title' => 'Trending Services',
            'items' => $latest_services_items
        ];
        $category2 = [
            'slug' => 'trending-products',
            'title' => 'Trending Products',
            'items' => $new_arrival_items
        ];
        $category3 = [
            'slug' => 'recently-viewed',
            'title' => 'Recently Viewed',
            'items' => $recently_viewed_items
        ];
        $category4 = [
            'slug' => 'nearby',
            'title' => 'Nearby',
            'items' => $nearby_items
        ];
        $category5 = [
            'slug' => 'matches',
            'title' => 'My Matches',
            'items' => $matches
        ];

        array_push($categories,$category5);
        array_push($categories,$category2);
        array_push($categories,$category1);
        array_push($categories,$category3);
        array_push($categories,$category4);

        return response()->json($categories);
    }

    public function home_top_categories_items(Request $request,$slug) {
        $user = null;
        $items_viewed = [];
        $latitude = isset($request->latitude) ? $request->latitude : null;
        $longitude = isset($request->longitude) ? $request->longitude : null;
        if($request->header('token')){
            $user = $this->validate_user($request);
            $items_viewed = UserPostViewed::whereUserId($user->id)->orderby('updated_at','desc')->pluck('item_id')->toarray();
        }
        if($slug == 'trending-products'){
            $query = Item::whereType('offer')->whereHas('category', function ($q) {
                $q->whereType('products');
            })->whereIsAvailable(1)->orderby('created_at','desc');
            $currency = env('APP_CURRENCY');
            $items = $query->take(50)->get()->map(function ($value, $key) use ($currency,$user) {
                $value->is_favorite = false;
                if($user){
                    if(UserFavorite::whereMetaName('items')->whereMetaId($value->id)->whereUserId($user->id)->first()){
                        $value->is_favorite = true;
                    }
                }
                unset($value->quantity);
                $value->currency = $currency;
                $value->post_type = "products";
                return $value;
            })->values();
        }elseif ($slug == 'trending-services'){
            $query = Item::whereType('offer')->whereHas('category', function ($q) {
                $q->whereType('services');
            })->whereIsAvailable(1)->orderby('created_at','desc');
            $currency = env('APP_CURRENCY');
            $items = $query->take(50)->get()->map(function ($value, $key) use ($currency,$user) {
                $value->is_favorite = false;
                if($user){
                    if(UserFavorite::whereMetaName('items')->whereMetaId($value->id)->whereUserId($user->id)->first()){
                        $value->is_favorite = true;
                    }
                }
                $value->currency = $currency;
                $value->post_type = "services";
                return $value;
            })->values();
        }elseif ($slug == 'recently-viewed'){
            $query = Item::wherein('id',$items_viewed)->whereType('offer')->whereIsAvailable(1)->orderby('created_at','desc');
            $currency = env('APP_CURRENCY');
            $items = $query->take(50)->get()->map(function ($value, $key) use ($currency,$user) {
                $value->is_favorite = false;
                if($user){
                    if(UserFavorite::whereMetaName('items')->whereMetaId($value->id)->whereUserId($user->id)->first()){
                        $value->is_favorite = true;
                    }
                }
                unset($value->quantity);
                $value->currency = $currency;
                $value->post_type = $value->category->type;
                unset($value->category);
                return $value;
            })->values();
        }elseif ($slug == 'nearby'){
            if($latitude && $longitude){
                $query = Item::whereType('offer')->whereIsAvailable(1)->orderbyRaw("(6371 * 2 * ASIN(SQRT( POWER(SIN(( $latitude - latitude) *  pi()/180 / 2), 2) +COS( $latitude * pi()/180) * COS(latitude * pi()/180) * POWER(SIN(( $longitude - longitude) * pi()/180 / 2), 2) ))) asc");
                $currency = env('APP_CURRENCY');
                $items = $query->take(50)->get()->map(function ($value, $key) use ($currency,$user) {
                    $value->is_favorite = false;
                    if($user){
                        if(UserFavorite::whereMetaName('items')->whereMetaId($value->id)->whereUserId($user->id)->first()){
                            $value->is_favorite = true;
                        }
                    }
                    unset($value->quantity);
                    $value->currency = $currency;
                    $value->post_type = $value->category->type;
                    unset($value->category);
                    return $value;
                })->values();
            }
        }elseif ($slug == 'matches'){
            $query = Item::whereType('offer')->whereIsAvailable(1)->orderby('created_at','desc');
            $currency = env('APP_CURRENCY');
            $items = $query->take(50)->get()->map(function ($value, $key) use ($currency,$user) {
                $value->is_favorite = false;
                if($user){
                    if(UserFavorite::whereMetaName('items')->whereMetaId($value->id)->whereUserId($user->id)->first()){
                        $value->is_favorite = true;
                    }
                }
                unset($value->quantity);
                $value->currency = $currency;
                $value->post_type = $value->category->type;
                unset($value->category);
                return $value;
            })->values();
        }

        $limit = 20;
        $items = new LengthAwarePaginator(
            $items->forPage(LengthAwarePaginator::resolveCurrentPage(), $limit)->values(), $items->count(), $limit, LengthAwarePaginator::resolveCurrentPage(), ['path' => secure_url('/api/products/items/pagination')]
        );

        return response()->json($items);
    }

    public function all_items(Request $request) {
        $user = null;
        if($request->header('token')){
            $user = $this->validate_user($request);
        }
        $now = Carbon::now()->format('Y-m-d');
        $query = Item::whereType('offer')->whereDate('expired_at','>=',$now)->whereIsAvailable(1);
        if(Input::has('search')){
            $search = Input::get('search');
            $query->where(function($query) use ($search){
                $query->whereRaw("LOWER(name_en) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(name_ar) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(description_en) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(description_ar) like ? ", [ "%".strtolower($search)."%"]);
            });
        }
        $category_id = isset($request->category_id) ? $request->category_id : null;
        $sub_category_ids = isset($request->sub_category_ids) ? $request->sub_category_ids : null;
        $sub_sub_category_id = isset($request->sub_sub_category_id) ? $request->sub_sub_category_id : null;
        $filters_fields = isset($request->filters_fields) ? json_decode($request->filters_fields) : null;

        $query->when($category_id, function ($query) use ($category_id) {
            $query->whereCategoryId($category_id);
        });
        $query->when($sub_category_ids, function ($query) use ($sub_category_ids) {
            $query->whereIn('sub_category_id',$sub_category_ids);
        });
        $query->when($sub_sub_category_id, function ($query) use ($sub_sub_category_id) {
            $query->whereSubSubCategoryId($sub_sub_category_id);
        });
        $query->when($filters_fields, function ($query) use ($filters_fields) {
            $query->whereHas('custom_fields', function ($query) use ($filters_fields) {
                foreach ($filters_fields as $filter_field){
                    $query->where(function($query) use ($filters_fields,$filter_field){
                        $extra_field_name = isset($filter_field->extra_field_name) ? $filter_field->extra_field_name : null;
                        $extra_field_value = isset($filter_field->extra_field_value) ? $filter_field->extra_field_value : null;
                        /**
                         * special check for min/max
                         */
                        if (strpos($filter_field->field_name, 'min') !== false || strpos($filter_field->field_name, 'max') !== false){
                            if (strpos($filter_field->field_name, 'max') !== false){
                                $min_field_name = str_replace('max', 'min', $filter_field->field_name);
                                $query->where(function($query) use ($filter_field,$min_field_name){
                                    $query->where($filter_field->field_name,'>=',$filter_field->field_value)->where($min_field_name,'<=',$filter_field->field_value);
                                });
                            }else{
                                $max_field_name = str_replace('min', 'max', $filter_field->field_name);
                                $query->where(function($query) use ($filter_field,$max_field_name){
                                    $query->where($filter_field->field_name,'<=',$filter_field->field_value)->where($max_field_name,'>=',$filter_field->field_value);
                                });
                            }
                        }else{
                            $query->whereCustomFieldId($filter_field->id)->whereFieldValue($filter_field->field_value);
                        }

                        if ($extra_field_name && (strpos($extra_field_name, 'min') !== false || strpos($extra_field_name, 'max') !== false)){
                            if (strpos($extra_field_name, 'max') !== false){
                                $min_field_name = str_replace('max', 'min', $extra_field_name);
                                $query->where(function($query) use ($extra_field_name,$extra_field_value,$min_field_name){
                                    $query->where($extra_field_name,'>=',$extra_field_value)->where($min_field_name,'<=',$extra_field_value);
                                });
                            }else{
                                $max_field_name = str_replace('min', 'max', $filter_field->field_name);
                                $query->where(function($query) use ($extra_field_name,$extra_field_value,$max_field_name){
                                    $query->where($extra_field_name,'<=',$extra_field_value)->where($max_field_name,'>=',$extra_field_value);
                                });
                            }
                        }else{
                            if($extra_field_name){
                                $query->whereExtraFieldName($extra_field_name)->whereExtraFieldValue($extra_field_value);
                            }
                        }
                    });
                }
            });
        });
        if($request->sort){
            $sort = Input::get('sort');
            if($sort == 'price_lowest'){
                $query->orderBy('price','asc');
            }elseif ($sort == 'price_highest'){
                $query->orderBy('price','desc');
            }elseif ($sort == 'most_recent'){
                $query->orderBy('created_at','desc');
            }
        }else{
            $query->orderBy('item_order','desc');
        }
        if($request->latitude && $request->longitude){
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $items = $query->selectRaw("*,(6371 * 2 * ASIN(SQRT( POWER(SIN(( $latitude - latitude) *  pi()/180 / 2), 2) +COS( $latitude * pi()/180) * COS(latitude * pi()/180) * POWER(SIN(( $longitude - longitude) * pi()/180 / 2), 2) ))) as distance")->orderbyRaw("(6371 * 2 * ASIN(SQRT( POWER(SIN(( $latitude - latitude) *  pi()/180 / 2), 2) +COS( $latitude * pi()/180) * COS(latitude * pi()/180) * POWER(SIN(( $longitude - longitude) * pi()/180 / 2), 2) ))) asc");
        }else{
            $items = $query->selectRaw("*,NULL as distance");
        }
        $currency = env('APP_CURRENCY');
        $items = $items->get()->map(function ($value, $key) use ($currency,$user) {
            $value->is_favorite = false;
            if($user){
                if(UserFavorite::whereMetaName('items')->whereMetaId($value->id)->whereUserId($user->id)->first()){
                    $value->is_favorite = true;
                }
            }
            $value->return_post;
            $post_type = $value->category->type;
            unset($value->category);
            if($post_type == 'products'){
                unset($value->quantity);
            }

            $value->currency = $currency;
            $value->post_type = $post_type;
            $value->price_formatted = number_format($value->price,3) . ' ' . $currency;
            $value->discounted_price_formatted = $value->discounted_price ? number_format($value->discounted_price,3) . ' ' . $currency : null;
            $value->discount_amount_formatted = $value->discount_amount ? number_format($value->discount_amount,3) . ' ' . $currency : null;
            return $value;
        })->values();

        return response()->json($items);
    }

    public function all_items_with_pagination(Request $request) {
        $user = null;
        if($request->header('token')){
            $user = $this->validate_user($request);
        }
        $now = Carbon::now()->format('Y-m-d');
        $query = Item::whereType('offer')->whereDate('expired_at','>=',$now)->whereIsAvailable(1);
        if(Input::has('search')){
            $search = Input::get('search');
            $query->where(function($query) use ($search){
                $query->whereRaw("LOWER(name_en) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(name_ar) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(description_en) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(description_ar) like ? ", [ "%".strtolower($search)."%"]);
            });
        }
        $category_id = isset($request->category_id) ? $request->category_id : null;
        $sub_category_ids = isset($request->sub_category_ids) ? $request->sub_category_ids : null;
        $sub_sub_category_id = isset($request->sub_sub_category_id) ? $request->sub_sub_category_id : null;
        $filters_fields = isset($request->filters_fields) ? json_decode($request->filters_fields) : null;

        $query->when($category_id, function ($query) use ($category_id) {
            $query->whereCategoryId($category_id);
        });
        $query->when($sub_category_ids, function ($query) use ($sub_category_ids) {
            $query->whereIn('sub_category_id',$sub_category_ids);
        });
        $query->when($sub_sub_category_id, function ($query) use ($sub_sub_category_id) {
            $query->whereSubSubCategoryId($sub_sub_category_id);
        });
        $query->when($filters_fields, function ($query) use ($filters_fields) {
            $query->whereHas('custom_fields', function ($query) use ($filters_fields) {
                foreach ($filters_fields as $filter_field){
                    $query->where(function($query) use ($filters_fields,$filter_field){
                        $extra_field_name = isset($filter_field->extra_field_name) ? $filter_field->extra_field_name : null;
                        $extra_field_value = isset($filter_field->extra_field_value) ? $filter_field->extra_field_value : null;
                        /**
                         * special check for min/max
                         */
                        if (strpos($filter_field->field_name, 'min') !== false || strpos($filter_field->field_name, 'max') !== false){
                            if (strpos($filter_field->field_name, 'max') !== false){
                                $min_field_name = str_replace('max', 'min', $filter_field->field_name);
                                $query->where(function($query) use ($filter_field,$min_field_name){
                                    $query->where($filter_field->field_name,'>=',$filter_field->field_value)->where($min_field_name,'<=',$filter_field->field_value);
                                });
                            }else{
                                $max_field_name = str_replace('min', 'max', $filter_field->field_name);
                                $query->where(function($query) use ($filter_field,$max_field_name){
                                    $query->where($filter_field->field_name,'<=',$filter_field->field_value)->where($max_field_name,'>=',$filter_field->field_value);
                                });
                            }
                        }else{
                            $query->whereCustomFieldId($filter_field->id)->whereFieldValue($filter_field->field_value);
                        }

                        if ($extra_field_name && (strpos($extra_field_name, 'min') !== false || strpos($extra_field_name, 'max') !== false)){
                            if (strpos($extra_field_name, 'max') !== false){
                                $min_field_name = str_replace('max', 'min', $extra_field_name);
                                $query->where(function($query) use ($extra_field_name,$extra_field_value,$min_field_name){
                                    $query->where($extra_field_name,'>=',$extra_field_value)->where($min_field_name,'<=',$extra_field_value);
                                });
                            }else{
                                $max_field_name = str_replace('min', 'max', $filter_field->field_name);
                                $query->where(function($query) use ($extra_field_name,$extra_field_value,$max_field_name){
                                    $query->where($extra_field_name,'<=',$extra_field_value)->where($max_field_name,'>=',$extra_field_value);
                                });
                            }
                        }else{
                            if($extra_field_name){
                                $query->whereExtraFieldName($extra_field_name)->whereExtraFieldValue($extra_field_value);
                            }
                        }
                    });
                }
            });
        });
        if($request->sort){
            $sort = Input::get('sort');
            if($sort == 'price_lowest'){
                $query->orderBy('price','asc');
            }elseif ($sort == 'price_highest'){
                $query->orderBy('price','desc');
            }elseif ($sort == 'most_recent'){
                $query->orderBy('created_at','desc');
            }
        }else{
            $query->orderBy('item_order','desc');
        }
        if($request->latitude && $request->longitude){
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $items = $query->selectRaw("*,(6371 * 2 * ASIN(SQRT( POWER(SIN(( $latitude - latitude) *  pi()/180 / 2), 2) +COS( $latitude * pi()/180) * COS(latitude * pi()/180) * POWER(SIN(( $longitude - longitude) * pi()/180 / 2), 2) ))) as distance")->orderbyRaw("(6371 * 2 * ASIN(SQRT( POWER(SIN(( $latitude - latitude) *  pi()/180 / 2), 2) +COS( $latitude * pi()/180) * COS(latitude * pi()/180) * POWER(SIN(( $longitude - longitude) * pi()/180 / 2), 2) ))) asc");
        }else{
            $items = $query->selectRaw("*,NULL as distance");
        }
        $currency = env('APP_CURRENCY');
        $items = $items->get()->map(function ($value, $key) use ($currency,$user) {
            $value->is_favorite = false;
            if($user){
                if(UserFavorite::whereMetaName('items')->whereMetaId($value->id)->whereUserId($user->id)->first()){
                    $value->is_favorite = true;
                }
            }
            $value->return_post;
            $post_type = $value->category->type;
            unset($value->category);
            if($post_type == 'products'){
                unset($value->quantity);
            }

            $value->currency = $currency;
            $value->post_type = $post_type;

            $value->price_formatted = number_format($value->price,3) . ' ' . $currency;
            $value->discounted_price_formatted = $value->discounted_price ? number_format($value->discounted_price,3) . ' ' . $currency : null;
            $value->discount_amount_formatted = $value->discount_amount ? number_format($value->discount_amount,3) . ' ' . $currency : null;
            return $value;
        })->values();

        $limit = 20;
        $items = new LengthAwarePaginator(
            $items->forPage(LengthAwarePaginator::resolveCurrentPage(), $limit)->values(), $items->count(), $limit, LengthAwarePaginator::resolveCurrentPage(), ['path' => secure_url('/api/products/items/pagination')]
        );

        return response()->json($items);
    }

    public function product_items(Request $request) {
        $user = null;
        if($request->header('token')){
            $user = $this->validate_user($request);
        }
        $now = Carbon::now()->format('Y-m-d');
        $query = Item::whereType('offer')->whereDate('expired_at','>=',$now)->whereIsAvailable(1);
        if(Input::has('search')){
            $search = Input::get('search');
            $query->where(function($query) use ($search){
                $query->whereRaw("LOWER(name_en) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(name_ar) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(description_en) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(description_ar) like ? ", [ "%".strtolower($search)."%"]);
            });
        }
        $category_id = isset($request->category_id) ? $request->category_id : null;
        $sub_category_ids = isset($request->sub_category_ids) ? $request->sub_category_ids : null;
        $sub_sub_category_id = isset($request->sub_sub_category_id) ? $request->sub_sub_category_id : null;
        $filters_fields = isset($request->filters_fields) ? json_decode($request->filters_fields) : null;

        $query->when($category_id, function ($query) use ($category_id) {
            $query->whereCategoryId($category_id);
        });
        $query->when($sub_category_ids, function ($query) use ($sub_category_ids) {
            $query->whereIn('sub_category_id',$sub_category_ids);
        });
        $query->when($sub_sub_category_id, function ($query) use ($sub_sub_category_id) {
            $query->whereSubSubCategoryId($sub_sub_category_id);
        });

        $query->when($filters_fields, function ($query) use ($filters_fields) {
            $query->whereHas('custom_fields', function ($query) use ($filters_fields) {
                foreach ($filters_fields as $filter_field){
                    $query->where(function($query) use ($filters_fields,$filter_field){
                        $extra_field_name = isset($filter_field->extra_field_name) ? $filter_field->extra_field_name : null;
                        $extra_field_value = isset($filter_field->extra_field_value) ? $filter_field->extra_field_value : null;
                        /**
                         * special check for min/max
                         */
                        if (strpos($filter_field->field_name, 'min') !== false || strpos($filter_field->field_name, 'max') !== false){
                            if (strpos($filter_field->field_name, 'max') !== false){
                                $min_field_name = str_replace('max', 'min', $filter_field->field_name);
                                $query->where(function($query) use ($filter_field,$min_field_name){
                                    $query->where($filter_field->field_name,'>=',$filter_field->field_value)->where($min_field_name,'<=',$filter_field->field_value);
                                });
                            }else{
                                $max_field_name = str_replace('min', 'max', $filter_field->field_name);
                                $query->where(function($query) use ($filter_field,$max_field_name){
                                    $query->where($filter_field->field_name,'<=',$filter_field->field_value)->where($max_field_name,'>=',$filter_field->field_value);
                                });
                            }
                        }else{
                            $query->whereCustomFieldId($filter_field->id)->whereFieldValue($filter_field->field_value);
                        }

                        if ($extra_field_name && (strpos($extra_field_name, 'min') !== false || strpos($extra_field_name, 'max') !== false)){
                            if (strpos($extra_field_name, 'max') !== false){
                                $min_field_name = str_replace('max', 'min', $extra_field_name);
                                $query->where(function($query) use ($extra_field_name,$extra_field_value,$min_field_name){
                                    $query->where($extra_field_name,'>=',$extra_field_value)->where($min_field_name,'<=',$extra_field_value);
                                });
                            }else{
                                $max_field_name = str_replace('min', 'max', $filter_field->field_name);
                                $query->where(function($query) use ($extra_field_name,$extra_field_value,$max_field_name){
                                    $query->where($extra_field_name,'<=',$extra_field_value)->where($max_field_name,'>=',$extra_field_value);
                                });
                            }
                        }else{
                            if($extra_field_name){
                                $query->whereExtraFieldName($extra_field_name)->whereExtraFieldValue($extra_field_value);
                            }
                        }
                    });
                }
            });
        });
        if($request->sort){
            $sort = Input::get('sort');
            if($sort == 'price_lowest'){
                $query->orderBy('price','asc');
            }elseif ($sort == 'price_highest'){
                $query->orderBy('price','desc');
            }elseif ($sort == 'most_recent'){
                $query->orderBy('created_at','desc');
            }
        }else{
            $query->orderBy('item_order','desc');
        }
        if($request->latitude && $request->longitude){
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $items = $query->selectRaw("*,(6371 * 2 * ASIN(SQRT( POWER(SIN(( $latitude - latitude) *  pi()/180 / 2), 2) +COS( $latitude * pi()/180) * COS(latitude * pi()/180) * POWER(SIN(( $longitude - longitude) * pi()/180 / 2), 2) ))) as distance")->orderbyRaw("(6371 * 2 * ASIN(SQRT( POWER(SIN(( $latitude - latitude) *  pi()/180 / 2), 2) +COS( $latitude * pi()/180) * COS(latitude * pi()/180) * POWER(SIN(( $longitude - longitude) * pi()/180 / 2), 2) ))) asc");
        }else{
            $items = $query->selectRaw("*,NULL as distance");
        }
        $currency = env('APP_CURRENCY');
        $items = $items->get()->map(function ($value, $key) use ($currency,$user) {
            $value->is_favorite = false;
            if($user){
                if(UserFavorite::whereMetaName('items')->whereMetaId($value->id)->whereUserId($user->id)->first()){
                    $value->is_favorite = true;
                }
            }
            $value->return_post;
            unset($value->quantity);
            $value->currency = $currency;
            $value->post_type = "products";
            $value->price_formatted = number_format($value->price,3) . ' ' . $currency;
            $value->discounted_price_formatted = $value->discounted_price ? number_format($value->discounted_price,3) . ' ' . $currency : null;
            $value->discount_amount_formatted = $value->discount_amount ? number_format($value->discount_amount,3) . ' ' . $currency : null;
            return $value;
        })->values();
        return response()->json($items);
    }

    public function product_items_with_pagination(Request $request) {
        $user = null;
        if($request->header('token')){
            $user = $this->validate_user($request);
        }
        $now = Carbon::now()->format('Y-m-d');
        $query = Item::whereType('offer')->whereDate('expired_at','>=',$now)->whereIsAvailable(1);
        if(Input::has('search')){
            $search = Input::get('search');
            $query->where(function($query) use ($search){
                $query->whereRaw("LOWER(name_en) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(name_ar) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(description_en) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(description_ar) like ? ", [ "%".strtolower($search)."%"]);
            });
        }
        $category_id = isset($request->category_id) ? $request->category_id : null;
        $sub_category_ids = isset($request->sub_category_ids) ? $request->sub_category_ids : null;
        $sub_sub_category_id = isset($request->sub_sub_category_id) ? $request->sub_sub_category_id : null;
        $filters_fields = isset($request->filters_fields) ? json_decode($request->filters_fields) : null;

        $query->when($category_id, function ($query) use ($category_id) {
            $query->whereCategoryId($category_id);
        });
        $query->when($sub_category_ids, function ($query) use ($sub_category_ids) {
            $query->whereIn('sub_category_id',$sub_category_ids);
        });
        $query->when($sub_sub_category_id, function ($query) use ($sub_sub_category_id) {
            $query->whereSubSubCategoryId($sub_sub_category_id);
        });
        $query->when($filters_fields, function ($query) use ($filters_fields) {
            $query->whereHas('custom_fields', function ($query) use ($filters_fields) {
                foreach ($filters_fields as $filter_field){
                    $query->where(function($query) use ($filters_fields,$filter_field){
                        $extra_field_name = isset($filter_field->extra_field_name) ? $filter_field->extra_field_name : null;
                        $extra_field_value = isset($filter_field->extra_field_value) ? $filter_field->extra_field_value : null;
                        /**
                         * special check for min/max
                         */
                        if (strpos($filter_field->field_name, 'min') !== false || strpos($filter_field->field_name, 'max') !== false){
                            if (strpos($filter_field->field_name, 'max') !== false){
                                $min_field_name = str_replace('max', 'min', $filter_field->field_name);
                                $query->where(function($query) use ($filter_field,$min_field_name){
                                    $query->where($filter_field->field_name,'>=',$filter_field->field_value)->where($min_field_name,'<=',$filter_field->field_value);
                                });
                            }else{
                                $max_field_name = str_replace('min', 'max', $filter_field->field_name);
                                $query->where(function($query) use ($filter_field,$max_field_name){
                                    $query->where($filter_field->field_name,'<=',$filter_field->field_value)->where($max_field_name,'>=',$filter_field->field_value);
                                });
                            }
                        }else{
                            $query->whereCustomFieldId($filter_field->id)->whereFieldValue($filter_field->field_value);
                        }

                        if ($extra_field_name && (strpos($extra_field_name, 'min') !== false || strpos($extra_field_name, 'max') !== false)){
                            if (strpos($extra_field_name, 'max') !== false){
                                $min_field_name = str_replace('max', 'min', $extra_field_name);
                                $query->where(function($query) use ($extra_field_name,$extra_field_value,$min_field_name){
                                    $query->where($extra_field_name,'>=',$extra_field_value)->where($min_field_name,'<=',$extra_field_value);
                                });
                            }else{
                                $max_field_name = str_replace('min', 'max', $filter_field->field_name);
                                $query->where(function($query) use ($extra_field_name,$extra_field_value,$max_field_name){
                                    $query->where($extra_field_name,'<=',$extra_field_value)->where($max_field_name,'>=',$extra_field_value);
                                });
                            }
                        }else{
                            if($extra_field_name){
                                $query->whereExtraFieldName($extra_field_name)->whereExtraFieldValue($extra_field_value);
                            }
                        }
                    });
                }
            });
        });
        if($request->sort){
            $sort = Input::get('sort');
            if($sort == 'price_lowest'){
                $query->orderBy('price','asc');
            }elseif ($sort == 'price_highest'){
                $query->orderBy('price','desc');
            }elseif ($sort == 'most_recent'){
                $query->orderBy('created_at','desc');
            }
        }else{
            $query->orderBy('item_order','desc');
        }
        if($request->latitude && $request->longitude){
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $items = $query->selectRaw("*,(6371 * 2 * ASIN(SQRT( POWER(SIN(( $latitude - latitude) *  pi()/180 / 2), 2) +COS( $latitude * pi()/180) * COS(latitude * pi()/180) * POWER(SIN(( $longitude - longitude) * pi()/180 / 2), 2) ))) as distance")->orderbyRaw("(6371 * 2 * ASIN(SQRT( POWER(SIN(( $latitude - latitude) *  pi()/180 / 2), 2) +COS( $latitude * pi()/180) * COS(latitude * pi()/180) * POWER(SIN(( $longitude - longitude) * pi()/180 / 2), 2) ))) asc");
        }else{
            $items = $query->selectRaw("*,NULL as distance");
        }
        $currency = env('APP_CURRENCY');
        $items = $items->get()->map(function ($value, $key) use ($currency,$user) {
            $value->is_favorite = false;
            if($user){
                if(UserFavorite::whereMetaName('items')->whereMetaId($value->id)->whereUserId($user->id)->first()){
                    $value->is_favorite = true;
                }
            }
            $value->return_post;
            unset($value->quantity);
            $value->currency = $currency;
            $value->post_type = "products";
            $value->price_formatted = number_format($value->price,3) . ' ' . $currency;
            $value->discounted_price_formatted = $value->discounted_price ? number_format($value->discounted_price,3) . ' ' . $currency : null;
            $value->discount_amount_formatted = $value->discount_amount ? number_format($value->discount_amount,3) . ' ' . $currency : null;
            return $value;
        })->values();

        $limit = 20;
        $items = new LengthAwarePaginator(
            $items->forPage(LengthAwarePaginator::resolveCurrentPage(), $limit)->values(), $items->count(), $limit, LengthAwarePaginator::resolveCurrentPage(), ['path' => secure_url('/api/products/items/pagination')]
        );

        return response()->json($items);
    }

    public function service_items(Request $request) {
        $user = null;
        if($request->header('token')){
            $user = $this->validate_user($request);
        }
        $now = Carbon::now()->format('Y-m-d');
        $query = Item::whereType('offer')->whereDate('expired_at','>=',$now)->whereIsAvailable(1);
        if(Input::has('search')){
            $search = Input::get('search');
            $query->where(function($query) use ($search){
                $query->whereRaw("LOWER(name_en) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(name_ar) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(description_en) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(description_ar) like ? ", [ "%".strtolower($search)."%"]);
            });
        }
        $category_id = isset($request->category_id) ? $request->category_id : null;
        $sub_category_ids = isset($request->sub_category_ids) ? $request->sub_category_ids : null;
        $sub_sub_category_id = isset($request->sub_sub_category_id) ? $request->sub_sub_category_id : null;
        $filters_fields = isset($request->filters_fields) ? json_decode($request->filters_fields) : null;

        $query->when($category_id, function ($query) use ($category_id) {
            $query->whereCategoryId($category_id);
        });
        $query->when($sub_category_ids, function ($query) use ($sub_category_ids) {
            $query->whereIn('sub_category_id',$sub_category_ids);
        });
        $query->when($sub_sub_category_id, function ($query) use ($sub_sub_category_id) {
            $query->whereSubSubCategoryId($sub_sub_category_id);
        });
        $query->when($filters_fields, function ($query) use ($filters_fields) {
            $query->whereHas('custom_fields', function ($query) use ($filters_fields) {
                foreach ($filters_fields as $filter_field){
                    $query->where(function($query) use ($filters_fields,$filter_field){
                        $extra_field_name = isset($filter_field->extra_field_name) ? $filter_field->extra_field_name : null;
                        $extra_field_value = isset($filter_field->extra_field_value) ? $filter_field->extra_field_value : null;
                        /**
                         * special check for min/max
                         */
                        if (strpos($filter_field->field_name, 'min') !== false || strpos($filter_field->field_name, 'max') !== false){
                            if (strpos($filter_field->field_name, 'max') !== false){
                                $min_field_name = str_replace('max', 'min', $filter_field->field_name);
                                $query->where(function($query) use ($filter_field,$min_field_name){
                                    $query->where($filter_field->field_name,'>=',$filter_field->field_value)->where($min_field_name,'<=',$filter_field->field_value);
                                });
                            }else{
                                $max_field_name = str_replace('min', 'max', $filter_field->field_name);
                                $query->where(function($query) use ($filter_field,$max_field_name){
                                    $query->where($filter_field->field_name,'<=',$filter_field->field_value)->where($max_field_name,'>=',$filter_field->field_value);
                                });
                            }
                        }else{
                            $query->whereCustomFieldId($filter_field->id)->whereFieldValue($filter_field->field_value);
                        }

                        if ($extra_field_name && (strpos($extra_field_name, 'min') !== false || strpos($extra_field_name, 'max') !== false)){
                            if (strpos($extra_field_name, 'max') !== false){
                                $min_field_name = str_replace('max', 'min', $extra_field_name);
                                $query->where(function($query) use ($extra_field_name,$extra_field_value,$min_field_name){
                                    $query->where($extra_field_name,'>=',$extra_field_value)->where($min_field_name,'<=',$extra_field_value);
                                });
                            }else{
                                $max_field_name = str_replace('min', 'max', $filter_field->field_name);
                                $query->where(function($query) use ($extra_field_name,$extra_field_value,$max_field_name){
                                    $query->where($extra_field_name,'<=',$extra_field_value)->where($max_field_name,'>=',$extra_field_value);
                                });
                            }
                        }else{
                            if($extra_field_name){
                                $query->whereExtraFieldName($extra_field_name)->whereExtraFieldValue($extra_field_value);
                            }
                        }
                    });
                }
            });
        });
        if($request->sort){
            $sort = Input::get('sort');
            if($sort == 'price_lowest'){
                $query->orderBy('price','asc');
            }elseif ($sort == 'price_highest'){
                $query->orderBy('price','desc');
            }elseif ($sort == 'most_recent'){
                $query->orderBy('created_at','desc');
            }
        }else{
            $query->orderBy('item_order','desc');
        }
        if($request->latitude && $request->longitude){
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $items = $query->selectRaw("*,(6371 * 2 * ASIN(SQRT( POWER(SIN(( $latitude - latitude) *  pi()/180 / 2), 2) +COS( $latitude * pi()/180) * COS(latitude * pi()/180) * POWER(SIN(( $longitude - longitude) * pi()/180 / 2), 2) ))) as distance")->orderbyRaw("(6371 * 2 * ASIN(SQRT( POWER(SIN(( $latitude - latitude) *  pi()/180 / 2), 2) +COS( $latitude * pi()/180) * COS(latitude * pi()/180) * POWER(SIN(( $longitude - longitude) * pi()/180 / 2), 2) ))) asc");
        }else{
            $items = $query->selectRaw("*,NULL as distance");
        }
        $currency = env('APP_CURRENCY');
        $items = $items->get()->map(function ($value, $key) use ($currency,$user) {
            $value->is_favorite = false;
            if($user){
                if(UserFavorite::whereMetaName('items')->whereMetaId($value->id)->whereUserId($user->id)->first()){
                    $value->is_favorite = true;
                }
            }
            $value->return_post;
            $value->currency = $currency;
            $value->post_type = "services";
            $value->price_formatted = number_format($value->price,3) . ' ' . $currency;
            $value->discounted_price_formatted = $value->discounted_price ? number_format($value->discounted_price,3) . ' ' . $currency : null;
            $value->discount_amount_formatted = $value->discount_amount ? number_format($value->discount_amount,3) . ' ' . $currency : null;
            return $value;
        })->values();

        return response()->json($items);
    }

    public function service_items_with_pagination(Request $request) {
        $user = null;
        if($request->header('token')){
            $user = $this->validate_user($request);
        }
        $now = Carbon::now()->format('Y-m-d');
        $query = Item::whereType('offer')->whereDate('expired_at','>=',$now)->whereIsAvailable(1);
        if(Input::has('search')){
            $search = Input::get('search');
            $query->where(function($query) use ($search){
                $query->whereRaw("LOWER(name_en) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(name_ar) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(description_en) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(description_ar) like ? ", [ "%".strtolower($search)."%"]);
            });
        }
        $category_id = isset($request->category_id) ? $request->category_id : null;
        $sub_category_ids = isset($request->sub_category_ids) ? $request->sub_category_ids : null;
        $sub_sub_category_id = isset($request->sub_sub_category_id) ? $request->sub_sub_category_id : null;
        $filters_fields = isset($request->filters_fields) ? json_decode($request->filters_fields) : null;

        $query->when($category_id, function ($query) use ($category_id) {
            $query->whereCategoryId($category_id);
        });
        $query->when($sub_category_ids, function ($query) use ($sub_category_ids) {
            $query->whereIn('sub_category_id',$sub_category_ids);
        });
        $query->when($sub_sub_category_id, function ($query) use ($sub_sub_category_id) {
            $query->whereSubSubCategoryId($sub_sub_category_id);
        });
        $query->when($filters_fields, function ($query) use ($filters_fields) {
            $query->whereHas('custom_fields', function ($query) use ($filters_fields) {
                foreach ($filters_fields as $filter_field){
                    $query->where(function($query) use ($filters_fields,$filter_field){
                        $extra_field_name = isset($filter_field->extra_field_name) ? $filter_field->extra_field_name : null;
                        $extra_field_value = isset($filter_field->extra_field_value) ? $filter_field->extra_field_value : null;
                        /**
                         * special check for min/max
                         */
                        if (strpos($filter_field->field_name, 'min') !== false || strpos($filter_field->field_name, 'max') !== false){
                            if (strpos($filter_field->field_name, 'max') !== false){
                                $min_field_name = str_replace('max', 'min', $filter_field->field_name);
                                $query->where(function($query) use ($filter_field,$min_field_name){
                                    $query->where($filter_field->field_name,'>=',$filter_field->field_value)->where($min_field_name,'<=',$filter_field->field_value);
                                });
                            }else{
                                $max_field_name = str_replace('min', 'max', $filter_field->field_name);
                                $query->where(function($query) use ($filter_field,$max_field_name){
                                    $query->where($filter_field->field_name,'<=',$filter_field->field_value)->where($max_field_name,'>=',$filter_field->field_value);
                                });
                            }
                        }else{
                            $query->whereCustomFieldId($filter_field->id)->whereFieldValue($filter_field->field_value);
                        }

                        if ($extra_field_name && (strpos($extra_field_name, 'min') !== false || strpos($extra_field_name, 'max') !== false)){
                            if (strpos($extra_field_name, 'max') !== false){
                                $min_field_name = str_replace('max', 'min', $extra_field_name);
                                $query->where(function($query) use ($extra_field_name,$extra_field_value,$min_field_name){
                                    $query->where($extra_field_name,'>=',$extra_field_value)->where($min_field_name,'<=',$extra_field_value);
                                });
                            }else{
                                $max_field_name = str_replace('min', 'max', $filter_field->field_name);
                                $query->where(function($query) use ($extra_field_name,$extra_field_value,$max_field_name){
                                    $query->where($extra_field_name,'<=',$extra_field_value)->where($max_field_name,'>=',$extra_field_value);
                                });
                            }
                        }else{
                            if($extra_field_name){
                                $query->whereExtraFieldName($extra_field_name)->whereExtraFieldValue($extra_field_value);
                            }
                        }
                    });
                }
            });
        });
        if($request->sort){
            $sort = Input::get('sort');
            if($sort == 'price_lowest'){
                $query->orderBy('price','asc');
            }elseif ($sort == 'price_highest'){
                $query->orderBy('price','desc');
            }elseif ($sort == 'most_recent'){
                $query->orderBy('created_at','desc');
            }
        }else{
            $query->orderBy('item_order','desc');
        }
        if($request->latitude && $request->longitude){
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $items = $query->selectRaw("*,(6371 * 2 * ASIN(SQRT( POWER(SIN(( $latitude - latitude) *  pi()/180 / 2), 2) +COS( $latitude * pi()/180) * COS(latitude * pi()/180) * POWER(SIN(( $longitude - longitude) * pi()/180 / 2), 2) ))) as distance")->orderbyRaw("(6371 * 2 * ASIN(SQRT( POWER(SIN(( $latitude - latitude) *  pi()/180 / 2), 2) +COS( $latitude * pi()/180) * COS(latitude * pi()/180) * POWER(SIN(( $longitude - longitude) * pi()/180 / 2), 2) ))) asc");
        }else{
            $items = $query->selectRaw("*,NULL as distance");
        }
        $currency = env('APP_CURRENCY');
        $items = $items->get()->map(function ($value, $key) use ($currency,$user) {
            $value->is_favorite = false;
            if($user){
                if(UserFavorite::whereMetaName('items')->whereMetaId($value->id)->whereUserId($user->id)->first()){
                    $value->is_favorite = true;
                }
            }
            $value->return_post;
            $value->currency = $currency;
            $value->post_type = "services";
            $value->price_formatted = number_format($value->price,3) . ' ' . $currency;
            $value->discounted_price_formatted = $value->discounted_price ? number_format($value->discounted_price,3) . ' ' . $currency : null;
            $value->discount_amount_formatted = $value->discount_amount ? number_format($value->discount_amount,3) . ' ' . $currency : null;
            return $value;
        })->values();

        $limit = 20;
        $items = new LengthAwarePaginator(
            $items->forPage(LengthAwarePaginator::resolveCurrentPage(), $limit)->values(), $items->count(), $limit, LengthAwarePaginator::resolveCurrentPage(), ['path' => secure_url('/api/services/items/pagination')]
        );
        return response()->json($items);
    }

    public function filters(Request $request) {
        $query = CustomField::whereInBrowsingFilters(1);

        $category_id = isset($request->category_id) ? $request->category_id : null;
        $sub_category_id = isset($request->sub_category_id) ? $request->sub_category_id : null;
        $sub_sub_category_id = isset($request->sub_sub_category_id) ? $request->sub_sub_category_id : null;

        $query->when($category_id, function ($query) use ($category_id) {
            $query->whereCategoryId($category_id);
        });
        $query->when($sub_category_id, function ($query) use ($sub_category_id) {
            $query->whereSubCategoryId($sub_category_id);
        });
//        $query->when($sub_sub_category_id, function ($query) use ($sub_sub_category_id) {
//            $query->whereSubSubCategoryId($sub_sub_category_id);
//        });

        $filters = $query->orderby('item_order','asc')->get()->map(function ($value, $key){
            if($value->field_type == 'boolean'){
                $value->true_title = 'YES';
                $value->false_title = 'NO';
            }
            if($value->extra_field_type == 'boolean'){
                $value->extra_true_title = 'YES';
                $value->extra_false_title = 'NO';
            }
            if($value->field_name == 'is-sell'){
                $value->true_title = 'Sell';
                $value->false_title = 'Rent';
            }
            return $value;
        })->values();

        return response()->json($filters);
    }

    public function custom_fields(Request $request) {
        $is_return = isset($request->post_type) && $request->post_type == 'return' ? true : null;
        $query = $is_return ? CustomField::whereInReturn($is_return) : CustomField::whereIn('in_return',[0,1]);

        $category_id = isset($request->category_id) ? $request->category_id : null;
        $sub_category_id = isset($request->sub_category_id) ? $request->sub_category_id : null;
        $sub_sub_category_id = isset($request->sub_sub_category_id) ? $request->sub_sub_category_id : null;

        $query->when($category_id, function ($query) use ($category_id) {
            $query->whereCategoryId($category_id);
        });
        $query->when($sub_category_id, function ($query) use ($sub_category_id) {
            $query->whereSubCategoryId($sub_category_id);
        });
//        $query->when($sub_sub_category_id, function ($query) use ($sub_sub_category_id) {
//            $query->whereSubSubCategoryId($sub_sub_category_id);
//        });

        $filters = $query->orderby('item_order','asc')->get()->map(function ($value, $key) {
            $value->section = $value->in_filters ? 'filters' : 'regular';
            if($value->field_type == 'boolean'){
                $value->true_title = 'YES';
                $value->false_title = 'NO';
            }
            if($value->extra_field_type == 'boolean'){
                $value->extra_true_title = 'YES';
                $value->extra_false_title = 'NO';
            }
            if($value->field_name == 'is-sell'){
                $value->true_title = 'Sell';
                $value->false_title = 'Rent';
            }
            return $value;
        })->groupBy('section');

        if(!$filters->has('regular')){
            $filters->put('regular', []);
        }
        if(!$filters->has('filters')){
            $filters->put('filters', []);
        }
        return response()->json($filters);
    }

    public function product_single_item(Request $request) {
        $user = null;
        if($request->header('token')){
            $user = $this->validate_user($request);
            UserPostViewed::updateOrCreate([
                'user_id' => $user->id,
                'item_id' => $request->item_id
            ])->touch();
        }

        if(!Input::has('item_id')){
            return parent::return_error('Missing Item ID',401,'messages.missing_parameter');
        }
        $now = Carbon::now()->format('Y-m-d');
        /**
         * do not validate post here since could be clicked from my posts which could be expired or not available yet
         */
        if(!$item = Item::whereId($request->item_id)->with(['sub_category','sub_sub_category'])->first()){
            return parent::return_error("Invalid Item", 402, 'messages.invalid_parameters');
        }
        $currency = env('APP_CURRENCY');
        $item->is_favorite = false;
        $can_chat = false;
        if($user){
            if(UserFavorite::whereMetaName('items')->whereMetaId($item->id)->whereUserId($user->id)->first()){
                $item->is_favorite = true;
            }
            if(Barter::wherein('status',['completed','accepted'])->where(function ($query) use ($user) {
                $query->whereReceiverId($user->id)->orWhere('sender_id',$user->id);
            })->first()){
                $can_chat = true;
            }
        }


        $item->can_chat = $can_chat;
        $item->currency = $currency;
        $post_type = $item->category->type;
        $item->post_type = $post_type;
        unset($item->quantity);
        $item->custom_fields = CustomField::whereCategoryId($item->category_id)->whereSubCategoryId($item->sub_category_id)->orderby('item_order','asc')->get()->map(function ($value, $key) use ($item) {
            $value->section = $value->in_filters ? 'filters' : 'regular';
            if($value->field_type == 'boolean'){
                $value->true_title = 'YES';
                $value->false_title = 'NO';
            }
            if($value->extra_field_type && $value->extra_field_type == 'boolean'){
                $value->extra_true_title = 'YES';
                $value->extra_false_title = 'NO';
            }
            if($value->field_name == 'is-sell'){
                $value->true_title = 'Sell';
                $value->false_title = 'Rent';
            }
            $custom_field_value = ItemCustomField::whereFieldName($value->field_name)->whereItemId($item->id)->whereCustomFieldId($value->id)->first();
            $value->field_value = $custom_field_value ? $this->cast_field_value($custom_field_value->field_value, $value->field_type) : null;
            $field_display_name = null;
            if($custom_field_value && $value->field_type == 'list'){
                $list_item = CustomFieldList::whereId($value->field_value)->first();
                // whereFieldName($value->field_name)->
                if($list_item){
                    $field_display_name = $list_item->name_en;
                }
            }
            $value->field_display_name = $field_display_name;
            $custom_extra_field_value = null;
            if($value->has_extra_field){
                $custom_extra_field_value = ItemCustomField::whereExtraFieldName($value->extra_field_name)->whereItemId($item->id)->whereCustomFieldId($value->id)->first();
            }
            $value->extra_field_value = $custom_extra_field_value ? $this->cast_field_value($custom_extra_field_value->extra_field_value, $value->extra_field_type) : null;
            $extra_field_display_name = null;
            if($custom_extra_field_value && $value->extra_field_type == 'list'){
                $extra_list_item = CustomFieldList::whereFieldName($value->extra_field_name)->whereId($value->extra_field_value)->first();
                if($extra_list_item){
                    $extra_field_display_name = $extra_list_item->name_en;
                }
            }
            $value->extra_field_display_name = $extra_field_display_name;
            return $value;
        })->groupBy('section');
        if(!$item->custom_fields->has('regular')){
            $item->custom_fields->put('regular', []);
        }
        if(!$item->custom_fields->has('filters')){
            $item->custom_fields->put('filters', []);
        }
        $return_post = $item->return_post;
        if($return_post){
            $return_post_type = $return_post->category->type;
            $return_post->post_type = $return_post_type;
            $return_post->category_name = $return_post->category->name;
            $return_post->sub_category_name = $return_post->sub_category->name;
            $return_post->sub_sub_category_name = $return_post->sub_sub_category->name;
            $return_post->custom_fields = CustomField::whereInFilters(0)->whereInReturn(1)->whereCategoryId($return_post->category_id)->whereSubCategoryId($return_post->sub_category_id)->orderby('item_order','asc')->get()->map(function ($value, $key) use ($return_post) {
                $value->section = $value->in_filters ? 'filters' : 'regular';
                $custom_field_value = ItemCustomField::whereFieldName($value->field_name)->whereItemId($return_post->id)->whereCustomFieldId($value->id)->first();
                $value->field_value = $custom_field_value ? $this->cast_field_value($custom_field_value->field_value, $value->field_type) : null;
                $field_display_name = null;
                if($custom_field_value && $value->field_type == 'list'){
                    $list_item = CustomFieldList::whereId($value->field_value)->first();
                    // whereFieldName($value->field_name)->
                    if($list_item){
                        $field_display_name = $list_item->name_en;
                    }
                }
                $value->field_display_name = $field_display_name;
                $custom_extra_field_value = null;
                if($value->has_extra_field){
                    $custom_extra_field_value = ItemCustomField::whereExtraFieldName($value->extra_field_name)->whereItemId($return_post->id)->whereCustomFieldId($value->id)->first();
                }
                $value->extra_field_value = $custom_extra_field_value ? $this->cast_field_value($custom_extra_field_value->extra_field_value, $value->extra_field_type) : null;
                $extra_field_display_name = null;
                if($custom_extra_field_value && $value->extra_field_type == 'list'){
                    $extra_list_item = CustomFieldList::whereFieldName($value->extra_field_name)->whereId($value->extra_field_value)->first();
                    if($extra_list_item){
                        $extra_field_display_name = $extra_list_item->name_en;
                    }
                }
                $value->extra_field_display_name = $extra_field_display_name;
                return $value;
            })->groupBy('section');
            if(!$return_post->custom_fields->has('regular')){
                $return_post->custom_fields->put('regular', []);
            }
            if(!$return_post->custom_fields->has('filters')){
                $return_post->custom_fields->put('filters', []);
            }
        }
        if(!$user){
            $item->category;
            $item->sub_category;
            $item->sub_sub_category;
        }

        return response()->json($item);
    }

    public function service_single_item(Request $request) {
        $user = null;
        if($request->header('token')){
            $user = $this->validate_user($request);
            UserPostViewed::updateOrCreate([
                'user_id' => $user->id,
                'item_id' => $request->item_id
            ])->touch();
        }

        if(!Input::has('item_id')){
            return parent::return_error('Missing Item ID',401,'messages.missing_parameter');
        }
        $now = Carbon::now()->format('Y-m-d');
        /**
         * do not validate post here since could be clicked from my posts which could be expired or not available yet
         */
        if(!$item = Item::whereId($request->item_id)->with(['sub_category','sub_sub_category'])->first()){
            return parent::return_error("Invalid Item", 402, 'messages.invalid_parameters');
        }
        $currency = env('APP_CURRENCY');
        $item->is_favorite = false;
        $can_chat = false;
        if($user){
            if(UserFavorite::whereMetaName('items')->whereMetaId($item->id)->whereUserId($user->id)->first()){
                $item->is_favorite = true;
            }
            if(Barter::wherein('status',['completed','accepted'])->where(function ($query) use ($user) {
                $query->whereReceiverId($user->id)->orWhere('sender_id',$user->id);
            })->first()){
                $can_chat = true;
            }
        }


        $item->can_chat = $can_chat;
        $item->currency = $currency;
        $post_type = $item->category->type;
        $item->post_type = $post_type;
        unset($item->quantity);
        $item->custom_fields = CustomField::whereCategoryId($item->category_id)->whereSubCategoryId($item->sub_category_id)->orderby('item_order','asc')->get()->map(function ($value, $key) use ($item) {
            $value->section = $value->in_filters ? 'filters' : 'regular';
            if($value->field_type == 'boolean'){
                $value->true_title = 'YES';
                $value->false_title = 'NO';
            }
            if($value->extra_field_type && $value->extra_field_type == 'boolean'){
                $value->extra_true_title = 'YES';
                $value->extra_false_title = 'NO';
            }
            if($value->field_name == 'is-sell'){
                $value->true_title = 'Sell';
                $value->false_title = 'Rent';
            }
            $custom_field_value = ItemCustomField::whereFieldName($value->field_name)->whereItemId($item->id)->whereCustomFieldId($value->id)->first();
            $value->field_value = $custom_field_value ? $this->cast_field_value($custom_field_value->field_value, $value->field_type) : null;
            $field_display_name = null;
            if($custom_field_value && $value->field_type == 'list'){
                $list_item = CustomFieldList::whereId($value->field_value)->first();
                // whereFieldName($value->field_name)->
                if($list_item){
                    $field_display_name = $list_item->name_en;
                }
            }
            $value->field_display_name = $field_display_name;
            $custom_extra_field_value = null;
            if($value->has_extra_field){
                $custom_extra_field_value = ItemCustomField::whereExtraFieldName($value->extra_field_name)->whereItemId($item->id)->whereCustomFieldId($value->id)->first();
            }
            $value->extra_field_value = $custom_extra_field_value ? $this->cast_field_value($custom_extra_field_value->extra_field_value, $value->extra_field_type) : null;
            $extra_field_display_name = null;
            if($custom_extra_field_value && $value->extra_field_type == 'list'){
                $extra_list_item = CustomFieldList::whereFieldName($value->extra_field_name)->whereId($value->extra_field_value)->first();
                if($extra_list_item){
                    $extra_field_display_name = $extra_list_item->name_en;
                }
            }
            $value->extra_field_display_name = $extra_field_display_name;
            return $value;
        })->groupBy('section');
        if(!$item->custom_fields->has('regular')){
            $item->custom_fields->put('regular', []);
        }
        if(!$item->custom_fields->has('filters')){
            $item->custom_fields->put('filters', []);
        }
        $return_post = $item->return_post;
        if($return_post){
            $return_post_type = $return_post->category->type;
            $return_post->post_type = $return_post_type;
            $return_post->category_name = $return_post->category->name;
            $return_post->sub_category_name = $return_post->sub_category->name;
            $return_post->sub_sub_category_name = isset($return_post->sub_sub_category) ? $return_post->sub_sub_category->name : null;
            $return_post->custom_fields = CustomField::whereInFilters(0)->whereInReturn(1)->whereCategoryId($return_post->category_id)->whereSubCategoryId($return_post->sub_category_id)->orderby('item_order','asc')->get()->map(function ($value, $key) use ($return_post) {
                $value->section = $value->in_filters ? 'filters' : 'regular';
                $custom_field_value = ItemCustomField::whereFieldName($value->field_name)->whereItemId($return_post->id)->whereCustomFieldId($value->id)->first();
                $value->field_value = $custom_field_value ? $this->cast_field_value($custom_field_value->field_value, $value->field_type) : null;
                $field_display_name = null;
                if($custom_field_value && $value->field_type == 'list'){
                    $list_item = CustomFieldList::whereId($value->field_value)->first();
                    // whereFieldName($value->field_name)->
                    if($list_item){
                        $field_display_name = $list_item->name_en;
                    }
                }
                $value->field_display_name = $field_display_name;
                $custom_extra_field_value = null;
                if($value->has_extra_field){
                    $custom_extra_field_value = ItemCustomField::whereExtraFieldName($value->extra_field_name)->whereItemId($return_post->id)->whereCustomFieldId($value->id)->first();
                }
                $value->extra_field_value = $custom_extra_field_value ? $this->cast_field_value($custom_extra_field_value->extra_field_value, $value->extra_field_type) : null;
                $extra_field_display_name = null;
                if($custom_extra_field_value && $value->extra_field_type == 'list'){
                    $extra_list_item = CustomFieldList::whereFieldName($value->extra_field_name)->whereId($value->extra_field_value)->first();
                    if($extra_list_item){
                        $extra_field_display_name = $extra_list_item->name_en;
                    }
                }
                $value->extra_field_display_name = $extra_field_display_name;
                return $value;
            })->groupBy('section');
            if(!$return_post->custom_fields->has('regular')){
                $return_post->custom_fields->put('regular', []);
            }
            if(!$return_post->custom_fields->has('filters')){
                $return_post->custom_fields->put('filters', []);
            }
        }
        if(!$user){
            $item->category;
            $item->sub_category;
            $item->sub_sub_category;
        }

        return response()->json($item);
    }

    public function favorites(Request $request) {
        $user = $this->validate_user($request);
        $query = Category::whereIsPublished(1)->orderBy('item_order','asc');
        $favorites = UserFavorite::whereUserId($user->id)->whereMetaName('interests')->pluck('meta_id')->toarray();
        $categories = $query->get()->map(function ($value, $key) use ($favorites) {
            $value->is_favorite = false;
            if(in_array($value->id, $favorites)){
                $value->is_favorite = true;
            }
            return $value;
        })->groupBy('type');

        return response()->json($categories);
    }

    public function favorites_action(Request $request){
        $user = $this->validate_user($request);
        if(!Input::has('item_id')){
            return parent::return_error('Missing Category ID',401,'messages.missing_parameter');
        }
        if(!Input::has('action')){
            return parent::return_error('Missing Action',401,'messages.missing_parameter');
        }
        if(!in_array(Input::get('action'),['add','remove'])){
            return parent::return_error('Invalid Action Value',402,'messages.invalid_parameters');
        }
        if(!$item = Category::whereIsPublished(1)->whereId($request->item_id)->first()){
            return parent::return_error("Invalid Category", 402, 'messages.invalid_parameters');
        }
        $action = Input::get('action');
        $user_favorite = UserFavorite::whereMetaId($item->id)->whereUserId($user->id)->first();
        if($action == 'add'){
            if($user_favorite){
                return parent::return_error('Category already in Interest list',401,'Category already in Interest list');
            }

            UserFavorite::create([
                'user_id' => $user->id,
                'meta_id' => Input::get('item_id'),
                'meta_name' => 'interests',
                'meta_type' => $item->type
            ]);
        }else{
            if($user_favorite){
                $user_favorite->delete();
            }else{
                return parent::return_error('Category does not exist in Interest list',408,'Category does not exist in Interest list');
            }
        }
        return parent::return_json(['status' => 'success']);
    }

    public function item_favorites(Request $request) {
        $user = $this->validate_user($request);
        $search = isset($request->search) && !empty($request->search) ? trim($request->search) : null;
        $favorites = $user->favorites($search);

        return response()->json($favorites);
    }

    public function item_favorites_action(Request $request){
        $user = $this->validate_user($request);
        if(!Input::has('item_id')){
            return parent::return_error('Missing Category ID',401,'messages.missing_parameter');
        }
        if(!Input::has('action')){
            return parent::return_error('Missing Action',401,'messages.missing_parameter');
        }
        if(!in_array(Input::get('action'),['add','remove'])){
            return parent::return_error('Invalid Action Value',402,'messages.invalid_parameters');
        }
        if(!$item = Item::whereIsAvailable(1)->whereId($request->item_id)->first()){
            return parent::return_error("Invalid Item", 402, 'messages.invalid_parameters');
        }
        $action = Input::get('action');
        $user_favorite = UserFavorite::whereMetaId($item->id)->whereUserId($user->id)->first();
        if($action == 'add'){
            if($user_favorite){
                return parent::return_error('Item already in favorites list',401,'messages.already_favorite');
            }

            UserFavorite::create([
                'user_id' => $user->id,
                'meta_id' => Input::get('item_id'),
                'meta_name' => 'items',
                'meta_type' => null
            ]);
        }else{
            if($user_favorite){
                $user_favorite->delete();
            }else{
                return parent::return_error('Item does not exist in favorites list',408,'messages.item_not_exist_favorite');
            }
        }
        return parent::return_json(['status' => 'success']);
    }

    public function reserve_table(Request $request){
        $user = $this->validate_user($request);
        $validator = Validator::make($request->all(), [
            'item_id' => 'required',
            'event_type_id' => 'required',
            'persons' => 'required',
            'country_code' => 'required',
            'phone_number' => 'required',
            'reservation_datetime' => 'required',
        ]);

        if($validator->fails()){
            return parent::return_error('Missing parameter(s)', 400, $validator->messages()->all()[0]);
        }
        if(!$item = Restaurant::whereIsAvailable(1)->whereId($request->item_id)->first()){
            return parent::return_error("Invalid Restaurant", 402, 'messages.invalid_parameters');
        }
        if(!$event_type = RestaurantEventType::whereItemId($item->id)->whereIsPublished(1)->whereId($request->event_type_id)->first()){
            return parent::return_error("Invalid Restaurant", 402, 'messages.invalid_parameters');
        }
        if(!in_array($request->persons, $item->table_persons)){
            return parent::return_error("Invalid Persons number", 402, 'messages.invalid_parameters');
        }
        $new_country_code = null;
        $new_phone_number = null;
        $phone_number = $request->phone_number;
        $country_code = $request->country_code;
        $request->merge([ 'mobile_number' => $country_code.$phone_number]);

        $validator = Validator::make($request->all(), [
            'mobile_number' => 'phone:IQ,LB',
        ]);

        if ($validator->fails()) {
            return parent::return_error($validator->errors()->first('mobile_number'),403,$validator->errors()->first('mobile_number'));
        }
        $phone_details = PhoneNumber::make($request->mobile_number)->getPhoneNumberInstance();
        $new_country_code = '+'.$phone_details->getCountryCode();
        $new_phone_number = $phone_details->getNationalNumber();
        $user_reservation = UserReservation::whereUserId($user->id)->whereStatus('pending')->first();
        if($user_reservation){
            return parent::return_error('you already have a pending request',401,'you already have a pending request');
        }
        $notes = isset($request->notes) && !empty($request->notes) ? trim($request->notes) : null;
        $user_reservation = UserReservation::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'event_type_id' => $event_type->id,
            'status' => 'pending',
            'persons' => $request->persons,
            'reservation_datetime' => $request->reservation_datetime,
            'notes' => $notes,
            'country_code' => $new_country_code,
            'mobile_number' => $new_phone_number,
        ]);

        /**
         * send push to Merchant
         */
        $full_name = $user->first_name.' '.$user->last_name;
        $message = 'You have new request from '.$full_name;
        $title = 'New Reservation Request';
        if($merchant_push = UserPush::where('type','=','restaurant')->whereUsersId($item->id)->first()){
            $player_id = $merchant_push->player_id;
            $contents = ["en" => $message];
            $headings = ["en" => $title];

            $info['contents'] = $contents;
            $info['headings'] = $headings;
            $info['player_ids'] = [$player_id];
            $info['type'] = 'restaurant';
            $info['data']['type'] = 'new_reservation';
            if(isset($request->date)){
                $info['send_after'] = Carbon::createFromTimestamp($request->date)->format('Y-m-d H:i:s');
            }
            $result = parent::oneSignal($info);
        }
        $code = Controller::number_generator(6);
        Inbox::create([
            'user_type' => 'restaurant',
            'user_id' => $item->id,
            'title' => $title,
            'message' => $message,
            'image' => null,
            'type' => 'targeted',
            'code' => $code,
            'meta_type' => 'user_reservation',
            'meta_value' => $user_reservation->id,
        ]);

        return parent::return_json(['status' => 'Request has been sent successfully, Restaurant will contact you soon']);
    }

    public function review(Request $request){
        $user = $this->validate_user($request);
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:dining,delivery',
            'item_id' => 'required',
            'stars' => 'required|integer|between:1,5'
        ]);

        if($validator->fails()){
            return parent::return_error('Missing parameter(s)', 400, $validator->messages()->all()[0]);
        }
        if(!$item = Restaurant::whereIsAvailable(1)->whereId($request->item_id)->first()){
            return parent::return_error("Invalid Restaurant", 402, 'messages.invalid_parameters');
        }
        $tags_like = $request->like_ids;
        $tags_dislike = $request->dislike_ids;
        foreach ($tags_like as $tag_like){
            if(!ReviewTag::whereId($tag_like)->whereIsPublished(1)->whereType('like')->first()){
                return parent::return_error("Invalid Tag", 402, 'messages.invalid_parameters');
            }
        }
        foreach ($tags_dislike as $tag_dislike){
            if(!ReviewTag::whereId($tag_dislike)->whereIsPublished(1)->whereType('dislike')->first()){
                return parent::return_error("Invalid Tag", 402, 'messages.invalid_parameters');
            }
        }
        $fileName = null;
        $width = null;
        $height = null;
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images/rating';
        // check if a file exist and is valid.
        if($request->hasFile('image')) {
            if($request->file('image')->isValid()) {
                $file = $request->file('image');
                $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $fileName);
                $this->correctImageOrientation($destinationDir.'/'.$fileName);
                list($width, $height, $type, $attr) = getimagesize($destinationDir.'/'.$fileName);
            }else{
                return parent::return_error("Invalid Image", 402, 'messages.invalid_parameters');
            }
        }
        $description = isset($request->description) && !empty($request->description) ? trim($request->description) : null;
        UserRating::updateOrCreate(['user_id' => $user->id,'type' => $request->type,'item_id' => $item->id],[
            'stars' => $request->stars,
            'tags_like' => $tags_like,
            'tags_dislike' => $tags_dislike,
            'description' => $description,
            'image' => $fileName,
            'width' => $width,
            'height' => $height,
        ]);

        return parent::return_json(['status' => 'Thank you for your review']);
    }

    public function review_action(Request $request){
        $user = $this->validate_user($request);
        if(!Input::has('review_id')){
            return parent::return_error('Missing Restaurant ID',401,'messages.missing_parameter');
        }
        if(!Input::has('action')){
            return parent::return_error('Missing Action',401,'messages.missing_parameter');
        }
        if(!in_array(Input::get('action'),['add','remove'])){
            return parent::return_error('Invalid Action Value',402,'messages.invalid_parameters');
        }

        if(!$item = UserRating::whereId($request->review_id)->first()){
            return parent::return_error("Invalid Review", 402, 'messages.invalid_parameters');
        }
//        if($item->user_id == $user->id){
//            return parent::return_error("you cannot like your own review", 402, "you cannot like your own review");
//        }
        $action = Input::get('action');
        $user_review = UserRatingLiker::whereRatingId($item->id)->whereLikerId($user->id)->first();
        if($action == 'add'){
            if($user_review){
                return parent::return_error('Review already liked',402,'Review already liked');
            }

            UserRatingLiker::create([
                'liker_id' => $user->id,
                'user_id' => $item->user_id,
                'rating_id' => $item->id,
            ]);
            $item->nb_likes = $item->nb_likes + 1;
            $item->save();
        }else{
            if($user_review){
                $user_review->delete();
                $item->nb_likes = $item->nb_likes - 1;
                $item->save();
            }else{

            }
        }
        return parent::return_json(['status' => 'success']);
    }

}
