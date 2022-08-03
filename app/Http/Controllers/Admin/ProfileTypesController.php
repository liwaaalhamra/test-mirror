<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TermsRequest;
use App\Models\Api\BonusSubject;
use App\Models\Api\Client;
use App\Models\Api\ContactUsCategory;
use App\Models\Api\EventType;
use App\Models\Api\Game;
use App\Models\Api\PlaceTypes;
use App\Models\Api\Player;
use App\Models\Api\ProfileType;
use App\Models\Api\Report;
use App\Models\Api\Store;
use App\Models\Api\Suggestion;
use App\Models\Api\TalkToUsCategory;
use App\Models\Api\Terms;
use App\Models\Api\TypeBrand;
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
 * Class ProfileTypesController
 */
class ProfileTypesController extends Controller
{
    public function __construct()
    {
        parent::__construct();
//        if(auth()->user() == null || (!in_array('all',$this->sections))){
//            abort(401);
//        }
    }
    /**
     * Show .
     * @return View
     */
    public function index(Request $request) /*: View*/
    {
        $items = ProfileType::get();
        return view('pages.profile_types.index', compact('items'))
            ->with('section_name', 'profile_types')
            ->with('content_header', 'Profile Types');
    }

    /**
     * Show the create page.
     *
     * @return View
     */
    public function create()
    {
        return view('pages.profile_types.create')
            ->with('content_header', 'Add Profile Type');
    }

    /**
     * Store.
     *
     * @param Request $request
     * @return View
     */
    public function store(Request $request)
    {

        ProfileType::create([
            'name' => $request->get('name'),
            'start_color' => trim($request->get('start_color')),
            'end_color' => trim($request->get('end_color'))
        ]);
        return redirect()->route('admin.profile_types.index')
            ->with('success', 'Profile Type Successfully added');
    }

    /**
     * Show the edit page.
     *
     * @param $id
     * @return View
     */
    public function edit($id) /*: View*/
    {
        $item = ProfileType::findOrFail($id);
        return view('pages.profile_types.edit', compact('item'))
            ->with('content_header', 'Update Profile Type');
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
        $item = ProfileType::findOrFail($id);
        $item->update([
            'name' => $request->get('name'),
            'start_color' => trim($request->get('start_color')),
            'end_color' => trim($request->get('end_color'))
        ]);
        return redirect()->route('admin.profile_types.index')
            ->with('success', 'Profile Type Successfully updated');
    }

    /**
     * Remove.
     *
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id) /*: RedirectResponse*/
    {
        $item = ProfileType::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.profile_types.index')
            ->with('success', 'Profile Type Successfully deleted');
    }

}

