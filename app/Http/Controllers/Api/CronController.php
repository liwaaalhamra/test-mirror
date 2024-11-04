<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\UserMatchingRecord;
use App\Models\Api\Ad;

use App\Models\Api\CheckoutLog;
use App\Models\Api\Country;
use App\Models\Api\Dashboard;
use App\Models\Api\Item;
use App\Models\Api\OrderStatus;
use App\Models\Api\Restaurant;
use App\Models\Api\Order;
use App\Models\Api\PackageItem;
use App\Models\Api\Setting;
use App\Models\Api\Inbox;
use App\Models\Api\Users;
use App\Models\Api\UserPush;
use App\Models\Api\WallMedia;
use App\Models\Api\WallPost;
use App\Models\Api\Barter;
use Carbon\Carbon;

use DB;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Api\UserFavorite;
use Illuminate\Support\Facades\Log;
use Mail;
use stdClass;

class CronController extends Controller
{
    /**
     * Get Wall
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function save_posts(Request $request) {
        $posts = [];
        /**
         * get Instagram posts
         */
        $platform_type = 'instagram';
        $access_token = env('INSTAGRAM_ACCESS_TOKEN');
        $photo_count=20;

        $json_link="https://api.instagram.com/v1/users/self/media/recent/?";
        $json_link.="access_token={$access_token}&count={$photo_count}";
        $instagram_posts = file_get_contents($json_link);
//            $instagram_posts = json_encode(json_decode($json, true, 512, JSON_BIGINT_AS_STRING));
        $instagram_posts = json_decode(json_decode(json_encode($instagram_posts)));
        foreach ($instagram_posts->data as $instagram_post){
            $created_datetime = intval($instagram_post->created_time);
            $id = $instagram_post->id;
            $user_name = $instagram_post->user->full_name;
            $user_image = $instagram_post->user->profile_picture;
            $title = null;
            $description = isset($instagram_post->caption) ? $instagram_post->caption->text : null;
            $db_post = WallPost::wherePlatformType($platform_type)->wherePlatformId($id)->first();
            if($db_post){
                /**
                 * delete old media to avoid expired media
                 */
                WallMedia::wherePostId($db_post->id)->delete();
            }else{
                $db_post = WallPost::create([
                    'post_source' => 'social_media',
                    'platform_type' => $platform_type,
                    'platform_id' => $id,
                    'created_datetime' => $created_datetime,
                    'post_source_url' => $instagram_post->link,
                    'title' => $title,
                    'description' => $description,
                    'user_name' => $user_name,
                    'user_image' => $user_image,
                ]);
            }

            $media = [];
            if(isset($instagram_post->carousel_media)){//post with multiple media(images or videos)
                foreach ($instagram_post->carousel_media as $index => $instagram_media){
                    if(isset($instagram_media->images)){// the media is an image
                        $media[] = [
                            'type' => 'image',
                            'url' => $instagram_media->images->standard_resolution->url,
                            'height' => $instagram_media->images->standard_resolution->height,
                            'width' => $instagram_media->images->standard_resolution->width,
                            'video_thumbnail' => null
                        ];
                        WallMedia::create([
                           'post_id' => $db_post->id,
                            'type' => 'image',
                            'url' => $instagram_media->images->standard_resolution->url,
                            'height' => $instagram_media->images->standard_resolution->height,
                            'width' => $instagram_media->images->standard_resolution->width,
                            'video_thumbnail' => null
                        ]);
                    }elseif (isset($instagram_media->videos)){// the media is a video
                        exec("ffmpeg -i ".$instagram_media->videos->standard_resolution->url." -ss 00:00:1.435 -vframes 1 ".public_path('uploads/images/wall/'.$id.'_'.$index).".png");
                        $media[] = [
                            'type' => 'video',
                            'url' => $instagram_media->videos->standard_resolution->url,
                            'height' => $instagram_media->videos->standard_resolution->height,
                            'width' => $instagram_media->videos->standard_resolution->width,
                            'video_thumbnail' => secure_url('uploads/images/wall/'.$id.'_'.$index.'.png')
                        ];
                        WallMedia::create([
                            'post_id' => $db_post->id,
                            'type' => 'video',
                            'url' => $instagram_media->videos->standard_resolution->url,
                            'height' => $instagram_media->videos->standard_resolution->height,
                            'width' => $instagram_media->videos->standard_resolution->width,
                            'video_thumbnail' => secure_url('uploads/images/wall/'.$id.'_'.$index.'.png')
                        ]);
                    }
                }
            }elseif (!isset($instagram_post->videos) && !isset($instagram_post->carousel_media)){// post with 1 image
                $media[] = [
                    'type' => 'image',
                    'url' => $instagram_post->images->standard_resolution->url,
                    'height' => $instagram_post->images->standard_resolution->height,
                    'width' => $instagram_post->images->standard_resolution->width,
                    'video_thumbnail' => null
                ];
                WallMedia::create([
                    'post_id' => $db_post->id,
                    'type' => 'image',
                    'url' => $instagram_post->images->standard_resolution->url,
                    'height' => $instagram_post->images->standard_resolution->height,
                    'width' => $instagram_post->images->standard_resolution->width,
                    'video_thumbnail' => null
                ]);
            }elseif (isset($instagram_post->videos)){//post with 1 video
                $media[] = [
                    'type' => 'video',
                    'url' => $instagram_post->videos->standard_resolution->url,
                    'height' => $instagram_post->videos->standard_resolution->height,
                    'width' => $instagram_post->videos->standard_resolution->width,
                    'video_thumbnail' => $instagram_post->images->standard_resolution->url
                ];
                WallMedia::create([
                    'post_id' => $db_post->id,
                    'type' => 'video',
                    'url' => $instagram_post->videos->standard_resolution->url,
                    'height' => $instagram_post->videos->standard_resolution->height,
                    'width' => $instagram_post->videos->standard_resolution->width,
                    'video_thumbnail' => $instagram_post->images->standard_resolution->url
                ]);
            }

            $post['platform_id'] = $id;
            $post['platform_type'] = $platform_type;
            $post['post_source_url'] = $instagram_post->link;
            $post['created_datetime'] = $created_datetime;
            $post['title'] = $title;
            $post['description'] = $description;
            $post['user']['image'] = $user_image;
            $post['user']['name'] = $user_name;
            $post['media'] = $media;
            $posts[] = $post;
        }
        try {
            $fb = new \Facebook\Facebook([
                'default_graph_version' => 'v3.3',
            ]);
            $access_token = $fb->getApp()->getAccessToken();
            $access_token = env('FACEBOOK_ACCESS_TOKEN');
            $fb->setDefaultAccessToken($fb->getApp()->getAccessToken());
            $user_profile = $fb->get(
                '/'.env('FACEBOOK_PAGE_ID').'?fields=picture,name',
                "{$access_token}"
            );
            $user_profile = $user_profile->getBody();
            $user_profile = json_decode($user_profile);
            $user_name = strpos($user_profile->name,'onald') !== false ? $user_profile->name : "McDonald's Lebanon";
            $user_image = $user_profile->picture->data->url;
            $response = $fb->get(
                '/'.env('FACEBOOK_PAGE_ID').'/feed?fields=from,created_time,full_picture,icon,message,picture,message_tags,shares,place,status_type,attachments,child_attachments',
                "{$access_token}"
            );

        } catch(FacebookResponseException $e) {
            return parent::return_error('Graph returned an error: ' . $e->getMessage(), 488, 'Cannot Get Facebbok Posts');
        } catch(FacebookSDKException $e) {
            return parent::return_error('Facebook SDK returned an error: ' . $e->getMessage(), 488, 'Cannot Get Facebbok Posts');
        }
        $response = $response->getBody();
        $response = json_decode($response);
        $platform_type = 'facebook';
        foreach ($response->data as $facebook_post){
            $created_datetime = strtotime($facebook_post->created_time);
            $id = $facebook_post->id;
            $description = isset($facebook_post->message) ? $facebook_post->message : null;
//            $user_image = isset($facebook_post->picture) ? $facebook_post->picture : null;
//            $user_name = isset($facebook_post->from) && isset($facebook_post->from->name) ? $facebook_post->from->name : null;
            $db_post = WallPost::wherePlatformType($platform_type)->wherePlatformId($id)->first();
            $media = [];
            $post_source_url = 'https://www.facebook.com/McDonaldsLebanon/?fref=ts';
            if($db_post){
                /**
                 * delete old media to avoid expired media
                 */
                WallMedia::wherePostId($db_post->id)->delete();
            }else{
                $db_post = WallPost::create([
                    'post_source' => 'social_media',
                    'platform_type' => $platform_type,
                    'platform_id' => $id,
                    'created_datetime' => $created_datetime,
                    'post_source_url' => $post_source_url,
                    'title' => null,
                    'description' => $description,
                    'user_name' => $user_name,
                    'user_image' => $user_image,
                ]);
            }


            if(isset($facebook_post->attachments) && isset($facebook_post->attachments->data)){
                $attachment_data = $facebook_post->attachments->data[0];
                $post_source_url = isset($attachment_data->url) ? $attachment_data->url : 'https://www.facebook.com/McDonaldsLebanon/?fref=ts';
                $db_post->post_source_url = $post_source_url;
                $db_post->save();
                if(isset($attachment_data) && isset($attachment_data->subattachments) && isset($attachment_data->subattachments->data)){
                    $sub_attachments = $attachment_data->subattachments->data;
                    foreach ($sub_attachments as $attachment){
                        /**
                         * ignore all non-original video
                         */
                        if(strpos($attachment->type,'video') !== false && !isset($attachment->media->source)){
                            continue;
                        }
                        $width = null;
                        $height = null;
                        if(strpos($attachment->type,'video') !== false && isset($attachment->media->image->src)){
                            list($width, $height, $type, $attr) = getimagesize($attachment->media->image->src);
                        }
                        $media[] = [
                            'type' => strpos($attachment->type,'video') !== false ? 'video' : 'image',
                            'url' => strpos($attachment->type,'video') !== false && isset($attachment->media) && isset($attachment->media->source) ? $attachment->media->source : $attachment->media->image->src,
                            'height' => strpos($attachment->type,'video') !== false && isset($attachment->media)  ? $height : $attachment->media->image->height,
                            'width' => strpos($attachment->type,'video') !== false && isset($attachment->media)  ? $width : $attachment->media->image->width,
                            'video_thumbnail' => strpos($attachment->type,'video') !== false ? $attachment->media->image->src : null
                        ];
                        WallMedia::create([
                            'post_id' => $db_post->id,
                            'type' => strpos($attachment->type,'video') !== false ? 'video' : 'image',
                            'url' => strpos($attachment->type,'video') !== false && isset($attachment->media) && isset($attachment->media->source) ? $attachment->media->source : $attachment->media->image->src,
                            'height' => strpos($attachment->type,'video') !== false && isset($attachment->media)  ? $height : $attachment->media->image->height,
                            'width' => strpos($attachment->type,'video') !== false && isset($attachment->media)  ? $width : $attachment->media->image->width,
                            'video_thumbnail' => strpos($attachment->type,'video') !== false ? $attachment->media->image->src : null
                        ]);
                    }
                }else{// one media only
                    /**
                     * ignore all posts that contains non-original video
                     */
                    if(strpos($attachment_data->type,'video') !== false && !isset($attachment_data->media->source)){
                        continue;
                    }
                    $width = null;
                    $height = null;
                    if(strpos($attachment_data->type,'video') !== false && isset($attachment_data->media->image->src)){
                        list($width, $height, $type, $attr) = getimagesize($attachment_data->media->image->src);
                    }
                    $media[] = [
                        'type' => strpos($attachment_data->type,'video') !== false ? 'video' : 'image',
                        'url' => strpos($attachment_data->type,'video') !== false && isset($attachment_data->media) && isset($attachment_data->media->source) ? $attachment_data->media->source : $attachment_data->media->image->src,
                        'height' => strpos($attachment_data->type,'video') !== false && isset($attachment_data->media)  ? $height : $attachment_data->media->image->height,
                        'width' => strpos($attachment_data->type,'video') !== false && isset($attachment_data->media)  ? $width : $attachment_data->media->image->width,
                        'video_thumbnail' => strpos($attachment_data->type,'video') !== false ? $attachment_data->media->image->src : null
                    ];
                    WallMedia::create([
                        'post_id' => $db_post->id,
                        'type' => strpos($attachment_data->type,'video') !== false ? 'video' : 'image',
                        'url' => strpos($attachment_data->type,'video') !== false && isset($attachment_data->media) && isset($attachment_data->media->source) ? $attachment_data->media->source : $attachment_data->media->image->src,
                        'height' => strpos($attachment_data->type,'video') !== false && isset($attachment_data->media)  ? $height : $attachment_data->media->image->height,
                        'width' => strpos($attachment_data->type,'video') !== false && isset($attachment_data->media)  ? $width : $attachment_data->media->image->width,
                        'video_thumbnail' => strpos($attachment_data->type,'video') !== false ? $attachment_data->media->image->src : null
                    ]);
                }
            }else{
                // no media exists for this post
            }
            $post['platform_id'] = $id;
            $post['platform_type'] = $platform_type;
            $post['post_source_url'] = $post_source_url;
            $post['created_datetime'] = $created_datetime;
            $post['title'] = null;
            $post['description'] = $description;
            $post['user']['image'] = $user_image;
            $post['user']['name'] = $user_name;
            $post['media'] = $media;
            $posts[] = $post;
        }

        shuffle($posts);
        return response()->json($posts);
    }
    
    public function retrieve_orders(Request $request) {
        $orders = CheckoutLog::whereNull('retrieve_info')->orderBy('created_at','desc')->get();
        foreach ($orders as $order){
            if($order){
                $insurance_request = $order->insurance;
                if($order->session_id != null){
                    $data = [
                        'apiUsername' => 'merchant.' . env('PAYMENT_MERCHANT_NUMBER'),
                        'apiPassword' => env('PAYMENT_API_PASSWORD'),
                        'apiOperation' => 'RETRIEVE_ORDER',
                        'merchant' => env('PAYMENT_MERCHANT_NUMBER'),
                        'order.id' => $order->id,
                    ];
                    $info['data'] = $data;
                    $session_info = parent::manage_session($info);
                    $order->retrieve_info = $session_info;
                    $order->save();
                    parse_str($session_info, $session_details);
                    if(strtoupper($session_details['result']) == 'SUCCESS' && strtoupper($session_details['status']) != 'FAILED' && strtoupper($session_details['merchant']) == strtoupper(env('PAYMENT_MERCHANT_NUMBER'))){
                        $order->payment_status = 'success';
                        $order->done = true;
                        $order->save();
                        if($insurance_request){
                            $insurance_request->status = 'paid';
                            $insurance_request->save();
                        }
                    }else{
                        if($insurance_request){
                            $insurance_request->status = 'failed';
                            $insurance_request->save();
                        }
                        $order->status = 'failed';
                        $order->save();
                    }
                }
            }
        }
        return response()->json('Orders Updated!');
    }

    public function update_dashboard(Request $request){
        $values = [];

        $registered_users = Users::count();
        $verified_users = Users::where('is_verified','=',1)->count();

        $pending_barters = Barter::where('status','pending')->count();
        $accepted_barters = Barter::where('status','accepted')->count();
        $completed_barters = Barter::where('status','completed')->count();
        $rejected_barters = Barter::where('status','rejected')->count();

        $values['registered_users'] = [
            'meta_type' => 'registered_users',
            'meta_name' => 'Registered Users',
            'meta_value' => $registered_users,
            'meta_col' => 6,
            'meta_icon' => 'fas fa-users',
            'meta_route' => '/admin/users?type=2',
            'meta_unit' => 'Users'
        ];

        $values['verified_users'] = [
            'meta_type' => 'verified_users',
            'meta_name' => 'Verified Users',
            'meta_value' => $verified_users,
            'meta_col' => 6,
            'meta_icon' => 'fas fa-users',
            'meta_route' => '/admin/users?type=2',
            'meta_unit' => 'Users'
        ];

        $values['pending_barters'] = [
            'meta_type' => 'pending_barters',
            'meta_name' => 'Pending Barters',
            'meta_value' => $pending_barters,
            'meta_col' => 3,
            'meta_icon' => 'fas fa-tag',
            'meta_route' => '/admin/items',
            'meta_unit' => 'Barters'
        ];

        $values['completed_barters'] = [
            'meta_type' => 'completed_barters',
            'meta_name' => 'Completed Barters',
            'meta_value' => $completed_barters,
            'meta_col' => 3,
            'meta_icon' => 'fas fa-tag',
            'meta_route' => '/admin/items',
            'meta_unit' => 'Barters'
        ];

        $values['accepted_barters'] = [
            'meta_type' => 'accepted_barters',
            'meta_name' => 'Accepted Barters',
            'meta_value' => $accepted_barters,
            'meta_col' => 3,
            'meta_icon' => 'fas fa-tag',
            'meta_route' => '/admin/items',
            'meta_unit' => 'Barters'
        ];

        $values['rejected_barters'] = [
            'meta_type' => 'rejected_barters',
            'meta_name' => 'Rejected Barters',
            'meta_value' => $rejected_barters,
            'meta_col' => 3,
            'meta_icon' => 'fas fa-tag',
            'meta_route' => '/admin/items',
            'meta_unit' => 'Barters'
        ];

        $sales = Order::whereStatus('delivered')->sum('total_price');
        $sales = $sales ? $sales : 0;
        $values['sales'] = [
            'meta_type' => 'sales',
            'meta_name' => 'Sales',
            'meta_value' => number_format($sales,0,'',','),
            'meta_col' => 6,
            'meta_icon' => 'fas fa-hand-holding-usd',
            'meta_route' => '/admin/orders',
            'meta_unit' => 'KWD'
        ];

        $product = PackageItem::select('item_id')
            ->selectRaw('sum(quantity) AS count')
            ->groupBy('item_id')
            ->orderBy('count','desc')
            ->limit(1)
            ->first();
        $restaurant = PackageItem::select('restaurant_id')
            ->selectRaw('sum(quantity) AS count')
            ->groupBy('restaurant_id')
            ->orderBy('count','desc')
            ->limit(1)
            ->first();
        $supplier = PackageItem::select('supplier_food_group_id')
            ->selectRaw('sum(quantity) AS count')
            ->groupBy('supplier_food_group_id')
            ->orderBy('count','desc')
            ->limit(1)
            ->first();
        $product_id = null;
        $product_name = null;
        if($product && $product->item){
            $product_id = $product->item->id;
            $product_name = $product->item->name_en;
        }
        $values['most_purchased_product'] = [
            'meta_type' => 'most_purchased_product',
            'meta_name' => 'Most Purchased Product',
            'meta_value' => $product_name,
            'meta_col' => 4,
            'meta_icon' => 'fas fa-tag',
            'meta_route' => '/admin/items?item_id='.$product_id,
            'meta_unit' => null
        ];
        $restaurant_id = null;
        $restaurant_name = null;
        if($restaurant && $restaurant->restaurant){
            $restaurant_id = $restaurant->restaurant->id;
            $restaurant_name = $restaurant->restaurant->name_en;
        }
        $values['best_sales_restaurant'] = [
            'meta_type' => 'best_sales_restaurant',
            'meta_name' => 'Best Sales Restaurant',
            'meta_value' => $restaurant_name,
            'meta_col' => 4,
            'meta_icon' => 'fas fa-tag',
            'meta_route' => '/admin/orders?restaurant_id='.$restaurant_id,
            'meta_unit' => null
        ];
        $supplier_id = null;
        $supplier_name = null;
        if($supplier && $supplier->supplier_food_group){
            $supplier_id = $supplier->supplier_food_group->id;
            $supplier_name = $supplier->supplier_food_group->name_en;
        }
        $values['best_sales_supplier'] = [
            'meta_type' => 'best_sales_supplier',
            'meta_name' => 'Best Sales Supplier',
            'meta_value' => $supplier_name,
            'meta_col' => 4,
            'meta_icon' => 'fas fa-tag',
            'meta_route' => '/admin/orders?supplier_id='.$supplier_id,
            'meta_unit' => null
        ];

        foreach (OrderStatus::get() as $order_status){
            $values[$order_status->status.'_orders'] = [
                'meta_type' => $order_status->status.'_orders',
                'meta_name' => $order_status->label .' Orders',
                'meta_value' => Order::whereStatus($order_status->status)->count(),
                'meta_col' => 6,
                'meta_icon' => 'fas fa-cart-plus',
                'meta_route' => '/admin/orders?status='.$order_status->status,
                'meta_unit' => 'Orders'
            ];
        }

        foreach ($values as $key => $value){
            $stat = Dashboard::whereMetaType($key)->first();
            if($stat){
                $stat->update($value);
            }else{
                $max_order = DB::table('dashboard')->max('meta_order');
                $order = $max_order + 1;
                Dashboard::create($value + [
                    'meta_order' => $order,
                ]);
            }
        }
        return response()->json(['status' => 'Dashboard Statistics Synced Successfully']);
    }

    public function check_expired_items(Request $request){
        $now = Carbon::now()->format('Y-m-d');
        Item::whereDate('expired_at','<',$now)->whereNotIn('status',['expired'])->get()->map(function($value, $key) {
            if($user = Users::where('id','=',$value->user_id)->where('is_blocked','=',0)->first()){
                if($user_push = UserPush::whereUsersId($user->id)->first()){
                    $player_id = $user_push->player_id;
                    $contents = ["en" => "Unfortunately, your $value->name_en post is expired. Go ahead and renew it!"];
                    $headings = ["en" => "Expired Post!"];

                    $info['contents'] = $contents;
                    $info['headings'] = $headings;
                    $info['player_ids'] = [$player_id];
                    if(isset($request->date)){
                        $info['send_after'] = Carbon::createFromTimestamp($request->date)->format('Y-m-d H:i:s');
                    }
                    $result = parent::oneSignal($info);
                    $code = Controller::number_generator(6);

                    Inbox::create([
                        'user_id' => $item->user_id,
                        'title' => "Expired Post!",
                        'message' => "Unfortunately, your $value->name_en post is expired. Go ahead and renew it!",
                        'image' => null,
                        'type' => 'targeted',
                        'code' => $code
                    ]);
                }
            }

//            $info = [];
//            $info['to'] = $to = explode(',', env('EXPIRY_DATE_EMAILS'));
//            $info['icon'] = 'expired.png';
//            $info['message'] = 'Expired Posts';
//            $info['item'] = $value;
//            $info['item_link'] = secure_url('admin/items?item_id='.$value->id);
//            $info['title'] = env('APP_NAME') . ' | Expired Posts';
//            Mail::send('emails.expired_item', ['data' => $info], function ($message) use ($info) {
//                $message->subject($info['title']);
//                $message->to($info['to']);
//            });
            $value->status = 'expired';
            $value->is_available = false;
            $value->save();
            return $value;
        })->values();
        return response()->json(['status' => 'Alerts sent of expired Items']);
    }

    public function matchingRecords()
    {
        $users = Item::where('expired_at','>=', Carbon::now())->where('id','>',0)->get()->pluck('user_id','user_id');
        // UserMatchingRecord::where('id', '>', 0)->delete();
        foreach($users as $user){
            // $result = $this->matchingResult($user);
            $result = (new MatchingController)->matchingResult($user);
            $set_data = new UserMatchingRecord();
            $set_data->user_id = $user;
            $set_data->matching_records = json_encode($result);
            $set_data->save();
        }
        return redirect()->back()->with('success', 'Matching Successfully Done');
    }
    public function get_matches()
    {

        $data = UserMatchingRecord::where('user_id',5)->orderBy('created_at','desc')->first();
            if($data){
                $ids = json_decode($data->matching_records,true);
                $ids_ordered = implode(',', $ids);
                $match_query = Item::whereIn('id',$ids)->where('is_available', 1)->orderByRaw("FIELD(id, $ids_ordered)");
                $currency = env('APP_CURRENCY');
                
                $matches = $match_query->get()->map(function ($value, $key) use ($currency) {
                    $value->is_favorite = false;
                    // if($user){
                    //     if(UserFavorite::whereMetaName('items')->whereMetaId($value->id)->whereUserId($user->id)->first()){
                    //         $value->is_favorite = true;
                    //     }
                    // }
                    $value->currency = $currency;
                    $value->post_type = "services";
                    return $value;
                })->values();
            }
            // dd($data);
        return $matches;
    }

    public function send_push_for_expired(Request $request){
        if($user_push = UserPush::whereUsersId($request->user_id)->first()){
            $players_ids[] = $user_push->player_id;
            $contents = ["en" => "your post will expire soon"];
            $headings = ["en" => "Reactivate your post"];
            $info['contents'] = $contents;
            $info['headings'] = $headings;
            $info['player_ids'] = $players_ids;
            $info['data'] = ['type' => 'renew_post'];
            $result = parent::oneSignal($info);
        }

        return response()->json($result);
    }

    public function check_expired_posts(Request $request){
        $today = Carbon::now();
        $tomorrow_start = Carbon::now()->tomorrow()->startOfDay();
        $tomorrow_end = Carbon::now()->tomorrow()->endOfDay();
        $before_32_days = Carbon::now()->subDays('32')->startOfDay();


        // $items_to_be_notified = Item::whereBetween('expired_at', [$tomorrow_start, $tomorrow_end])->get();
        // foreach($items_to_be_notified as $noti){
        //     $user_push = UserPush::whereUsersId($noti->user_id)->first();
        //     $players_ids[] = $user_push->player_id;
        //     $contents = ["en" => "your post will expire soon"];
        //     $headings = ["en" => "Reactivate your post"];
        //     $info['contents'] = $contents;
        //     $info['headings'] = $headings;
        //     $info['player_ids'] = $players_ids;
        //     $info['data'] = ['type' => 'renew_post'];
        //     $result = parent::oneSignal($info);
        // }

        // $items_to_change_status = Item::where('expired_at','<',$today)->get();
        // foreach($items_to_change_status as $status){
        //     $status->status = 'expired';
        //     $status->save();
        // }

        $items_to_be_auto_deleted = Item::where('expired_at','<',$before_32_days)->get();
        foreach($items_to_be_auto_deleted as $delete){
            $delete->delete();
        }
    }
}
