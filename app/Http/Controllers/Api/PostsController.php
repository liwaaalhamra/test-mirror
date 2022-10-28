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
use App\Models\Api\Conversation;
use App\Traits\HITrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Propaganistas\LaravelPhone\PhoneNumber;
use Illuminate\Support\Facades\Log;

use Validator;
use Mail;

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
            return parent::return_error('Missing Sender Item ID', 401, 'messages.select_a_post');
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
            return parent::return_error("Barter Request already sent, check status in My Barters section", 402, "Barter Request already sent");
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
        $message = "Hello $receiver_full_name. $sender_full_name is interested in your post. Please click on the button below to check what $sender_full_name is offering you. You will be able to chat with $sender_full_name once you accept the request to barter!";

        if($user_push = UserPush::whereUsersId($receiver->id)->first()){
            $players_ids[] = $user_push->player_id;
            $contents = ["en" => $message];
            $headings = ["en" => $title];
            $info['contents'] = $contents;
            $info['headings'] = $headings;
            $info['player_ids'] = $players_ids;
            $info['data'] = ['meta_id' => $barter->id, 'type' => 'barter_request'];
            $result = parent::oneSignal($info);
                        
            $code = Controller::number_generator(6);

            Inbox::create([
                'user_id' => $receiver->id,
                'title' => $title,
                'message' => $message,
                'image' => null,
                'type' => 'targeted',
                'code' => $code,
                'meta_type' => 'barter_request',
            ]);

        }

        return parent::return_json(['status' => 'Thank you for bartering with us. Your barter request has been sent to '.$receiver_full_name.'. You will be able to chat with '.$receiver_full_name.' once they accept your barter.']);
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
        $title = "Barter Request ".ucwords($request->status).'!';
        $status = "Your request status has been updated.";
        $type = "barter";

        if($request->status == 'accepted'){
            $message = "Hello $sender_full_name. $receiver_full_name has accepted your request to barter! You can chat with $receiver_full_name by clicking on the chat or later by accessing the chat from the inbox or my barters.";
            $status = "Thank you for accepting the offer. Now you can chat with $sender_full_name!";
            $type= "barter_accepted";
        }else{
            $message = "Hello $sender_full_name. $receiver_full_name is not interested in your request to barter. Please explore more of the posts found in barterdom' App!";
            $status = "Your request status has been updated.";
        }

        $conversation = Conversation::where(function ($query) use($sender,$receiver) {
            $query->where(function ($query) use($sender,$receiver) {
                $query->whereUserId($sender->id)->whereFriendId($receiver->id);
            })->orwhere(function ($query) use($sender,$receiver) {
                $query->whereUserId($receiver->id)->whereFriendId($sender->id);
            });
        })->first();
        if(!$conversation){
            $conversation = Conversation::create([
                'user_id' => $sender->id,
                'friend_id' => $receiver->id,
                'unread_messages' => 1
            ]);
        }else{
            $conversation->unread_messages = $conversation->unread_messages + 1;
            $conversation->save();
        }

        if($user_push = UserPush::whereUsersId($sender->id)->first()){
            $players_ids[] = $user_push->player_id;
            $contents = ["en" => $message];
            $headings = ["en" => $title];
            $info['contents'] = $contents;
            $info['headings'] = $headings;
            $info['player_ids'] = $players_ids;
            $conversation->conversation_id = $conversation ? $conversation->id : null;
            $conversation->friend_name = $user->id == $conversation->friend_id ? $conversation->friend->full_name : $conversation->user->full_name;
            $conversation->friend_id = $receiver->id;
            $conversation->friend_image = $user->id == $conversation->friend_id ? $conversation->friend->photo : $conversation->user->photo;

            $info['data'] = ['meta_id' => $barter->id, 'type' => $type, 'conversation' => $conversation];
            $result = parent::oneSignal($info);

            $code = Controller::number_generator(6);

            Inbox::create([
                'user_id' => $sender->id,
                'title' => $title,
                'message' => $message,
                'image' => null,
                'type' => 'targeted',
                'code' => $code,
                'meta_type' => $type,
                'meta_value' => '{"friend_name": "'.$conversation->friend_name.'","conversation_id": '.$conversation->id.',"friend_id": '.$conversation->friend_id.',"friend_image": "'.$conversation->friend_image.'"}'
            ]);
        }

        return parent::return_json(['status' => $status]);
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

        return parent::return_json(['status' => 'Your request status has been updated successfully!']);
    }

    public function my_barters(Request $request)
    {
        $user = $this->validate_user($request);

        $barters = Barter::wherenotin('status',['completed', 'rejected'])
        ->where('created_at', '>=', Carbon::now()->subMonths(2)->toDateTimeString()) //removing barters older than 2 months
        ->where(function ($query) use ($user) {
            $query->whereReceiverId($user->id)->orWhere('sender_id',$user->id);
        })->orderByRaw("FIELD(status , 'pending', 'accepted', 'rejected') ASC")->orderByRaw("FIELD(sender_id, $user->id) ASC")->get()->map(function ($query, $key) use ($user){
            $query->sender_post_type = $query->sender_post ? $query->sender_post->category->type : "";
            $query->receiver_post_type = $query->receiver_post ? $query->receiver_post->category->type : "";
            if($query->status == 'accepted' && $query->receiver_post){
                $sender = $query->sender_post;
                $receiver = $query->receiver_post;

                if(isset($sender) && isset($receiver)){
                    $conversation = Conversation::where(function ($query) use($sender, $receiver) {
                        $query->where(function ($query) use($sender, $receiver) {
                            $query->whereUserId($sender->user_id)->whereFriendId($receiver->user_id);
                        })->orwhere(function ($query) use($sender, $receiver) {
                            $query->whereUserId($receiver->user_id)->whereFriendId($sender->user_id);
                        });
                    })->first();
                    
                    if(!$conversation){
                        $conversation = Conversation::create([
                            'user_id' => $user ? $user->id : null,
                            'friend_id' => $user && $user->id == $sender->user_id  ? $receiver->user_id : $sender->user_id,
                            'unread_messages' => 0
                        ]);
                    }else{
                        $conversation->unread_messages = $conversation->unread_messages + 1;
                        $conversation->save();
                    }
                }     
            }          
            $query->conversation_id = isset($conversation) && $conversation ? $conversation->id : null;
            return $query;
        });

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
        $max_order = isset($sub_sub_category) ? DB::table('items')->whereCategoryId($category->id)->whereSubCategoryId($sub_category->id)->whereSubSubCategoryId($sub_sub_category->id)->max('item_order') + 1 : DB::table('items')->whereCategoryId($category->id)->whereSubCategoryId($sub_category->id)->max('item_order') + 1;
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
            'sub_region_id' => $request->sub_region_id,
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
                'sub_region_id' => $request->return_sub_region_id,
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
        return parent::return_json(['status' => 'Thank you for bartering with us. Your post will be reviewed within 24 hours. You will be updated once it is live.','payment_url' => secure_url('payment-test'),'post_id' => $item->id]);
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
        $max_order = isset($sub_sub_category->id) ? DB::table('items')->whereCategoryId($category->id)->whereSubCategoryId($sub_category->id)->whereSubSubCategoryId($sub_sub_category->id)->max('item_order') + 1 : DB::table('items')->whereCategoryId($category->id)->whereSubCategoryId($sub_category->id)->max('item_order') + 1;
        $item->update([
            'category_id' => $category->id,
            'sub_category_id' => $sub_category->id,
            'sub_sub_category_id' => isset($sub_sub_category) ? $sub_sub_category->id : null,
            'price' => $request->price,
            'name_en' => $request->title,
            'description_en' => $description,
            'location_id' => $location->id,
            'sub_region_id' => $request->sub_region_id,
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
                    'sub_region_id' => $request->return_sub_region_id,
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
                    'sub_region_id' => $request->return_sub_region_id,
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

        $info = [];
        $info['to'] = env("MAIL_TO");
        $info['icon'] = 'alert.png';
        $info['post_title'] = $request->title;
        $info['post_name'] = $user->full_name;
        $info['title'] =  "Post Edited!";
        Mail::send('emails.on_post_edit', ['data' => $info], function ($message) use ($info) {
            $message->subject($info['title']);
            $message->to($info['to']);
        });

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
        $query = Item::whereType('offer')->whereUserId($user->id)->orderBy('status')->orderBy('updated_at', 'desc')->when($search, function ($query) use ($search) {
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
        $query = Item::where('id', '!=', $request->post_id)->whereIsAvailable(1)->whereType('offer')->whereUserId($user_id)->when($search, function ($query) use ($search) {
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
        $query = Item::where('id', '!=', $request->post_id)->whereIsAvailable(1)->whereType('offer')->whereNotIn('status', ['pending'])->whereUserId($user_id)->when($search, function ($query) use ($search) {
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
