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
use App\Models\Api\Media;
use App\Models\Api\Restaurant;
use App\Models\Api\RestaurantEventType;
use App\Models\Api\ReviewTag;
use App\Models\Api\Setting;
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

    /**
     * Post Status
     * pending: on creation
     * in_review: after payment success
     * approved: from cms
     * completed: in case flagged as done deal => post will be in finalized barter
     * expired
     *
     * barter status
     * pending: request sent from user1 to user2
     * accepted/rejected: user2 action on request
     * completed: done deal
     */

    public function barter(Request $request)
    {
        $sender = $this->validate_user($request);

        if (!Input::has('sender_item_id')) {
            return parent::return_error('Missing Sender Item ID', 401, 'messages.missing_parameter');
        }
        if (!$sender_item = Item::whereUserId($sender->id)->whereId($request->sender_item_id)->first()) {
            return parent::return_error("Invalid Sender Item", 402, 'messages.invalid_parameters');
        }
        if($sender_item->status != 'approved'){
            return parent::return_error("Post not approved yet", 402, "Post not approved yet");
        }

        if (!Input::has('receiver_item_id')) {
            return parent::return_error('Missing Receiver Item ID', 401, 'messages.missing_parameter');
        }
        if (!$receiver_item = Item::whereStatus('approved')->whereId($request->receiver_item_id)->first()) {
            return parent::return_error("Invalid Receiver Item", 402, 'messages.invalid_parameters');
        }
        $receiver = $receiver_item->user;

        if($existing_barter = Barter::whereSenderId($sender->id)->whereReceiverId($receiver->id)->whereSenderPostId($sender_item->id)->whereReceiverPostId($receiver_item->id)->first()){
            return parent::return_error("Barter Request already Sent,check status in My Barters section", 402, "Barter Request already Sent");
        }

        $status_at['pending_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $barter = Barter::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'sender_post_id' => $sender_item->id,
            'receiver_post_id' => $receiver_item->id,
            'status' => 'pending',
            'status_at' => $status_at
        ]);
        $sender_full_name = $sender->full_name;
        $receiver_full_name = $receiver->full_name;
        $title = "Barter Request!";
        $message = "$sender_full_name is interested in your post, would you like to accept the request?";

        if($user_push = UserPush::whereUsersId($receiver->id)->first()){
            $players_ids[] = $user_push->player_id;
            $contents = ["en" => $message];
            $headings = ["en" => $title];
            $info['contents'] = $contents;
            $info['headings'] = $headings;
            $info['player_ids'] = $players_ids;
            $info['data'] = ['meta_id' => $barter->id,'type' => 'barter'];
            $result = parent::oneSignal($info);
        }

        return parent::return_json(['status' => ' thank you for bartering with us. Your barter request has been sent to '.$receiver_full_name.'. You will be able to chat with '.$receiver_full_name.' once they accept your barter.']);
    }

    public function barter_action(Request $request)
    {
        $user = $this->validate_user($request);

        if (!Input::has('barter_id')) {
            return parent::return_error('Missing Barter ID', 401, 'messages.missing_parameter');
        }
        if (!$barter = Barter::whereReceiverId($user->id)->whereStatus('pending')->whereId($request->barter_id)->first()) {
            return parent::return_error("Invalid Barter", 402, 'messages.invalid_parameters');
        }

        if (!Input::has('status')) {
            return parent::return_error('Missing Status', 401, 'messages.missing_parameter');
        }
        $status_possible_values = ['accepted','rejected'];
        if(!in_array($request->status,$status_possible_values)){
            return parent::return_error("Status Wrong Value", 402, 'messages.invalid_parameters');
        }
        $status_at = $barter->status_at;
        $status_at[$request->status.'_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $barter->status = $request->status;
        $barter->status_at = $status_at;
        $barter->save();

        $receiver = $barter->receiver;
        $sender = $barter->sender;


        $sender_full_name = $sender->full_name;
        $receiver_full_name = $receiver->full_name;
        $title = "Barter Request ".ucwords($request->status).' !';
        $message = "Hello $sender_full_name, $receiver_full_name has ".$request->status." your request!";

        if($user_push = UserPush::whereUsersId($sender->id)->first()){
            $players_ids[] = $user_push->player_id;
            $contents = ["en" => $message];
            $headings = ["en" => $title];
            $info['contents'] = $contents;
            $info['headings'] = $headings;
            $info['player_ids'] = $players_ids;
            $info['data'] = ['meta_id' => $barter->id,'type' => 'barter'];
            $result = parent::oneSignal($info);
        }

        return parent::return_json(['status' => 'your request status has been updated successfully']);
    }

    public function barter_done(Request $request)
    {
        $user = $this->validate_user($request);

        if (!Input::has('barter_id')) {
            return parent::return_error('Missing Barter ID', 401, 'messages.missing_parameter');
        }
        if (!$barter = Barter::whereStatus('accepted')->whereId($request->barter_id)->first()) {
            return parent::return_error("Invalid Barter", 402, 'messages.invalid_parameters');
        }
        $user_push_id = null;
        if($user->id == $barter->sender_id){
            $receiver = $barter->receiver;
            $sender = $barter->sender;
            $user_push_id = $barter->receiver_id;
        }elseif ($user->id == $barter->receiver_id){
            $receiver = $barter->sender;
            $sender = $barter->receiver;
            $user_push_id = $barter->sender_id;
        }else{
            return parent::return_error("Invalid Barter", 403, 'messages.invalid_parameters');
        }
        $status_at = $barter->status_at;
        $status_at['completed_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $barter->status = 'completed';
        $barter->status_at = $status_at;
        $barter->save();

        $sender_full_name = $sender->full_name;
        $receiver_full_name = $receiver->full_name;
        $title = "Barter ".ucwords($request->status).' !';
        $message = "Hello $receiver_full_name, $sender_full_name has flagged the barter as done!";

        if($user_push = UserPush::whereUsersId($user_push_id)->first()){
            $players_ids[] = $user_push->player_id;
            $contents = ["en" => $message];
            $headings = ["en" => $title];
            $info['contents'] = $contents;
            $info['headings'] = $headings;
            $info['player_ids'] = $players_ids;
            $info['data'] = ['meta_id' => $barter->id,'type' => 'barter'];
            $result = parent::oneSignal($info);
        }

        return parent::return_json(['status' => 'your request status has been updated successfully']);
    }

    public function my_barters(Request $request)
    {
        $user = $this->validate_user($request);

        $barters = Barter::wherenotin('status',['completed'])->where(function ($query) use ($user) {
            $query->whereReceiverId($user->id)->orWhere('sender_id',$user->id);
        })->get();

        return parent::return_json($barters);
    }

    public function my_finalized_barters(Request $request)
    {
        $user = $this->validate_user($request);

        $barters = Barter::wherein('status',['completed'])->where(function ($query) use ($user) {
            $query->whereReceiverId($user->id)->orWhere('sender_id',$user->id);
        })->get();

        return parent::return_json($barters);
    }

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

        // if(!Input::has('sub_sub_category_id')){
        //     return parent::return_error('Missing Sub Sub Category ID',401,'messages.missing_parameter');
        // }

        if(Input::has('sub_sub_category_id') && !$sub_sub_category = SubSubCategory::whereIsPublished(1)->whereId($request->sub_sub_category_id)->first()){
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
        if(!Input::has('description')){
            return parent::return_error('Missing Description',401,'messages.missing_parameter');
        }
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
            // if(!Input::has('return_sub_sub_category_id')){
            //     return parent::return_error('Missing Return Sub Sub Category ID',401,'messages.missing_parameter');
            // }
            if(Input::has('return_sub_sub_category_id')){
                if(!$return_sub_sub_category = SubSubCategory::whereIsPublished(1)->whereId($request->return_sub_sub_category_id)->first()){
                    return parent::return_error("Invalid Return Sub Sub Category", 402, 'messages.invalid_parameters');
                }
            }

            if(!Input::has('return_location_id')){
                return parent::return_error('Missing Return Location ID', 401, 'messages.missing_parameter');
            }
            if(!$return_location = Location::whereId($request->return_location_id)->first()){
                return parent::return_error("Invalid Return Location", 402, 'messages.invalid_parameters');
            }
            if(!Input::has('return_title')){
                return parent::return_error('Missing Return Title', 401, 'messages.missing_parameter');
            }
//            if(!Input::has('return_description')){
//                return parent::return_error('Missing Return Description',401,'messages.missing_parameter');
//            }
            $return_description = isset($request->return_description) && !empty($request->return_description) ? $request->return_description : null;
            if(!Input::has('return_price_from')){
                return parent::return_error('Missing Return Price From', 401,'messages.missing_parameter');
            }
            if(!Input::has('return_price_to')){
                return parent::return_error('Missing Return Price To', 401,'messages.missing_parameter');
            }
            $return_regular_fields = [];
            if(Input::has('return_regular_fields')){
                $return_regular_fields = json_decode($request->return_regular_fields);
            }
        }
        $latitude = isset($request->latitude) ? $request->latitude : null;
        $longitude = isset($request->longitude) ? $request->longitude : null;
        $max_order = DB::table('items')->whereCategoryId($category->id)->whereSubCategoryId($sub_category->id)->whereSubSubCategoryId($sub_sub_category->id)->max('item_order') + 1;
        $post_validity_days = 30;
        if($setting = Setting::whereMetaName('post_validity')->first()){
            $post_validity_days = intval($setting->meta_value);
        }
        $item = Item::create([
            'type' => 'offer',
            'status' => 'pending',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'sub_category_id' => $sub_category->id,
            'sub_sub_category_id' => isset($sub_sub_category) && isset($sub_sub_category->id) ? $sub_sub_category->id : null,
            'price' => $request->price,
            'name_en' => $request->title,
            'description_en' => $description,
            'location_id' => $location->id,
            'country_id' => $location->country_id,
            'is_available' => 0,
            'item_order' => $max_order,
            'longitude' => $longitude,
            'latitude' => $latitude,
            'expired_at' => Carbon::now()->addDays($post_validity_days)->timestamp,
        ]);
        foreach ($regular_fields as $regular_field){
            ItemCustomField::create([
                'item_id' => $item->id,
                'custom_field_id' => $regular_field->id,
                'field_name' => $regular_field->field_name,
                'field_value' => $regular_field->field_value,
                'extra_field_name' => isset($regular_field->extra_field_name) ? $regular_field->extra_field_name : null,
                'extra_field_value' => isset($regular_field->extra_field_value) ? $regular_field->extra_field_value : null,
            ]);
        }
        foreach ($filters_fields as $filter_field){
            ItemCustomField::create([
                'item_id' => $item->id,
                'custom_field_id' => $filter_field->id,
                'field_name' => $filter_field->field_name,
                'field_value' => $filter_field->field_value,
                'extra_field_name' => isset($filter_field->extra_field_name) ? $filter_field->extra_field_name : null,
                'extra_field_value' => isset($filter_field->extra_field_value) ? $filter_field->extra_field_value : null,
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
                'status' => 'pending',
                'user_id' => $user->id,
                'category_id' => $return_category->id,
                'sub_category_id' => $return_sub_category->id,
                'sub_sub_category_id' => isset($return_sub_sub_category) && $return_sub_sub_category ? $return_sub_sub_category->id : null,
                'price_from' => $request->return_price_from,
                'price_to' => $request->return_price_to,
                'name_en' => $request->return_title,
                'description_en' => $return_description,
                'location_id' => $return_location->id,
                'country_id' => $return_location->country_id,
                'is_available' => 0,
                'item_order' => $item->item_order,
                'longitude' => $item->longitude,
                'latitude' => $item->latitude,
                'expired_at' => Carbon::now()->addDays($post_validity_days)->timestamp,
            ]);
            $item->return_id = $return_item->id;
            $item->save();
            foreach ($return_regular_fields as $return_regular_field){
                ItemCustomField::create([
                    'item_id' => $return_item->id,
                    'custom_field_id' => $return_regular_field->id,
                    'field_name' => $return_regular_field->field_name,
                    'field_value' => $return_regular_field->field_value,
                    'extra_field_name' => isset($return_regular_field->extra_field_name) ? $return_regular_field->extra_field_name : null,
                    'extra_field_value' => isset($return_regular_field->extra_field_value) ? $return_regular_field->extra_field_value : null,
                ]);
            }
        }
        return parent::return_json(['status' => 'Thank you for bartering with us. Your post will be reviewed within 24 hours. You will be updated once it is live','payment_url' => secure_url('payment-test'),'post_id' => $item->id]);
    }

    public function update(Request $request){
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

        // if(!Input::has('sub_sub_category_id')){
        //     return parent::return_error('Missing Sub Sub Category ID',401,'messages.missing_parameter');
        // }

        if(Input::has('sub_sub_category_id') && !$sub_sub_category = SubSubCategory::whereIsPublished(1)->whereId($request->sub_sub_category_id)->first()){
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
        if(!Input::has('description')){
            return parent::return_error('Missing Description',401,'messages.missing_parameter');
        }
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
            // if(!Input::has('return_sub_sub_category_id')){
            //     return parent::return_error('Missing Return Sub Sub Category ID',401,'messages.missing_parameter');
            // }
            if(Input::has('return_sub_sub_category_id') && !$return_sub_sub_category = SubSubCategory::whereIsPublished(1)->whereId($request->return_sub_sub_category_id)->first()){
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
            if(!Input::has('return_price_from')){
                return parent::return_error('Missing Return Price From',401,'messages.missing_parameter');
            }
            if(!Input::has('return_price_to')){
                return parent::return_error('Missing Return Price To',401,'messages.missing_parameter');
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
            'sub_sub_category_id' => isset($sub_sub_category) ? $sub_sub_category->id : null,
            'price' => $request->price,
            'name_en' => $request->title,
            'description_en' => $description,
            'location_id' => $location->id,
            'country_id' => $location->country_id,
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
                'extra_field_name' => isset($regular_field->extra_field_name) ? $regular_field->extra_field_name : null,
                'extra_field_value' => isset($regular_field->extra_field_value) ? $regular_field->extra_field_value : null,
            ]);
        }
        foreach ($filters_fields as $filter_field){
            ItemCustomField::create([
                'item_id' => $item->id,
                'custom_field_id' => $filter_field->id,
                'field_name' => $filter_field->field_name,
                'field_value' => $filter_field->field_value,
                'extra_field_name' => isset($filter_field->extra_field_name) ? $filter_field->extra_field_name : null,
                'extra_field_value' => isset($filter_field->extra_field_value) ? $filter_field->extra_field_value : null,
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
                    'sub_sub_category_id' => isset($return_sub_sub_category) ? $return_sub_sub_category->id : null,
                    'price_from' => $request->return_price_from,
                    'price_to' => $request->return_price_to,
                    'name_en' => $request->return_title,
                    'description_en' => $return_description,
                    'location_id' => $return_location->id,
                    'country_id' => $return_location->country_id,
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
                    'sub_sub_category_id' => isset($return_sub_sub_category) ? $return_sub_sub_category->id : null,
                    'price_from' => $request->return_price_from,
                    'price_to' => $request->return_price_to,
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
            $item->return_id = $return_item->id;
            $item->save();
            ItemCustomField::whereItemId($return_item->id)->delete();
            foreach ($return_regular_fields as $return_regular_field){
                ItemCustomField::create([
                    'item_id' => $return_item->id,
                    'custom_field_id' => $return_regular_field->id,
                    'field_name' => $return_regular_field->field_name,
                    'field_value' => $return_regular_field->field_value,
                    'extra_field_name' => isset($return_regular_field->extra_field_name) ? $return_regular_field->extra_field_name : null,
                    'extra_field_value' => isset($return_regular_field->extra_field_value) ? $return_regular_field->extra_field_value : null,
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
            if(in_array($value->status, ['expired','pending'])){
                $value->payment_url = secure_url('payment-test');
            }
            $value->return_post;
            $value->currency = $currency;
            $value->post_type = $value->category->type;
            unset($value->category);
//            $value->price_formatted = number_format($value->price,3) . ' ' . $currency;
//            $value->discounted_price_formatted = $value->discounted_price ? number_format($value->discounted_price,3) . ' ' . $currency : null;
//            $value->discount_amount_formatted = $value->discount_amount ? number_format($value->discount_amount,3) . ' ' . $currency : null;
            return $value;
        })->values();

        return response()->json($items);
    }

    public function my_postsx(Request $request) {
        $items = Item::whereType('offer')->get()->map(function ($value, $key) {
            $value->total = 0;
            if($value->category_id == 1){
                $value->total = $value->total + 10;
            }
            if($value->sub_category_id == 1){
                $value->total = $value->total + 10;
            }
            if($value->sub_sub_category_id == 1){
                $value->total = $value->total + 10;
            }
            return $value;
        })->sortbydesc("total")->values();

        

        return response()->json($items);
    }

    public function my_posts_with_pagination(Request $request) {
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
//            $value->price_formatted = number_format($value->price,3) . ' ' . $currency;
//            $value->discounted_price_formatted = $value->discounted_price ? number_format($value->discounted_price,3) . ' ' . $currency : null;
//            $value->discount_amount_formatted = $value->discount_amount ? number_format($value->discount_amount,3) . ' ' . $currency : null;
            return $value;
        })->values();

        $limit = 20;
        $items = new LengthAwarePaginator(
            $items->forPage(LengthAwarePaginator::resolveCurrentPage(), $limit)->values(), $items->count(), $limit, LengthAwarePaginator::resolveCurrentPage(), ['path' => secure_url('/api/services/items/pagination')]
        );
        return response()->json($items);
    }

    public function user_related_posts_with_pagination(Request $request) {
        $user = null;
        if($request->header('token')){
            $user = $this->validate_user($request);
        }
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
        $items = $query->get()->map(function ($value, $key) use ($currency,$user) {
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
//            $value->price_formatted = number_format($value->price,3) . ' ' . $currency;
//            $value->discounted_price_formatted = $value->discounted_price ? number_format($value->discounted_price,3) . ' ' . $currency : null;
//            $value->discount_amount_formatted = $value->discount_amount ? number_format($value->discount_amount,3) . ' ' . $currency : null;
            return $value;
        })->values();

        $limit = 20;
        $items = new LengthAwarePaginator(
            $items->forPage(LengthAwarePaginator::resolveCurrentPage(), $limit)->values(), $items->count(), $limit, LengthAwarePaginator::resolveCurrentPage(), ['path' => secure_url('/api/services/items/pagination')]
        );
        return response()->json($items);
    }

    public function user_related_posts(Request $request) {
        $user = null;
        if($request->header('token')){
            $user = $this->validate_user($request);
        }
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
//            $value->price_formatted = number_format($value->price,3) . ' ' . $currency;
//            $value->discounted_price_formatted = $value->discounted_price ? number_format($value->discounted_price,3) . ' ' . $currency : null;
//            $value->discount_amount_formatted = $value->discount_amount ? number_format($value->discount_amount,3) . ' ' . $currency : null;
            return $value;
        })->values();

        return response()->json($items);
    }
}
