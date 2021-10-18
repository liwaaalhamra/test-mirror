<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TermsRequest;
use App\Models\Api\Area;
use App\Models\Api\BonusSubject;
use App\Models\Api\Client;
use App\Models\Api\EventType;
use App\Models\Api\Expert;
use App\Models\Api\Feature;
use App\Models\Api\Game;
use App\Models\Api\HappyLikelyList;
use App\Models\Api\Industry;
use App\Models\Api\PlaceTypes;
use App\Models\Api\Player;
use App\Models\Api\ProductType;
use App\Models\Api\Region;
use App\Models\Api\Report;
use App\Models\Api\RoadAssistance;
use App\Models\Api\Store;
use App\Models\Api\Suggestion;
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
 * Class ExpertsController
 */
class ExpertsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
//        $this->sections = implode(',',$this->sections);
//        if(auth()->user() == null || (strpos($this->sections,'all') === false) && strpos($this->sections,'users') === false){
//            abort(401);
//        }
    }
    /**
     * Show .
     * @return View
     */
    public function index(Request $request) /*: View*/
    {
        $items = Expert::get();
        return view('pages.experts.index', compact('items'))
            ->with('section_name', 'experts')
            ->with('content_header', 'Experts');
    }

    public function areas($region_id){
        $areas = Area::whereRegionId($region_id)->pluck('name','id')->tojson();
        return $areas;
    }

    /**
     * Show the create page.
     *
     * @return View
     */
    public function create()
    {
        $regions = Region::get();
        $areas = Area::get();
        return view('pages.experts.create',compact('regions','areas'))
            ->with('content_header', 'Add Expert');
    }

    /**
     * Store.
     *
     * @param Request $request
     * @return View
     */
    public function store(Request $request)
    {

        Expert::create([
            'name' => $request->get('name'),
            'phone_number' => $request->get('phone_number'),
            'area_id' => empty(trim($request->area_id )) ? null : trim($request->area_id ),
            'region_id' => empty(trim($request->region_id )) ? null : trim($request->region_id ),
        ]);
        return redirect()->route('admin.experts.index')
            ->with('success', 'Expert Successfully added');
    }

    /**
     * Show the edit page.
     *
     * @param $id
     * @return View
     */
    public function edit($id) /*: View*/
    {
        $item = Expert::findOrFail($id);
        $regions = Region::get();
        $areas = Area::whereRegionId($item->region_id)->get();
        return view('pages.experts.edit', compact('item','regions','areas'))
            ->with('content_header', 'Update Expert');
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
        $item = Expert::findOrFail($id);
        $item->update([
            'name' => $request->get('name'),
            'phone_number' => $request->get('phone_number'),
            'area_id' => empty(trim($request->area_id )) ? null : trim($request->area_id ),
            'region_id' => empty(trim($request->region_id )) ? null : trim($request->region_id ),
        ]);
        return redirect()->route('admin.experts.index')
            ->with('success', 'Expert Successfully updated');
    }

    /**
     * Remove.
     *
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id) /*: RedirectResponse*/
    {
        $item = Expert::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.experts.index')
            ->with('success', 'Expert Successfully deleted');
    }

}

