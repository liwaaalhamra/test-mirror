<?php

namespace App\Http\Controllers\Admin;

use App\Models\Api\Inbox;
use App\Models\Api\Restaurant;
use App\Models\Api\Notification;
use App\Models\Api\SupplierFoodGroup;
use App\Models\Api\UserPush;
use App\Models\Api\Users;
use App\Models\Api\UsersAccess;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;

/**
 * @author Hassan Itani
 * Class NotificationController
 *
 */
class NotificationController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show the push page.
     *
     * @return View
     */
    public function push() /*: View*/
    {
        $users = Users::join('user_push','users_id','=','users.id')->get();

        $products = Restaurant::orderBy('id','desc')->get();
        $suppliers = SupplierFoodGroup::orderBy('id','desc')->get();
        return view('pages.notification.push',compact('users','products','suppliers'))
            ->with('section_name', 'notification')
            ->with('content_header', 'Notification: Push');
    }

    public function send_single_push(Request $request)
    {
        $player_ids = [];
        $data = [];
        $info = [];
        $data['type'] = 'bulk';
        /**
         * Get All Player IDs
         */
        $users = Users::join('user_push','users_id','=','users.id')
            ->wherein('users.id',$request->users)
            ->select('user_push.users_id','user_push.player_id')
            ->distinct()
            ->get();
        $code = Controller::number_generator(6);
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . 'push';
        $fileName = null;
        // check if a file exist and is valid.
        if($request->hasFile('image')) {
            if($request->file('image')->isValid()) {
                $file = $request->file('image');
                try{
                    $data1 = file_get_contents($file->getPathname());
                    $base64 = base64_encode($data1);
                    $bin = base64_decode($base64);
                    $im = imageCreateFromString($bin);
                }catch (\Exception $ex){
                    return redirect()->back()
                        ->with('error_msg', 'Document Corrupted');
                }
                $validator = Validator::make($request->all(), [
                    'image' => 'mimetypes:image/jpeg,image/png',
                ]);
                if($validator->fails()){
                    return redirect()->back()
                        ->with('warning', 'Invalid Media Type');
                }
                $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $fileName);
                /**
                 * this image will be displayed in the notification
                 */
                //Android
                $info['big_picture'] = secure_url('public/uploads/images/push/'.$fileName);
                //iOS
                $media_id = 'id'.str_random(5);
                $info['ios_attachments'] = [$media_id => secure_url('public/uploads/images/push/'.$fileName)];
                /**
                 * at the same time, we will send the image in "data" in case
                 * they want to display it int the application
                 */
                list($width, $height, $type, $attr) = getimagesize(secure_url('public/uploads/images/push/'.$fileName));
                $data['image_width'] = $width;
                $data['image_height'] = $height;
                $data['image_url'] = secure_url('public/uploads/images/push/'.$fileName);
            }
        }

        /**
         * if we want to send badge
         * there are 2 types
         * The first option if we want to set a customized number in the badge
         * the second option if we want to increase/decrease  by "x" number every time we send a push
         */
        if($request->badge){
            if($request->badge == 'manual'){
                $info['ios_badgeType'] = 'SetTo';
                $info['ios_badgeCount'] = '10';// you can set the number you want
            }elseif ($request->badge == 'auto'){
                $info['ios_badgeType'] = 'Increase';
                $info['ios_badgeCount'] = '10';// if number negative it will decrease
            }
        }else{// by default we will follow the default behavior which is increasing the badge by one each time we send a push
            $info['ios_badgeType'] = 'Increase';
            $info['ios_badgeCount'] = '1';
        }

        /**
         * if targeted then send the options in "info" key
         */
        if($request->info){
            /**
             * check example below
             */
            $types = $request->info['types'];
            if(in_array('all',$types)){
                $users = Users::join('user_push','users_id','=','users.id')
                    ->select('user_push.users_id','user_push.player_id')
                    ->distinct()
                    ->get();
            }else{
                $users = Users::join('user_push','users_id','=','users.id')
                    ->whereIn('department',$types)
                    ->select('user_push.users_id','user_push.player_id')
                    ->distinct()
                    ->get();
            }
        }
        if(count($users) > 0) {
            foreach ($users as $player){
                Inbox::create([
                    'user_id' => $player->users_id,
                    'title' => trim(request()->title),
                    'message' => trim(request()->message),
                    'image' => $fileName,
                    'type' => 'targeted',
                    'code' => $code
                ]);
                $player_ids[] = $player->player_id;
            }
        }
        if(!empty($player_ids)){

            $contents = ["en" => trim(request()->message)];
            if($request->message_ar){
                $contents['ar'] = trim($request->message_ar);
            }
            $headings = ["en" => trim(request()->title)];
            if($request->title_ar){
                $headings['ar'] = trim($request->title_ar);
            }
            //extra data to send to application

            if($request->info['data']){
                $data = array_push($data,$request->info['data']);
            }

            $info['contents'] = $contents;
            $info['headings'] = $headings;
            $info['data'] = $data;

            $chunks = intval(ceil(count($player_ids)%2000));
            $player_ids_chunks = array_chunk($player_ids, 2000);
            $notification_message = '';
            foreach ($player_ids_chunks as $chunk){
                $info['player_ids'] = $chunk;
                $result = parent::oneSignal($info);
                if(!empty($result['error'])){
                    $notification_message .= 'Error: '.$result['error'].'<br>';
                }else{
                    $status = $result['status'];
                    $message = $result['message'];
                    $debugger = $result['debugger'];
                    if($status == 'OK'){
                        $notification_message .= 'Success: '.$debugger.'<br>';
                    }else{
                        if($status == 'OK_WITH_ERRORS'){
                            $notification_message .= 'Success: '.$debugger.'<br>';
                        }else{
                            $notification_message .= 'Error: '.$debugger.'<br>';

                        }
                    }
                }
            }
            return redirect()->route('admin.notification.push')
                ->with('success', $notification_message);
        }else{
            return redirect()->route('admin.notification.index')
                ->with('warning', 'No Users Found');
        }

    }


    public function send_push(Request $request)
    {
        $player_ids = [];
        $included_segments = [];
        /**
         * excluded will not received even if they are in included
         */
        $excluded_segments = [];
        $data = [];
        $info = [];
        $data['type'] = 'bulk';

        $code = Controller::number_generator(6);

        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . 'push';
        $fileName = null;
        // check if a file exist and is valid.
        if($request->hasFile('image')) {
            if($request->file('image')->isValid()) {
                $file = $request->file('image');
                $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $fileName);
                /**
                 * this image will be displayed in the notification
                 */
                //Android
                $info['big_picture'] = secure_url('public/uploads/images/push/'.$fileName);
                //iOS
                $media_id = 'id'.str_random(5);
                $info['ios_attachments'] = [$media_id => secure_url('public/uploads/images/push/'.$fileName)];
                /**
                 * at the same time, we will send the image in "data" in case
                 * they want to display it int the application
                 */
                list($width, $height, $type, $attr) = getimagesize(secure_url('public/uploads/images/push/'.$fileName));
                $data['image_width'] = $width;
                $data['image_height'] = $height;
                $data['image_url'] = secure_url('public/uploads/images/push/'.$fileName);
            }
        }

        if($request->segments){// could be "All" "Active Users" "Inactive Users"
            $included_segments = [$request->segments];
            /**
             * excluded segments is not implemented yet
             */
        }else{
            /**
             * Get All Player IDs
             */
            $users = Users::join('user_push','users_id','=','users.id')
                ->select('user_push.users_id','user_push.player_id')->get();

            /**
             * if targeted then send the options in "info" key
             */
            if($request->info){
                /**
                 * check example below
                 */
                $types = $request->info['types'];
                if(in_array('all',$types)){
                    $users = Users::join('user_push','users_id','=','users.id')
                        ->select('user_push.users_id','user_push.player_id')
                        ->distinct()
                        ->get();
                }else{
                    $users = Users::join('user_push','users_id','=','users.id')
                        ->whereIn('department',$types)
                        ->select('user_push.users_id','user_push.player_id')
                        ->distinct()
                        ->get();
                }
            }
            if(count($users) > 0) {
                foreach ($users as $player){
                    $player_ids[] = $player->player_id;
                }
            }
            if(empty($player_ids)){
                return redirect()->route('admin.notification.index')
                    ->with('warning', 'No Users Found');
            }
        }

        $contents = ["en" => trim(request()->message)];
        if($request->message_ar){
            $contents['ar'] = trim($request->message_ar);
        }
        $headings = ["en" => trim(request()->title)];
        if($request->title_ar){
            $headings['ar'] = trim($request->title_ar);
        }
        //extra data to send to application
        $meta_type = null;
        $meta_value = null;
        if(isset($request->info['data'])){
            if(isset($request->info['data']['item_id'])){
                $meta_type = $data['type'] = $request->info['data']['meta_type'];
                $meta_value = $request->info['data']['item_id'];
            }
            $data = array_merge($data,$request->info['data']);
        }

        $info['contents'] = $contents;
        $info['headings'] = $headings;
        $info['data'] = $data;
        $info['player_ids'] = $player_ids;
        $info['included_segments'] = $included_segments;
        $info['excluded_segments'] = $excluded_segments;
        $count_player_ids = count($player_ids);
        Inbox::create([
            'user_id' => null,
            'title' => trim(request()->title),
            'message' => trim(request()->message),
            'image' => $fileName,
            'type' => 'bulk',
            'code' => $code,
            'meta_type' => $meta_type,
            'meta_value' => $meta_value,
        ]);
        if($count_player_ids > 0) {//means this is bulk for logged in users only
            $chunks = intval(ceil($count_player_ids % 2000));
            $player_ids_chunks = array_chunk($player_ids, 2000);
            $notification_message = '';
            foreach ($player_ids_chunks as $chunk) {
                $info['player_ids'] = $chunk;
                $result = parent::oneSignal($info);
                if(!empty($result['error'])){
                    $notification_message .= 'Error: '.$result['error'].'<br>';
                }else{
                    $status = $result['status'];
                    $message = $result['message'];
                    $debugger = $result['debugger'];
                    if($status == 'OK'){
                        $notification_message .= 'Success: '.$debugger.'<br>';
                    }else{
                        if($status == 'OK_WITH_ERRORS'){
                            $notification_message .= 'Success: '.$debugger.'<br>';
                        }else{
                            $notification_message .= 'Error: '.$debugger.'<br>';

                        }
                    }
                }
            }
            return redirect()->route('admin.notification.push')
                ->with('success', $notification_message);
//            return redirect()->route('admin.notification.push')
//                ->with('success', 'Push Sent Successfully to '.$count_player_ids.' devices');
        }else{
            $result = parent::oneSignal($info);
            if(!empty($result['error'])){
                return redirect()->route('admin.notification.index')
                    ->with('error_msg', $result['error']);
            }else{
                $status = $result['status'];
                $message = $result['message'];
                $debugger = $result['debugger'];
                if($status == 'OK'){

                    return redirect()->route('admin.notification.index')
                        ->with('success', $debugger);
                }else{
                    if($status == 'OK_WITH_ERRORS'){
                        return redirect()->route('admin.notification.index')
                            ->with('warning', $debugger);
                    }else{
                        return redirect()->route('admin.notification.index')
                            ->with('error_msg', $debugger);
                    }
                }
            }
        }
    }



}
