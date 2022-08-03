<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TermsRequest;
use App\Models\Api\ContactUs;
use App\Models\Api\Game;
use App\Models\Api\PlaceTypes;
use App\Models\Api\Player;
use App\Models\Api\Report;
use App\Models\Api\Suggestion;
use App\Models\Api\Support;
use App\Models\Api\Terms;
use App\Models\Api\UserBonus;
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
 * Class ContactUsController
 */
class ContactUsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
//        if(auth()->user() == null || (!in_array('all',$this->sections))){
//            abort(401);
//        }
    }
    /**
     * Show SuggestionController.
     * @return View
     */
    public function index(Request $request) /*: View*/
    {
        $items = ContactUs::orderBy('id','desc')
            ->get();

        return view('pages.contact_us.index', compact('items'))
            ->with('section_name', 'contact_us')
            ->with('content_header', 'Requests');
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

