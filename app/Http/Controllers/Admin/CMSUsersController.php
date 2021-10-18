<?php

namespace App\Http\Controllers\Admin;


use App\Models\Api\CmsUserAccess;
use App\Models\Api\Merchant;
use App\Models\Api\PrivateHospital;
use App\Models\Api\PublicHospital;
use App\Models\Api\Role;
use App\Models\Api\User;
use App\Models\Api\Users;
use App\Models\Api\Venue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Requests\ResetRequest;
use Validator;

/**
 * @author Hassan Itani
 * @package App\Http\Controllers\Admin
 */
class CMSUsersController extends Controller
{
    public function __construct()
    {
        parent::__construct();
//        if(auth()->user() == null || (!in_array('all',$this->sections))){
//            abort(401);
//        }
    }
    /**
     * 
     * @return View
     */
    public function index() /*: View*/
    {
        $users = User::get();
        return view('pages.cms_users.index', compact('users'))
            ->with('section_name', 'cms_users')
            ->with('content_header', 'CMS Users');
    }

    /**
     * Show the create page.
     *
     * @return View
     */
    public function create() /*: View*/
    {
//        $used_merchants = User::whereNotNull('merchant_id')->pluck('merchant_id')->toArray();
//        $merchants = Merchant::whereNotIn('id', $used_merchants)->get();
        $roles = Role::orderBy('id','asc')->get();
        return view('pages.cms_users.create',compact('roles'))
            ->with('content_header', 'Add New CMS User');
    }

    /**
     * Create a new user.
     *
     * @param UserRequest $request
     * @return RedirectResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request) /*: RedirectResponse*/
    {
        $user = User::create([
            'is_admin' => 1,
//            'merchant_id' => $request->merchant_id,
            'first_name' => empty($request->first_name) ? null : $request->first_name,
            'last_name' => empty($request->last_name) ? null : $request->last_name,
            'email' => empty($request->email) ? null : $request->email,
            'role_id' => empty($request->role_id) ? null : $request->role_id,
//            'cost_center' => empty($request->cost_center) ? null : trim($request->cost_center),
            'password' => $request->password,
//            'password_clear_text' => $request->password,
        ]);
        
        return redirect()->route('admin.cms_users.index')
            ->with('success', 'Successfully create a new CMS User');
    }

    /**
     * lock/unlock user
     * @param integer $id
     * @return string
     */
    public function lock($id){
        $user = User::findOrFail($id);
        $user->is_locked = !$user->is_locked;
        $user->save();
        return redirect()->route('admin.cms_users.index')
            ->with('success', 'CMS User Status Successfully updated');
    }


    /**
     * Show the edit page.
     *
     * @param $id
     * @return View
     */
    public function edit($id) /*: View*/
    {
        $user = User::findOrFail($id);
//        $merchants = Merchant::get();
        $roles = Role::orderBy('id','asc')->get();
        return view('pages.cms_users.edit', compact('user','merchants','roles'))
            ->with('content_header', 'Edit CMS User');
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
        $user = User::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'password' => [
                'min:8',             // must be at least 8 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
                'confirmed'
            ],
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error_msg', 'Password must be at least 8 characters,must contain at least one lowercase letter,one uppercase letter,one digit,a special character');
        }
        $data = [
            'role_id' => empty($request->role_id) ? null : $request->role_id,
            'first_name' => empty($request->first_name) ? null : $request->first_name,
            'last_name' => empty($request->last_name) ? null : $request->last_name,
            'email' => empty($request->email) ? null : $request->email,
//            'cost_center' => empty($request->cost_center) ? null : trim($request->cost_center),
        ];

        // if the password field is not empty, updated it. otherwise, ignore it.
        if($request->get('old_password') && $request->get('new_password')) {
            if (Hash::check($request->old_password, $user->password)) {
                $data += [
                    'password' => $request->new_password,
//                    'password_clear_text' => $request->new_password,
                ];
            }else{
                return redirect()->back()
                    ->with('error_msg', 'Wrong Password');
            }
        }

        $user->update($data);
        return redirect()->route('admin.cms_users.index')
            ->with('success', 'Successfully updated the CMS User.');
    }

    /**
     * Delete a driver.
     *
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id) /*: RedirectResponse*/
    {
        $user = User::findOrFail($id);

        $user->delete();

        return redirect()->route('admin.cms_users.index')
            ->with('success', 'Successfully deleted the CMS User');
    }

    /**
     * show reset form.
     *
     * @param $id
     * @return RedirectResponse
     */
    public function reset($id) /*: RedirectResponse*/
    {
        $user = User::findOrFail($id);

        return view('pages.cms_users.reset', compact('user'))
            ->with('content_header', 'Reset Password');
    }

    /**
     * reset user password.
     *
     * @param ResetRequest $request
     * @return RedirectResponse
     */
    public function reset_password(ResetRequest $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->password = $request->password;
//        $user->password_clear_text = $request->password;
        $user->save();
        return redirect()->route('admin.cms_users.index')
            ->with('success', 'Password has been reset successfully');
    }

}
