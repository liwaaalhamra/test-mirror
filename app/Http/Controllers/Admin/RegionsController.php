<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TermsRequest;
use App\Models\Api\Area;
use App\Models\Api\BonusSubject;
use App\Models\Api\Client;
use App\Models\Api\Country;
use App\Models\Api\EventType;
use App\Models\Api\Feature;
use App\Models\Api\Game;
use App\Models\Api\HappyLikelyList;
use App\Models\Api\Industry;
use App\Models\Api\PlaceTypes;
use App\Models\Api\Player;
use App\Models\Api\ProductType;
use App\Models\Api\Region;
use App\Models\Api\SubRegion;
use App\Models\Api\Location;
use App\Models\Api\Report;
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
 * Class RegionsController
 */
class RegionsController extends Controller
{
    public function __construct()
    {
        parent::__construct();

    }
    /**
     * Show .
     * @return View
     */
    public function index(Request $request) /*: View*/
    {
        $items = SubRegion::get();
        return view('pages.regions.index', compact('items'))
            ->with('section_name', 'regions')
            ->with('content_header', 'Regions');
    }

    /**
     * Show the create page.
     *
     * @return View
     */
    public function create()
    {
        $countries = Location::orderBy('name_en')->get();

        return view('pages.regions.create',compact('countries'))
            ->with('content_header', 'Add Region');
    }

    /**
     * Store.
     *
     * @param Request $request
     * @return View
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name_en' => 'required',
            'country_id' => 'required',
        ]);
        SubRegion::create([
            'name_en' => isset($request->name_en) && !empty($request->name_en) ? trim($request->name_en) : null,
            'name_ar' => isset($request->name_ar) && !empty($request->name_ar) ? trim($request->name_ar) : null,
            'city_id' => $request->get('country_id'),
        ]);
        return redirect()->route('admin.regions.index')
            ->with('success', 'Region Successfully added');
    }

    /**
     * Show the edit page.
     *
     * @param $id
     * @return View
     */
    public function edit($id) /*: View*/
    {
        $item = SubRegion::findOrFail($id);
        $countries = Location::orderBy('name_en')->get();

        return view('pages.regions.edit', compact('item','countries'))
            ->with('content_header', 'Update Region');
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
        $this->validate($request, [
            'name_en' => 'required',
            'country_id' => 'required',
        ]);
        $item = SubRegion::findOrFail($id);
        $item->update([
            'name_en' => isset($request->name_en) && !empty($request->name_en) ? trim($request->name_en) : null,
            'name_ar' => isset($request->name_ar) && !empty($request->name_ar) ? trim($request->name_ar) : null,
            'city_id' => $request->get('country_id'),
        ]);
        return redirect()->route('admin.regions.index')
            ->with('success', 'Region Successfully updated');
    }

    /**
     * Remove.
     *
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id) /*: RedirectResponse*/
    {
        $item = SubRegion::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.regions.index')
            ->with('success', 'Region Successfully deleted');
    }

}

