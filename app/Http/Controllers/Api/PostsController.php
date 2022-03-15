<?php

namespace App\Http\Controllers\Api;


use App\Models\Api\Category;
use App\Models\Api\CustomField;
use App\Models\Api\CustomFieldList;
use App\Models\Api\Inbox;
use App\Models\Api\Item;
use App\Models\Api\ItemCustomField;
use App\Models\Api\Location;
use App\Models\Api\Media;
use App\Models\Api\Restaurant;
use App\Models\Api\RestaurantEventType;
use App\Models\Api\ReviewTag;
use App\Models\Api\SubCategory;
use App\Models\Api\SubSubCategory;
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
 * Class PostsController
 * @package App\Http\Controllers\Api
 */
class PostsController extends Controller
{
    use HITrait;


    public function create(Request $request){
        $user = $this->validate_user($request);
        if(!Input::has('category_id')){
            return parent::return_error('Missing Category ID',401,'messages.missing_parameter');
        }
        if(!$category = Category::whereIsPublished(1)->whereId($request->category_id)->first()){
            return parent::return_error("Invalid Category", 402, 'messages.invalid_parameters');
        }
        if(!Input::has('sub_category_id')){
            return parent::return_error('Missing Sub Category ID',401,'messages.missing_parameter');
        }
        if(!$sub_category = SubCategory::whereIsPublished(1)->whereId($request->sub_category_id)->first()){
            return parent::return_error("Invalid Sub Category", 402, 'messages.invalid_parameters');
        }
        if(!Input::has('sub_sub_category_id')){
            return parent::return_error('Missing Sub Sub Category ID',401,'messages.missing_parameter');
        }
        if(!$sub_sub_category = SubSubCategory::whereIsPublished(1)->whereId($request->sub_sub_category_id)->first()){
            return parent::return_error("Invalid Sub Sub Category", 402, 'messages.invalid_parameters');
        }
        if(!Input::has('location_id')){
            return parent::return_error('Missing Location ID',401,'messages.missing_parameter');
        }
        if(!$location = Location::whereId($request->location_id)->first()){
            return parent::return_error("Invalid Location", 402, 'messages.invalid_parameters');
        }
        if(!Input::has('title')){
            return parent::return_error('Missing Title',401,'messages.missing_parameter');
        }
//        if(!Input::has('description')){
//            return parent::return_error('Missing Description',401,'messages.missing_parameter');
//        }
        $description = isset($request->description) && !empty($request->description) ? $request->description : null;
        if(!Input::has('price')){
            return parent::return_error('Missing Price',401,'messages.missing_parameter');
        }
        $regular_fields = [];
        if(Input::has('regular_fields')){
            $regular_fields = json_decode($request->regular_fields);
        }
        $filters_fields = [];
        if(Input::has('filters_fields')){
            $filters_fields = json_decode($request->filters_fields);
        }
        if($request->with_return == 'yes'){
            if(!Input::has('return_category_id')){
                return parent::return_error('Missing Return Category ID',401,'messages.missing_parameter');
            }
            if(!$return_category = Category::whereIsPublished(1)->whereId($request->return_category_id)->first()){
                return parent::return_error("Invalid Return Category", 402, 'messages.invalid_parameters');
            }
            if(!Input::has('return_sub_category_id')){
                return parent::return_error('Missing Return Sub Category ID',401,'messages.missing_parameter');
            }
            if(!$return_sub_category = SubCategory::whereIsPublished(1)->whereId($request->return_sub_category_id)->first()){
                return parent::return_error("Invalid Return Sub Category", 402, 'messages.invalid_parameters');
            }
            if(!Input::has('return_sub_sub_category_id')){
                return parent::return_error('Missing Return Sub Sub Category ID',401,'messages.missing_parameter');
            }
            if(!$return_sub_sub_category = SubSubCategory::whereIsPublished(1)->whereId($request->return_sub_sub_category_id)->first()){
                return parent::return_error("Invalid Return Sub Sub Category", 402, 'messages.invalid_parameters');
            }
            if(!Input::has('return_location_id')){
                return parent::return_error('Missing Return Location ID',401,'messages.missing_parameter');
            }
            if(!$return_location = Location::whereId($request->return_location_id)->first()){
                return parent::return_error("Invalid Return Location", 402, 'messages.invalid_parameters');
            }
            if(!Input::has('return_title')){
                return parent::return_error('Missing Return Title',401,'messages.missing_parameter');
            }
//            if(!Input::has('return_description')){
//                return parent::return_error('Missing Return Description',401,'messages.missing_parameter');
//            }
            $return_description = isset($request->return_description) && !empty($request->return_description) ? $request->return_description : null;
            if(!Input::has('return_price')){
                return parent::return_error('Missing Return Price',401,'messages.missing_parameter');
            }
            $return_regular_fields = [];
            if(Input::has('return_regular_fields')){
                $return_regular_fields = json_decode($request->return_regular_fields);
            }
        }
        $latitude = isset($request->latitude) ? $request->latitude : null;
        $longitude = isset($request->longitude) ? $request->longitude : null;
        $max_order = DB::table('items')->whereCategoryId($category->id)->whereSubCategoryId($sub_category->id)->whereSubSubCategoryId($sub_sub_category->id)->max('item_order') + 1;
        $item = Item::create([
            'type' => 'offer',
            'status' => 'in_review',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'sub_category_id' => $sub_category->id,
            'sub_sub_category_id' => $sub_sub_category->id,
            'price' => $request->price,
            'name_en' => $request->title,
            'description_en' => $description,
            'location_id' => $location->id,
            'is_available' => 0,
            'item_order' => $max_order,
            'longitude' => $longitude,
            'latitude' => $latitude,
            'expired_at' => Carbon::now()->addMonths(1)->timestamp,
        ]);
        foreach ($regular_fields as $regular_field){
            ItemCustomField::create([
                'item_id' => $item->id,
                'custom_field_id' => $regular_field->id,
                'field_name' => $regular_field->field_name,
                'field_value' => $regular_field->field_value,
                'extra_field_name' => $regular_field->extra_field_name,
                'extra_field_value' => $regular_field->extra_field_value,
            ]);
        }
        foreach ($filters_fields as $filter_field){
            ItemCustomField::create([
                'item_id' => $item->id,
                'custom_field_id' => $filter_field->id,
                'field_name' => $filter_field->field_name,
                'field_value' => $filter_field->field_value,
                'extra_field_name' => $filter_field->extra_field_name,
                'extra_field_value' => $filter_field->extra_field_value,
            ]);
        }
        if(Input::has('media_ids')){
            foreach (array_filter($request->media_ids) as $media_id){
                if(!$media = Media::whereNull('meta_id')->whereUserId($user->id)->whereId($media_id)->first()){
                    continue;
                }
                $media->meta_id = $item->id;
                $media->save();
            }
        }
        if($request->with_return == 'yes'){
            $return_item = Item::create([
                'type' => 'return',
                'status' => 'in_review',
                'user_id' => $user->id,
                'category_id' => $return_category->id,
                'sub_category_id' => $return_sub_category->id,
                'sub_sub_category_id' => $return_sub_sub_category->id,
                'price' => $request->return_price,
                'name_en' => $request->return_title,
                'description_en' => $return_description,
                'location_id' => $return_location->id,
                'is_available' => 0,
                'item_order' => $item->item_order,
                'longitude' => $item->longitude,
                'latitude' => $item->latitude,
                'expired_at' => Carbon::now()->addMonths(1)->timestamp,
            ]);
            foreach ($return_regular_fields as $return_regular_field){
                ItemCustomField::create([
                    'item_id' => $return_item->id,
                    'custom_field_id' => $return_regular_field->id,
                    'field_name' => $return_regular_field->field_name,
                    'field_value' => $return_regular_field->field_value,
                    'extra_field_name' => $return_regular_field->extra_field_name,
                    'extra_field_value' => $return_regular_field->extra_field_value,
                ]);
            }
        }
        return parent::return_json(['status' => 'Thank you for bartering with us. The post will go live within 24 hours and valid for 30 days','payment_url' => secure_url('payment-test'),'post_id' => $item->id]);
    }

    public function edit(Request $request){
        $user = $this->validate_user($request);

        if(!Input::has('item_id')){
            return parent::return_error('Missing Item ID',401,'messages.missing_parameter');
        }
        if(!$item = Item::whereUserId($user->id)->whereId($request->item_id)->first()){
            return parent::return_error("Invalid Item", 402, 'messages.invalid_parameters');
        }
        if(!Input::has('category_id')){
            return parent::return_error('Missing Category ID',401,'messages.missing_parameter');
        }
        if(!$category = Category::whereIsPublished(1)->whereId($request->category_id)->first()){
            return parent::return_error("Invalid Category", 402, 'messages.invalid_parameters');
        }
        if(!Input::has('sub_category_id')){
            return parent::return_error('Missing Sub Category ID',401,'messages.missing_parameter');
        }
        if(!$sub_category = SubCategory::whereIsPublished(1)->whereId($request->sub_category_id)->first()){
            return parent::return_error("Invalid Sub Category", 402, 'messages.invalid_parameters');
        }
        if(!Input::has('sub_sub_category_id')){
            return parent::return_error('Missing Sub Sub Category ID',401,'messages.missing_parameter');
        }
        if(!$sub_sub_category = SubSubCategory::whereIsPublished(1)->whereId($request->sub_sub_category_id)->first()){
            return parent::return_error("Invalid Sub Sub Category", 402, 'messages.invalid_parameters');
        }
        if(!Input::has('location_id')){
            return parent::return_error('Missing Location ID',401,'messages.missing_parameter');
        }
        if(!$location = Location::whereId($request->location_id)->first()){
            return parent::return_error("Invalid Location", 402, 'messages.invalid_parameters');
        }
        if(!Input::has('title')){
            return parent::return_error('Missing Title',401,'messages.missing_parameter');
        }
//        if(!Input::has('description')){
//            return parent::return_error('Missing Description',401,'messages.missing_parameter');
//        }
        $description = isset($request->description) && !empty($request->description) ? $request->description : null;
        if(!Input::has('price')){
            return parent::return_error('Missing Price',401,'messages.missing_parameter');
        }
        $regular_fields = [];
        if(Input::has('regular_fields')){
            $regular_fields = json_decode($request->regular_fields);
        }
        $filters_fields = [];
        if(Input::has('filters_fields')){
            $filters_fields = json_decode($request->filters_fields);
        }
        if($request->with_return == 'yes'){
            if(!Input::has('return_category_id')){
                return parent::return_error('Missing Return Category ID',401,'messages.missing_parameter');
            }
            if(!$return_category = Category::whereIsPublished(1)->whereId($request->return_category_id)->first()){
                return parent::return_error("Invalid Return Category", 402, 'messages.invalid_parameters');
            }
            if(!Input::has('return_sub_category_id')){
                return parent::return_error('Missing Return Sub Category ID',401,'messages.missing_parameter');
            }
            if(!$return_sub_category = SubCategory::whereIsPublished(1)->whereId($request->return_sub_category_id)->first()){
                return parent::return_error("Invalid Return Sub Category", 402, 'messages.invalid_parameters');
            }
            if(!Input::has('return_sub_sub_category_id')){
                return parent::return_error('Missing Return Sub Sub Category ID',401,'messages.missing_parameter');
            }
            if(!$return_sub_sub_category = SubSubCategory::whereIsPublished(1)->whereId($request->return_sub_sub_category_id)->first()){
                return parent::return_error("Invalid Return Sub Sub Category", 402, 'messages.invalid_parameters');
            }
            if(!Input::has('return_location_id')){
                return parent::return_error('Missing Return Location ID',401,'messages.missing_parameter');
            }
            if(!$return_location = Location::whereId($request->return_location_id)->first()){
                return parent::return_error("Invalid Return Location", 402, 'messages.invalid_parameters');
            }
            if(!Input::has('return_title')){
                return parent::return_error('Missing Return Title',401,'messages.missing_parameter');
            }
//            if(!Input::has('return_description')){
//                return parent::return_error('Missing Return Description',401,'messages.missing_parameter');
//            }
            $return_description = isset($request->return_description) && !empty($request->return_description) ? $request->return_description : null;
            if(!Input::has('return_price')){
                return parent::return_error('Missing Return Price',401,'messages.missing_parameter');
            }
            $return_regular_fields = [];
            if(Input::has('return_regular_fields')){
                $return_regular_fields = json_decode($request->return_regular_fields);
            }
        }
        $latitude = isset($request->latitude) ? $request->latitude : null;
        $longitude = isset($request->longitude) ? $request->longitude : null;
        $max_order = DB::table('items')->whereCategoryId($category->id)->whereSubCategoryId($sub_category->id)->whereSubSubCategoryId($sub_sub_category->id)->max('item_order') + 1;
        $item->update([
            'category_id' => $category->id,
            'sub_category_id' => $sub_category->id,
            'sub_sub_category_id' => $sub_sub_category->id,
            'price' => $request->price,
            'name_en' => $request->title,
            'description_en' => $description,
            'location_id' => $location->id,
            'longitude' => $longitude,
            'latitude' => $latitude,
        ]);
        ItemCustomField::whereItemId($item->id)->delete();
        foreach ($regular_fields as $regular_field){
            ItemCustomField::create([
                'item_id' => $item->id,
                'custom_field_id' => $regular_field->id,
                'field_name' => $regular_field->field_name,
                'field_value' => $regular_field->field_value,
                'extra_field_name' => $regular_field->extra_field_name,
                'extra_field_value' => $regular_field->extra_field_value,
            ]);
        }
        foreach ($filters_fields as $filter_field){
            ItemCustomField::create([
                'item_id' => $item->id,
                'custom_field_id' => $filter_field->id,
                'field_name' => $filter_field->field_name,
                'field_value' => $filter_field->field_value,
                'extra_field_name' => $filter_field->extra_field_name,
                'extra_field_value' => $filter_field->extra_field_value,
            ]);
        }
        if(Input::has('media_ids')){
            foreach (array_filter($request->media_ids) as $media_id){
                if(!$media = Media::whereNull('meta_id')->whereUserId($user->id)->whereId($media_id)->first()){
                    continue;
                }
                $media->meta_id = $item->id;
                $media->save();
            }
        }
        if($request->with_return == 'yes'){
            $return_item = $item->return_post;
            if($return_item){
                $return_item->update([
                    'category_id' => $return_category->id,
                    'sub_category_id' => $return_sub_category->id,
                    'sub_sub_category_id' => $return_sub_sub_category->id,
                    'price' => $request->return_price,
                    'name_en' => $request->return_title,
                    'description_en' => $return_description,
                    'location_id' => $return_location->id,
                    'longitude' => $longitude,
                    'latitude' => $latitude,
                ]);
            }else{
                $return_item = Item::create([
                    'type' => 'return',
                    'status' => $item->status,
                    'user_id' => $user->id,
                    'category_id' => $return_category->id,
                    'sub_category_id' => $return_sub_category->id,
                    'sub_sub_category_id' => $return_sub_sub_category->id,
                    'price' => $request->return_price,
                    'name_en' => $request->return_title,
                    'description_en' => $return_description,
                    'location_id' => $return_location->id,
                    'is_available' => $item->is_available,
                    'item_order' => $item->item_order,
                    'longitude' => $item->longitude,
                    'latitude' => $item->latitude,
                    'expired_at' => $item->expired_at,
                ]);
            }
            ItemCustomField::whereItemId($return_item->id)->delete();
            foreach ($return_regular_fields as $return_regular_field){
                ItemCustomField::create([
                    'item_id' => $return_item->id,
                    'custom_field_id' => $return_regular_field->id,
                    'field_name' => $return_regular_field->field_name,
                    'field_value' => $return_regular_field->field_value,
                    'extra_field_name' => $return_regular_field->extra_field_name,
                    'extra_field_value' => $return_regular_field->extra_field_value,
                ]);
            }

        }
        return parent::return_json(['status' => 'Your post has been updated successfully']);
    }

    public function delete(Request $request){
        $user = $this->validate_user($request);

        if(!Input::has('item_id')){
            return parent::return_error('Missing Item ID',401,'messages.missing_parameter');
        }
        if(!$item = Item::whereUserId($user->id)->whereId($request->item_id)->first()){
            return parent::return_error("Invalid Item", 402, 'messages.invalid_parameters');
        }
        $item->delete();
        return parent::return_json(['status' => 'Your post has been removed successfully']);
    }

    public function my_posts(Request $request) {
        $user = $this->validate_user($request);
        $search = Input::has('search') ? Input::get('search') : null;
        $query = Item::whereType('offer')->whereUserId($user->id)->when($search, function ($query) use ($search) {
            $query->where(function($query) use ($search){
                $query->whereRaw("LOWER(name_en) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(name_ar) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(description_en) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(description_ar) like ? ", [ "%".strtolower($search)."%"]);
            });
        });
        $category_id = isset($request->category_id) ? $request->category_id : null;
        $sub_category_ids = isset($request->sub_category_ids) ? $request->sub_category_ids : null;
        $sub_sub_category_id = isset($request->sub_sub_category_id) ? $request->sub_sub_category_id : null;

        $query->when($category_id, function ($query) use ($category_id) {
            $query->whereCategoryId($category_id);
        });
        $query->when($sub_category_ids, function ($query) use ($sub_category_ids) {
            $query->whereIn('sub_category_id',$sub_category_ids);
        });
        $query->when($sub_sub_category_id, function ($query) use ($sub_sub_category_id) {
            $query->whereSubSubCategoryId($sub_sub_category_id);
        });
        $query->orderBy('item_order','desc');
        $currency = env('APP_CURRENCY');
        $items = $query->get()->map(function ($value, $key) use ($currency,$user) {
            $value->is_favorite = false;
            if($user){
                if(UserFavorite::whereMetaName('items')->whereMetaId($value->id)->whereUserId($user->id)->first()){
                    $value->is_favorite = true;
                }
            }
            if($value->status == 'expired'){
                $value->payment_url = secure_url('payment-test');
            }
            $value->return_post;
            $value->currency = $currency;
            $value->post_type = $value->category->type;
            unset($value->category);
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

    public function user_related_posts(Request $request) {
        $user = $this->validate_user($request);
        if(!Input::has('user_id')){
            return parent::return_error('Missing User ID',401,'messages.missing_parameter');
        }
        $user_id = $request->user_id;
        $search = Input::has('search') ? Input::get('search') : null;
        $query = Item::whereIsAvailable(1)->whereType('offer')->whereUserId($user_id)->when($search, function ($query) use ($search) {
            $query->where(function($query) use ($search){
                $query->whereRaw("LOWER(name_en) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(name_ar) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(description_en) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(description_ar) like ? ", [ "%".strtolower($search)."%"]);
            });
        });
        $category_id = isset($request->category_id) ? $request->category_id : null;
        $sub_category_ids = isset($request->sub_category_ids) ? $request->sub_category_ids : null;
        $sub_sub_category_id = isset($request->sub_sub_category_id) ? $request->sub_sub_category_id : null;

        $query->when($category_id, function ($query) use ($category_id) {
            $query->whereCategoryId($category_id);
        });
        $query->when($sub_category_ids, function ($query) use ($sub_category_ids) {
            $query->whereIn('sub_category_id',$sub_category_ids);
        });
        $query->when($sub_sub_category_id, function ($query) use ($sub_sub_category_id) {
            $query->whereSubSubCategoryId($sub_sub_category_id);
        });
        $query->orderBy('item_order','desc');
        $currency = env('APP_CURRENCY');
        $items = $query->take(5)->get()->map(function ($value, $key) use ($currency,$user) {
            $value->is_favorite = false;
            if($user){
                if(UserFavorite::whereMetaName('items')->whereMetaId($value->id)->whereUserId($user->id)->first()){
                    $value->is_favorite = true;
                }
            }
            $value->return_post;
            $value->currency = $currency;
            $value->post_type = $value->category->type;
            unset($value->category);
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
}
