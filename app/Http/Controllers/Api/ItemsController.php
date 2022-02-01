<?php

namespace App\Http\Controllers\Api;


use App\Models\Api\BodyType;
use App\Models\Api\Brand;
use App\Models\Api\Category;
use App\Models\Api\CollectionCategory;
use App\Models\Api\Color;
use App\Models\Api\Condition;
use App\Models\Api\CostLevel;
use App\Models\Api\Craving;
use App\Models\Api\Cuisine;
use App\Models\Api\CuratedCollection;
use App\Models\Api\CustomField;
use App\Models\Api\DoorType;
use App\Models\Api\FuelType;
use App\Models\Api\Inbox;
use App\Models\Api\Item;
use App\Models\Api\ItemCustomField;
use App\Models\Api\PackageItem;
use App\Models\Api\RentPeriod;
use App\Models\Api\Restaurant;
use App\Models\Api\ProductType;
use App\Models\Api\RestaurantEventType;
use App\Models\Api\ReviewTag;
use App\Models\Api\Service;
use App\Models\Api\SubCategory;
use App\Models\Api\SupplierFoodGroup;
use App\Models\Api\SupplierSubCategory;
use App\Models\Api\Tag;
use App\Models\Api\TransmissionType;
use App\Models\Api\UserFavorite;
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

    public function conditions(Request $request){
        $data = Condition::get();
        return response()->json($data);
    }
    public function fuel_types(Request $request){
        $data = FuelType::get();
        return response()->json($data);
    }
    public function body_types(Request $request){
        $data = BodyType::get();
        return response()->json($data);
    }
    public function transmission_types(Request $request){
        $data = TransmissionType::get();
        return response()->json($data);
    }
    public function door_types(Request $request){
        $data = DoorType::get();
        return response()->json($data);
    }
    public function colors(Request $request){
        $data = Color::get();
        return response()->json($data);
    }
    public function rent_periods(Request $request){
        $data = RentPeriod::get();
        return response()->json($data);
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
        $query = SubCategory::whereIsPublished(1)->orderBy('item_order','asc');
        if(Input::has('category_id')){
            $category_id = Input::get('category_id');
            $query->where(function($query) use ($category_id){
                $query->whereCategoryId($category_id);
            });
        }else{
            $query->whereIsFeatured(1);
        }
        $categories = $query->get();
        return response()->json($categories);
    }

    public function product_items(Request $request) {
        $query = Item::whereIsAvailable(1);
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
        $sub_category_id = isset($request->sub_category_id) ? $request->sub_category_id : null;
        $sub_sub_category_id = isset($request->sub_sub_category_id) ? $request->sub_sub_category_id : null;

        $query->when($category_id, function ($query) use ($category_id) {
            $query->whereCategoryId($category_id);
        });
        $query->when($sub_category_id, function ($query) use ($sub_category_id) {
            $query->whereSubCategoryId($sub_category_id);
        });
        $query->when($sub_sub_category_id, function ($query) use ($sub_sub_category_id) {
            $query->whereSubSubCategoryId($sub_sub_category_id);
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
        $currency = env('APP_CURRENCY');
        $items = $query->orderby('item_order','asc')->get()->map(function ($value, $key) use ($currency) {
            unset($value->quantity);
            $value->currency = $currency;
            $value->price_formatted = number_format($value->price,3) . ' ' . $currency;
            $value->discounted_price_formatted = $value->discounted_price ? number_format($value->discounted_price,3) . ' ' . $currency : null;
            $value->discount_amount_formatted = $value->discount_amount ? number_format($value->discount_amount,3) . ' ' . $currency : null;
//            $value->custom_fields = CustomField::whereCategoryId($value->category_id)->whereSubCategoryId($value->sub_category_id)->get();
            return $value;
        })->values();;
        $user = null;
        if($request->header('token')){
//            $user = $this->validate_user($request);
        }

        return response()->json($items);
    }

    public function product_items_with_pagination(Request $request) {
        $query = Item::whereIsAvailable(1);
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
        $sub_category_id = isset($request->sub_category_id) ? $request->sub_category_id : null;
        $sub_sub_category_id = isset($request->sub_sub_category_id) ? $request->sub_sub_category_id : null;

        $query->when($category_id, function ($query) use ($category_id) {
            $query->whereCategoryId($category_id);
        });
        $query->when($sub_category_id, function ($query) use ($sub_category_id) {
            $query->whereSubCategoryId($sub_category_id);
        });
        $query->when($sub_sub_category_id, function ($query) use ($sub_sub_category_id) {
            $query->whereSubSubCategoryId($sub_sub_category_id);
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
        $currency = env('APP_CURRENCY');
        $items = $query->orderby('item_order','asc')->get()->map(function ($value, $key) use ($currency) {
            unset($value->quantity);
            $value->currency = $currency;
            $value->price_formatted = number_format($value->price,3) . ' ' . $currency;
            $value->discounted_price_formatted = $value->discounted_price ? number_format($value->discounted_price,3) . ' ' . $currency : null;
            $value->discount_amount_formatted = $value->discount_amount ? number_format($value->discount_amount,3) . ' ' . $currency : null;
//            $value->custom_fields = CustomField::whereCategoryId($value->category_id)->whereSubCategoryId($value->sub_category_id)->get();
            return $value;
        })->values();
        $user = null;
        if($request->header('token')){
//            $user = $this->validate_user($request);
        }
        $limit = 20;
        $items = new LengthAwarePaginator(
            $items->forPage(LengthAwarePaginator::resolveCurrentPage(), $limit)->values(), $items->count(), $limit, LengthAwarePaginator::resolveCurrentPage(), ['path' => secure_url('/api/products/items/pagination')]
        );

        return response()->json($items);
    }

    public function service_items(Request $request) {
        $query = Item::whereIsAvailable(1);
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
        $sub_category_id = isset($request->sub_category_id) ? $request->sub_category_id : null;
        $sub_sub_category_id = isset($request->sub_sub_category_id) ? $request->sub_sub_category_id : null;

        $query->when($category_id, function ($query) use ($category_id) {
            $query->whereCategoryId($category_id);
        });
        $query->when($sub_category_id, function ($query) use ($sub_category_id) {
            $query->whereSubCategoryId($sub_category_id);
        });
        $query->when($sub_sub_category_id, function ($query) use ($sub_sub_category_id) {
            $query->whereSubSubCategoryId($sub_sub_category_id);
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
        $currency = env('APP_CURRENCY');
        $items = $query->orderby('item_order','asc')->get()->map(function ($value, $key) use ($currency) {
//            $value->custom_fields = CustomField::whereCategoryId($value->category_id)->whereSubCategoryId($value->sub_category_id)->get();
            $value->currency = $currency;
            $value->price_formatted = number_format($value->price,3) . ' ' . $currency;
            $value->discounted_price_formatted = $value->discounted_price ? number_format($value->discounted_price,3) . ' ' . $currency : null;
            $value->discount_amount_formatted = $value->discount_amount ? number_format($value->discount_amount,3) . ' ' . $currency : null;
            return $value;
        })->values();;
        $user = null;
        if($request->header('token')){
//            $user = $this->validate_user($request);
        }

        return response()->json($items);
    }

    public function service_items_with_pagination(Request $request) {
        $query = Item::whereIsAvailable(1);
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
        $sub_category_id = isset($request->sub_category_id) ? $request->sub_category_id : null;
        $sub_sub_category_id = isset($request->sub_sub_category_id) ? $request->sub_sub_category_id : null;

        $query->when($category_id, function ($query) use ($category_id) {
            $query->whereCategoryId($category_id);
        });
        $query->when($sub_category_id, function ($query) use ($sub_category_id) {
            $query->whereSubCategoryId($sub_category_id);
        });
        $query->when($sub_sub_category_id, function ($query) use ($sub_sub_category_id) {
            $query->whereSubSubCategoryId($sub_sub_category_id);
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
        $currency = env('APP_CURRENCY');
        $items = $query->orderby('item_order','asc')->get()->map(function ($value, $key) use ($currency) {
//            $value->custom_fields = CustomField::whereCategoryId($value->category_id)->whereSubCategoryId($value->sub_category_id)->get();
            $value->currency = $currency;
            $value->price_formatted = number_format($value->price,3) . ' ' . $currency;
            $value->discounted_price_formatted = $value->discounted_price ? number_format($value->discounted_price,3) . ' ' . $currency : null;
            $value->discount_amount_formatted = $value->discount_amount ? number_format($value->discount_amount,3) . ' ' . $currency : null;
            return $value;
        })->values();;
        $user = null;
        if($request->header('token')){
//            $user = $this->validate_user($request);
        }
        $limit = 20;
        $items = new LengthAwarePaginator(
            $items->forPage(LengthAwarePaginator::resolveCurrentPage(), $limit)->values(), $items->count(), $limit, LengthAwarePaginator::resolveCurrentPage(), ['path' => secure_url('/api/services/items/pagination')]
        );
        return response()->json($items);
    }

    public function filters(Request $request) {
        $query = CustomField::whereInFilters(1);

        $category_id = isset($request->category_id) ? $request->category_id : null;
        $sub_category_id = isset($request->sub_category_id) ? $request->sub_category_id : null;
        $sub_sub_category_id = isset($request->sub_sub_category_id) ? $request->sub_sub_category_id : null;

        $query->when($category_id, function ($query) use ($category_id) {
            $query->whereCategoryId($category_id);
        });
        $query->when($sub_category_id, function ($query) use ($sub_category_id) {
            $query->whereSubCategoryId($sub_category_id);
        });
        $query->when($sub_sub_category_id, function ($query) use ($sub_sub_category_id) {
            $query->whereSubSubCategoryId($sub_sub_category_id);
        });

        $filters = $query->orderby('item_order','asc')->get();

        return response()->json($filters);
    }

    public function custom_fields(Request $request) {
        $is_return = isset($request->post_type) && $request->post_type == 'return' ? true : false;
        $query = CustomField::whereInReturn($is_return);

        $category_id = isset($request->category_id) ? $request->category_id : null;
        $sub_category_id = isset($request->sub_category_id) ? $request->sub_category_id : null;
        $sub_sub_category_id = isset($request->sub_sub_category_id) ? $request->sub_sub_category_id : null;

        $query->when($category_id, function ($query) use ($category_id) {
            $query->whereCategoryId($category_id);
        });
        $query->when($sub_category_id, function ($query) use ($sub_category_id) {
            $query->whereSubCategoryId($sub_category_id);
        });
        $query->when($sub_sub_category_id, function ($query) use ($sub_sub_category_id) {
            $query->whereSubSubCategoryId($sub_sub_category_id);
        });

        $filters = $query->orderby('item_order','asc')->get();

        return response()->json($filters);
    }

    public function product_single_item(Request $request) {
        if(!Input::has('item_id')){
            return parent::return_error('Missing Item ID',401,'messages.missing_parameter');
        }
        if(!$item = Item::whereIsAvailable(1)->whereId($request->item_id)->first()){
            return parent::return_error("Invalid Item", 402, 'messages.invalid_parameters');
        }
        $currency = env('APP_CURRENCY');
        $user = null;
        if($request->header('token')){
//            $user = $this->validate_user($request);
        }
        $item->currency = $currency;
        $item->price_formatted = number_format($item->price,3) . ' ' . $currency;
        $item->discounted_price_formatted = $item->discounted_price ? number_format($item->discounted_price,3) . ' ' . $currency : null;
        $item->discount_amount_formatted = $item->discount_amount ? number_format($item->discount_amount,3) . ' ' . $currency : null;
        unset($item->quantity);
        $item->custom_fields = CustomField::whereCategoryId($item->category_id)->whereSubCategoryId($item->sub_category_id)->orderby('item_order','asc')->get()->map(function ($value, $key) use ($item) {
            $custom_field_value = ItemCustomField::whereFieldName($value->field_name)->whereItemId($item->id)->whereCustomFieldId($value->id)->first();
            $value->field_value = $custom_field_value ? $this->cast_field_value($custom_field_value->field_value, $value->field_type) : null;
            $custom_extra_field_value = null;
            if($value->has_extra_field){
                $custom_extra_field_value = ItemCustomField::whereExtraFieldName($value->extra_field_name)->whereItemId($item->id)->whereCustomFieldId($value->id)->first();
            }
            $value->extra_field_value = $custom_extra_field_value ? $this->cast_field_value($custom_extra_field_value->extra_field_value, $value->extra_field_type) : null;
            return $value;
        })->values();
        return response()->json($item);
    }

    public function service_single_item(Request $request) {
        if(!Input::has('item_id')){
            return parent::return_error('Missing Item ID',401,'messages.missing_parameter');
        }
        if(!$item = Item::whereIsAvailable(1)->whereId($request->item_id)->first()){
            return parent::return_error("Invalid Item", 402, 'messages.invalid_parameters');
        }
        $currency = env('APP_CURRENCY');
        $user = null;
        if($request->header('token')){
//            $user = $this->validate_user($request);
        }
        $item->currency = $currency;
        $item->price_formatted = number_format($item->price,3) . ' ' . $currency;
        $item->discounted_price_formatted = $item->discounted_price ? number_format($item->discounted_price,3) . ' ' . $currency : null;
        $item->discount_amount_formatted = $item->discount_amount ? number_format($item->discount_amount,3) . ' ' . $currency : null;
        unset($item->quantity);
        $item->custom_fields = CustomField::whereCategoryId($item->category_id)->whereSubCategoryId($item->sub_category_id)->orderby('item_order','asc')->get()->map(function ($value, $key) use ($item) {
            $custom_field_value = ItemCustomField::whereFieldName($value->field_name)->whereItemId($item->id)->whereCustomFieldId($value->id)->first();
            $value->field_value = $custom_field_value ? $this->cast_field_value($custom_field_value->field_value, $value->field_type) : null;
            $custom_extra_field_value = null;
            if($value->has_extra_field){
                $custom_extra_field_value = ItemCustomField::whereExtraFieldName($value->extra_field_name)->whereItemId($item->id)->whereCustomFieldId($value->id)->first();
            }
            $value->extra_field_value = $custom_extra_field_value ? $this->cast_field_value($custom_extra_field_value->extra_field_value, $value->extra_field_type) : null;
            return $value;
        })->values();
        return response()->json($item);
    }


    public function gifts(Request $request) {
        $user = $this->validate_user($request);
        $query = Restaurant::whereIsAvailable(1)->whereIsGift(1);
        if(Input::has('search')){
            $search = Input::get('search');
            $query->where(function($query) use ($search){
                $query->whereRaw("LOWER(name) like ? ", [ "%".strtolower($search)."%"]);
            });
        }
        if($request->sort){
            $sort = Input::get('sort');
            if($sort == 'low_to_high'){
                $query->orderBy('price','asc');
            }elseif ($sort == 'high_to_low'){
                $query->orderBy('price','desc');
            }elseif ($sort == 'most_recent'){
                $query->orderBy('created_at','desc');
            }
        }else{
            $query->orderBy('item_order','asc');
        }

        $items = $query->get();
//                foreach ($items as $item){
//                    $item->is_favorite = false;
//                    if(UserFavorite::whereMetaName($section)->whereMetaId($item->id)->whereUserId($user->id)->first()){
//                        $item->is_favorite = true;
//                    }
//                }
        return response()->json($items);

    }

    public function favorites(Request $request) {
        $user = $this->validate_user($request);
        $search = isset($request->search) && !empty($request->search) ? trim($request->search) : null;
        $favorites = $user->favorites($search);
        $currency = env('APP_CURRENCY');
        foreach ($favorites as $item){
            $item->out_of_stock = false;
            if ($item->quantity < 1) {
                $item->out_of_stock = true;
            }
            if($package_item = PackageItem::whereNull('order_id')->whereItemId($item->id)->whereUserId($user->id)->first()) {
                if ($item->quantity <= $package_item->quantity) {
                    $item->out_of_stock = true;
                }
            }
            $item->currency = $currency;
            $item->price_formatted = number_format($item->price,3) . ' ' . $currency;
            $item->discounted_price_formatted = $item->discounted_price ? number_format($item->discounted_price,3) . ' ' . $currency : null;
            $item->discount_amount_formatted = $item->discount_amount ? number_format($item->discount_amount,3) . ' ' . $currency : null;
            $item->is_favorite = true;
        }
        return response()->json($favorites);
    }


    public function favorites_action(Request $request){
        $user = $this->validate_user($request);
        if(!Input::has('item_id')){
            return parent::return_error('Missing Restaurant ID',401,'messages.missing_parameter');
        }
        if(!Input::has('action')){
            return parent::return_error('Missing Action',401,'messages.missing_parameter');
        }
        if(!in_array(Input::get('action'),['add','remove'])){
            return parent::return_error('Invalid Action Value',402,'messages.invalid_parameters');
        }
        if(!$item = Restaurant::whereIsAvailable(1)->whereId($request->item_id)->first()){
            return parent::return_error("Invalid Restaurant", 402, 'messages.invalid_parameters');
        }
        $action = Input::get('action');
        $user_favorite = UserFavorite::whereMetaId($item->id)->whereUserId($user->id)->first();
        if($action == 'add'){
            if($user_favorite){
                return parent::return_error('ITem already in favorites list',401,'messages.already_favorite');
            }

            UserFavorite::create([
                'user_id' => $user->id,
                'meta_id' => Input::get('item_id'),
                'meta_name' => 'restaurants'
            ]);
        }else{
            if($user_favorite){
                $user_favorite->delete();
            }else{
                return parent::return_error('Restaurant does not exist in favorites list',408,'messages.item_not_exist_favorite');
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
