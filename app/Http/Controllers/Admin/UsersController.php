<?php

namespace App\Http\Controllers\Admin;


use App\Models\Api\Country;
use App\Models\Api\UserPoints;
use App\Models\Api\Users;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Requests\ResetRequest;
use Propaganistas\LaravelPhone\PhoneNumber;
use Validator;

/**
 * @author Hassan Itani
 * @package App\Http\Controllers\Admin
 */
class UsersController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * 
     * @return View
     */
    public function index(Request $request) /*: View*/
    {
        $users = Users::where(function($query) use ($request){
            if ($request->has('type')){
                $type = $request->get('type');
                if($type == '1'){
                    //nothing to do, return all customers
                }elseif ($type == '2'){
                    $query->whereIsVerified(1);
                }elseif ($type == '3'){
                    $query->whereIsBlocked(0)->whereIsVerified(1);
                }elseif ($type == '4'){
                    $query->whereIsBlocked(1)->whereIsVerified(1);
                }elseif ($type == '5'){
                    $query->whereIsVerified(0);
                }
            }
            if(!empty($request->min) && !empty($request->max)){
                $from = Carbon::createFromTimestamp(strtotime($request->min))->format('Y-m-d');
                $to = Carbon::createFromTimestamp(strtotime($request->max))->format('Y-m-d');
                $query->whereDate('created_at','>=',$from)
                    ->whereDate('created_at','<=',$to);
            }
        })->orderby('created_at','desc')->get();

        $selected_type = '1';
        if ($request->has('type')){
            $selected_type = $request->get('type');
        }

        $from = null;
        $to = null;
        if(!empty($request->min) && !empty($request->max)){
            $from = strtotime($request->min);
            $to = strtotime($request->max);
        }
        return view('pages.users.index', compact('users','selected_type','from','to'))
            ->with('section_name', 'users')
            ->with('content_header', 'Customer');
    }

    /**
     * block/unblock user
     * @param integer $id
     * @return string
     */
    public function block($id){
        $user = Users::findOrFail($id);
        $user->is_blocked = !$user->is_blocked;
        $user->save();
        return redirect()->route('admin.users.index')
            ->with('success', 'User Status Successfully updated');
    }

    /**
     * Show the create page.
     *
     * @return View
     */
    public function create() /*: View*/
    {
        $genders = ['male','female','other'];
        $types = ['individual','company'];
        $countries = Country::where('visibility','=',1)->get();
        return view('pages.users.create',compact('types','countries','genders'))
            ->with('content_header', 'Add New Customer');
    }

    /**
     * Create a new user.
     *
     * @param UserRequest $request
     * @return RedirectResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request) /*: RedirectResponse*/
    {
        $dob = null;
        if(!empty($request->dob)){
            $dob = date("Y-m-d", strtotime($request->dob));
        }

        $new_country_code = null;
        $new_phone_number = null;
        $country_id = 368;
        $phone_number = $request->phone_number;
        $country_code = $request->country_code;
        $request->merge([ 'mobile_number' => $country_code.$phone_number]);

        $validator = Validator::make($request->all(), [
            'mobile_number' => 'phone:KW,LB,AE,QA',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error_msg', $validator->errors()->first('mobile_number'));
        }
        $phone_details = PhoneNumber::make($request->mobile_number)->getPhoneNumberInstance();
        $country = Country::where('calling_code','=',$phone_details->getCountryCode())->first();
        $country_id = $country ? $country->id : $country_id;
        $new_country_code = '+'.$phone_details->getCountryCode();
        $new_phone_number = $phone_details->getNationalNumber();
        if(Users::whereCountryCode($new_country_code)->wherePrimaryPhone($new_phone_number)->first()){
            return redirect()->back()
                ->with('error_msg', 'Mobile Number already in use!');
        }
        Users::create([
//            'profile_type_id' => empty($request->type_id) ? null : $request->type_id,
            'employee_id' => empty($request->employee_id) ? null : $request->employee_id,
            'first_name' => empty($request->first_name) ? null : $request->first_name,
            'last_name' => empty($request->last_name) ? null : $request->last_name,
            'access_type' => $request->type_id == 'regular' ? null : $request->type_id,
            'country_id' => $country_id,
            'country_code' => $new_country_code,
            'is_verified' => 1,
            'is_blocked' => 0,
            'primary_phone' => $new_phone_number,
            'blood_type' => empty($request->blood_type) ? null : $request->blood_type,
            'email' => empty($request->email) ? null : strtolower($request->email),
            'gender' => empty($request->gender) ? null : $request->gender,
            'dob' => $dob,
//            'password' => $request->password,
//            'password_clear_text' => $request->password,
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Successfully create a new User');
    }


    /**
     * Show the edit page.
     *
     * @param $id
     * @return View
     */
    public function edit($id) /*: View*/
    {
        $user = Users::findOrFail($id);
        $genders = ['male','female','other'];
        $types = ['individual','company'];
        $countries = Country::where('visibility','=',1)->get();
        return view('pages.users.edit', compact('user','types','countries','genders'))
            ->with('content_header', 'Edit Employee');
    }

    /**
     * Update a user.
     *
     * @param $id
     * @param Request $request
     * @return RedirectResponse
     */
    public function update($id, Request $request) /*: RedirectResponse*/
    {
        $user = Users::findOrFail($id);
        $dob = null;
        if(!empty($request->dob)){
            $dob = date("Y-m-d", strtotime($request->dob));
        }
        $new_country_code = null;
        $new_phone_number = null;
        $country_id = 368;
        $phone_number = $request->phone_number;
        $country_code = $request->country_code;
        $request->merge([ 'mobile_number' => $country_code.$phone_number]);

        $validator = Validator::make($request->all(), [
            'mobile_number' => 'phone:KW,LB,AE,QA',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error_msg', $validator->errors()->first('mobile_number'));
        }
        $phone_details = PhoneNumber::make($request->mobile_number)->getPhoneNumberInstance();
        $country = Country::where('calling_code','=',$phone_details->getCountryCode())->first();
        $country_id = $country ? $country->id : $country_id;
        $new_country_code = '+'.$phone_details->getCountryCode();
        $new_phone_number = $phone_details->getNationalNumber();

        $data = [
//            'profile_type_id' => empty($request->type_id) ? null : $request->type_id,
            'employee_id' => empty($request->employee_id) ? null : $request->employee_id,
            'first_name' => empty($request->first_name) ? null : $request->first_name,
            'last_name' => empty($request->last_name) ? null : $request->last_name,
            'access_type' => $request->type_id,
            'country_id' => $country_id,
            'country_code' => $new_country_code,
            'primary_phone' => $new_phone_number,
            'blood_type' => empty($request->blood_type) ? null : $request->blood_type,
            'email' => empty($request->email) ? null : strtolower($request->email),
            'gender' => empty($request->gender) ? null : $request->gender,
            'dob' => $dob,
        ];
        if(Users::whereCountryCode($new_country_code)->wherePrimaryPhone($new_phone_number)->where('id','<>',$id)->first()){
            return redirect()->back()
                ->with('error_msg', 'Mobile Number already in use!');
        }
//        if($request->get('old_password') && $request->get('new_password')) {
//            if ($user->password_clear_text == $request->old_password) {
//                $data += [
//                    'password' => $request->new_password,
//                    'password_clear_text' => $request->new_password,
//                ];
//            }else{
//                return redirect()->back()
//                    ->with('error_msg', 'Wrong Password');
//            }
//        }

        $user->update($data);
//        if(!empty($request->points) && $request->points != 0){
//            UserPoints::create([
//                'users_id' => $user->id,
//                'points' => $request->points,
//                'meta_name' => 'cms',
//                'meta_value' => auth()->user()->id,
//                'description' => empty($request->description) ? null : $request->description,
//            ]);
//
//            $total = 0;
//            foreach ($user->points as $row) {
//                $total += $row->points;
//            }
//            $user->total_points = $total;
//            $user->save();
//        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Successfully updated the user.');
    }

    /**
     * Delete a driver.
     *
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id) /*: RedirectResponse*/
    {
        $user = Users::findOrFail($id);

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Successfully deleted the user');
    }

    /**
     * show reset form.
     *
     * @param $id
     * @return RedirectResponse
     */
    public function reset($id) /*: RedirectResponse*/
    {
        $user = Users::findOrFail($id);

        return view('pages.users.reset', compact('user'))
            ->with('content_header', 'Reset Password');
    }

    /**
     * reset user password.
     *
     * @param ResetRequest $request
     * @return RedirectResponse
     */
    public function reset_password(ResetRequest $request) /*: RedirectResponse*/
    {
        $user = Users::findOrFail($request->user_id);
        $user->password = $request->password;
        $user->password_clear_text = $request->password;
        $user->save();
        return redirect()->route('admin.users.index')
            ->with('success', 'Password has been reset successfully');
    }

}
