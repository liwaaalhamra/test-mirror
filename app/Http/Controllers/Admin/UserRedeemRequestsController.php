<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TermsRequest;
use App\Models\Api\Game;
use App\Models\Api\Gift;
use App\Models\Api\PlaceTypes;
use App\Models\Api\Player;
use App\Models\Api\RedeemItem;
use App\Models\Api\Report;
use App\Models\Api\Suggestion;
use App\Models\Api\Support;
use App\Models\Api\Terms;
use App\Models\Api\UserPoints;
use App\Models\Api\UserPush;
use App\Models\Api\UserRedeemRequest;
use App\Models\Api\Users;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\View\View;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;

/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class UserRedeemRequestsController
 */
class UserRedeemRequestsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
//        if(auth()->user() == null || (!in_array('all',$this->sections))){
//            abort(401);
//        }
    }
    /**
     * @return View
     */
    public function index(Request $request) /*: View*/
    {
        $items = UserRedeemRequest::latest()
            ->get();

        return view('pages.user_redeem_requests.index', compact('items'))
            ->with('section_name', 'user_redeem_requests')
            ->with('content_header', 'User Redeem Requests');
    }

    /**
     * Accept/Reject
     * @param integer $id
     * @return string
     */
    public function action($id){
            $record = UserRedeemRequest::whereId($id)->first();
            if($record->status == 'approved'){
                $record->status = 'pending';
            }else{
                $record->status = 'approved';
                /**
                 * decrease quantity
                 * push to user
                 * decrease points from user
                 * update user total_points
                 */
                $user = Users::where('id','=',$record->users_id)->first();
                $old_points = $user->total_points;
                $new_points = $user->total_points - $record->item->points;
                $contents = ["en" => 'Redeem Request Accepted'];
                $headings = ["en" => 'Your redeem request has been accepted. you had '.$old_points.' points, and you have now '.$new_points.' points'];
                $user_push = UserPush::whereUsersId($record->users_id)->first();
                if($user_push){
                    $player_ids = [$user_push->player_id];
                    $info['contents'] = $contents;
                    $info['headings'] = $headings;
                    $info['player_ids'] = $player_ids;
                    $result = parent::oneSignal($info);
                }

                $redeem_item = RedeemItem::whereId($record->item->id)->first();
                $redeem_item->quantity = $redeem_item->quantity - 1;
                $redeem_item->save();

                $user->points()->create([
                    'meta_value' => $record->id,
                    'meta_name' => 'redeem',
                    'points' => -$record->item->points,
                    'description' => $record->item->title
                ]);
                $total = 0;

                foreach ($user->points as $row) {
                    $total += $row->points;
                }
                $user->total_points = $total;
                $user->save();
            }
            $record->save();
        return redirect()->route('admin.user_redeem_requests.index')
            ->with('success', 'Restaurant Status Successfully updated');
    }

    /**
     * Show the create page.
     *
     * @return View
     */
    public function create()
    {

    }

    /**
     * Store.
     *
     * @param Request $request
     * @return View
     */
    public function store(Request $request)
    {

    }

    /**
     * Show the edit page.
     *
     * @param $id
     * @return View
     */
    public function edit($id) /*: View*/
    {

    }

    /**
     * Update.
     *
     * @param $id
     * @param Request $request
     * @return RedirectResponse
     */
    public function update($id, Request $request) /*: RedirectResponse*/
    {

    }

    /**
     * Remove.
     *
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id) /*: RedirectResponse*/
    {

    }

}

