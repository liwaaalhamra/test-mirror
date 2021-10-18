<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TermsRequest;
use App\Models\Api\Game;
use App\Models\Api\Gift;
use App\Models\Api\PlaceTypes;
use App\Models\Api\Player;
use App\Models\Api\Report;
use App\Models\Api\Suggestion;
use App\Models\Api\Support;
use App\Models\Api\Terms;
use App\Models\Api\UserPoints;
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
 * Class UserGiftsController
 */
class UserGiftsController extends Controller
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
        $items = UserPoints::whereMetaName('gifts')->latest()
            ->get();

        return view('pages.user_gifts.index', compact('items'))
            ->with('section_name', 'user_gifts')
            ->with('content_header', 'User Gifts');
    }

    /**
     * Accept/Reject
     * @param integer $id
     * @return string
     */
    public function action($id,$action){
        if($action == 'accept'){
            $record = UserPoints::whereId($id)->first();
            $record->points = Gift::whereSlug('selfie')->first()->points;
            $record->save();
            $total = 0;
            $user = Users::where('id','=',$record->users_id)->first();
            foreach ($user->points as $row) {
                $total += $row->points;
            }
            $user->total_points = $total;
            $user->save();
        }
        return redirect()->route('admin.user_gifts.index')
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

