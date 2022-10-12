<?php

namespace App\Http\Controllers\api;

use App\Models\Api\Inbox;
use App\Models\Api\UserInbox;
use App\Models\Api\UserPush;
use App\Http\Requests\PushRequest;
use App\Helpers\PushHelper;
use App\Models\Api\Users;
use App\Traits\HITrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;

class PushController extends Controller
{
    use HITrait;
    public function set_user_push(Request $request) {
        $user = $this->validate_user($request);
        if(!Input::has('player_id')){
            return parent::return_error('Player ID required!',400,'messages.missing_parameter');
        }
        if(!parent::validate_player_id($request->player_id)){
            return parent::return_error('Invalid Player ID',401,'messages.invalid_parameters');
        }
        /**
         * get onesignal user details
         */
        $details = parent::oneSignalViewUser($request->player_id);
        if($user_push = UserPush::where('type','=','user')->whereUsersId($user->id)->first()){
            $user_push->device_model = isset($details['error']) ? null : $details['device_model'];
            $user_push->device_type = isset($details['error']) ? null : $details['device_type'];
            $user_push->identifier = isset($details['error']) ? null : $details['identifier'];
            $user_push->details = isset($details['error']) ? $details['error'] : json_encode($details);
            $user_push->player_id = Input::get('player_id');
            $user_push->type = 'user';
            $user_push->save();
        }else{
            $user_push = UserPush::create([
                'player_id' => $request->get('player_id'),
                'users_id' => $user->id,
                'type' => 'user',
                'device_model' => isset($details['error']) ? null : $details['device_model'],
                'device_type' => isset($details['error']) ? null : $details['device_type'],
                'identifier' => isset($details['error']) ? null : $details['identifier'],
                'details' => isset($details['error']) ? $details['error'] : json_encode($details),
            ]);
        }
        $message = utf8_decode(utf8_encode(Lang::get('messages.request_submitted_successfully')));
        return response()->json(['status' => $message]);

    }

    public function inbox(Request $request) {
        $user = $this->validate_user($request);
        // whereUserType('user')->
        $query = Inbox::where(function ($query) use($user) {
            $query->where(function ($query) use($user) {
                $query->whereUserId($user->id)->whereType('targeted');
            })->orwhere('type','=','bulk');
        })->latest();
        if(Input::has('search')){
            $search = Input::get('search');
            $query->where(function($query) use ($search){
                $query->whereRaw("LOWER(title) like ? ", [ "%".strtolower($search)."%"])
                    ->orwhereRaw("LOWER(message) like ? ", [ "%".strtolower($search)."%"]);
            });
        }
        $messages = $query->orderBy('is_read', 'desc')->get()->map(function ($value, $key) use ($user) {
            // UserInbox::updateOrCreate(['inbox_id' => $value->id,'user_id' => $user->id]);
            return $value;
        })->values();
        return response()->json($messages);
    }

    public function getMessage(Request $request) {
        $user = $this->validate_user($request);
        
        UserInbox::updateOrCreate(['inbox_id' => $request->id,'user_id' => $user->id]);
        $message = Inbox::updateOrCreate(['id' => $request->id, 'user_id' => $user->id],[
            'is_read' => 1
        ]);

        return response()->json($message);
    }

    public function unread_count(Request $request) {
        $user = $this->validate_user($request);
        // $count = Inbox::whereUserType('user')->doesnthave('push_read')->where(function ($query) use($user) {

        // $count = Inbox::doesnthave('push_read')->where(function ($query) use($user) {
        //     $query->where(function ($query) use($user) {
        //         $query->whereUserId($user->id)->whereType('targeted');
        //     })->orwhere('type','=','bulk');
        // })->count();

        $count = Inbox::where(function ($query) use($user) {
            $query->where(function ($query) use($user) {
                $query->whereUserId($user->id)->whereIsRead(0);
            });
        })->count();

        return response()->json(['unread_count' => $count]);
    }

    public function send_push(Request $request){
        if(!Input::has('title')){
            return parent::return_error('Title required!',400,'messages.missing_parameter');
        }
        if(!Input::has('message')){
            return parent::return_error('Message required!',400,'messages.missing_parameter');
        }
        if(!Input::has('employee_id')){
            return parent::return_error('Employee ID required!',400,'messages.missing_parameter');
        }
        if($employee = Users::where('employee_id','=',$request->employee_id)->where('is_blocked','=',0)->first()){
            if($user_push = UserPush::whereUsersId($employee->id)->first()){
                $player_id = $user_push->player_id;
                $contents = ["en" => trim($request->message)];
                $headings = ["en" => trim($request->title)];

                $info['contents'] = $contents;
                $info['headings'] = $headings;
                $info['player_ids'] = [$player_id];
                if(isset($request->date)){
                    $info['send_after'] = Carbon::createFromTimestamp($request->date)->format('Y-m-d H:i:s');
                }
                $result = parent::oneSignal($info);
            }
        }
    }

    public function sync_users(Request $request){
        foreach ($request->all() as $user){
            if(!isset($user['employee_id'])){
                continue;
            }
            if($existing_user = Users::where('employee_id','=',intval($user['employee_id']))->first()){
                if(isset($user['first_name'])){
                    $existing_user->first_name = $user['first_name'];
                }
                if(isset($user['last_name'])){
                    $existing_user->last_name = $user['last_name'];
                }
                if(isset($user['department'])){
                    $existing_user->department = $user['department'];
                }
                if(isset($user['cost_center'])){
                    $existing_user->cost_center = $user['cost_center'];
                }
                if(isset($user['job_title'])){
                    $existing_user->job_title = $user['job_title'];
                }
                if(isset($user['primary_phone'])){
                    $existing_user->primary_phone = $user['primary_phone'];
                }
                if(isset($user['secondary_phone'])){
                    $existing_user->secondary_phone = $user['secondary_phone'];
                }
                if(isset($user['street'])){
                    $existing_user->street = $user['street'];
                }
                if(isset($user['building'])){
                    $existing_user->building = $user['building'];
                }
                if(isset($user['floor'])){
                    $existing_user->floor = $user['floor'];
                }
                if(isset($user['gender'])){
                    $existing_user->gender = $user['gender'];
                }
                if(isset($user['dob'])){
                    $existing_user->dob = $user['dob'];
                }
                if(isset($user['address'])){
                    $existing_user->address = $user['address'];
                }
                if(isset($user['email'])){
                    $existing_user->email = $user['email'];
                }
                if(isset($user['is_blocked'])){
                    if($user['is_blocked'] == 'true'){
                        $existing_user->is_blocked = true;
                    }else{
                        $existing_user->is_blocked = false;
                    }
                }
                $existing_user->save();
            }else{
                $generated_password = str_random(10);
                $new_user = Users::create([
                    'employee_id' => intval($user['employee_id']),
                    'first_name' => isset($user['first_name']) ? $user['first_name'] : null,
                    'last_name' => isset($user['last_name']) ? $user['last_name'] : null,
                    'cost_center' => isset($user['cost_center']) ? $user['cost_center'] : null,
                    'department' => isset($user['department']) ? $user['department'] : null,
                    'job_title' => isset($user['job_title']) ? $user['job_title'] : null,
                    'primary_phone' => isset($user['primary_phone']) ? $user['primary_phone'] : null,
                    'secondary_phone' => isset($user['secondary_phone']) ? $user['secondary_phone'] : null,
                    'street' => isset($user['street']) ? $user['street'] : null,
                    'building' => isset($user['building']) ? $user['building'] : null,
                    'floor' => isset($user['floor']) ? $user['floor'] : null,
                    'gender' => isset($user['gender']) ? $user['gender'] : null,
                    'dob' => isset($user['dob']) ? $user['dob'] : null,
                    'address' => isset($user['address']) ? $user['address'] : null,
                    'email' => isset($user['email']) ? $user['email'] : null,
                    'password' => $generated_password,
                    'password_clear_text' => $generated_password
                ]);

                if(isset($user['email'])){
                    if(filter_var($user['email'], FILTER_VALIDATE_EMAIL)){
                        $data = [
                            'to' => $user['email'],
                            'full_name' => isset($user['full_name']) ? $user['full_name'] : 'Sir/Ms',
                            'title' => 'Welcome',
                            'generated_password' => $generated_password,
                            'message1' => 'Welcome to Mcdonalds app, please use the following password:',
                            'message3' => utf8_decode(utf8_encode(Lang::get('messages.sincerely'))),
                            'message4' => utf8_decode(utf8_encode(Lang::get('messages.team'))),
                        ];

                        Mail::send('emails.welcome', ['data' => $data], function ($message) use ($data) {
                            $message->subject(env('APP_NAME') .' | '.$data['title']);
                            $message->to($data['to']);
                        });
                    }

                }
            }
        }
        return response()->json(['status' => 'Users updated successfully']);
    }

}
