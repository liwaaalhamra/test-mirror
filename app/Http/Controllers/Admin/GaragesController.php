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
use App\Models\Api\Garage;
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
 * Class GaragesController
 */
class GaragesController extends Controller
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
        $items = Garage::get();
        return view('pages.garages.index', compact('items'))
            ->with('section_name', 'garages')
            ->with('content_header', 'Garages');
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
        return view('pages.garages.create',compact('regions','areas'))
            ->with('content_header', 'Add Garage');
    }

    /**
     * Store.
     *
     * @param Request $request
     * @return View
     */
    public function store(Request $request)
    {

        Garage::create([
            'name' => $request->get('name'),
            'type' => $request->get('type'),
            'phone_number' => $request->get('phone_number'),
            'area_id' => empty(trim($request->area_id )) ? null : trim($request->area_id ),
            'region_id' => empty(trim($request->region_id )) ? null : trim($request->region_id ),
        ]);
        return redirect()->route('admin.garages.index')
            ->with('success', 'Garage Successfully added');
    }

    /**
     * Show the edit page.
     *
     * @param $id
     * @return View
     */
    public function edit($id) /*: View*/
    {
        $item = Garage::findOrFail($id);
        $regions = Region::get();
        $areas = Area::whereRegionId($item->region_id)->get();
        return view('pages.garages.edit', compact('item','regions','areas'))
            ->with('content_header', 'Update Garage');
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
        $item = Garage::findOrFail($id);
        $item->update([
            'name' => $request->get('name'),
            'type' => $request->get('type'),
            'phone_number' => $request->get('phone_number'),
            'area_id' => empty(trim($request->area_id )) ? null : trim($request->area_id ),
            'region_id' => empty(trim($request->region_id )) ? null : trim($request->region_id ),
        ]);
        return redirect()->route('admin.garages.index')
            ->with('success', 'Garage Successfully updated');
    }

    /**
     * Remove.
     *
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id) /*: RedirectResponse*/
    {
        $item = Garage::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.garages.index')
            ->with('success', 'Garage Successfully deleted');
    }

}

