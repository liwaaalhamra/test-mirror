<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TermsRequest;
use App\Models\Api\Area;
use App\Models\Api\BonusSubject;
use App\Models\Api\Client;
use App\Models\Api\EventType;
use App\Models\Api\Feature;
use App\Models\Api\Game;
use App\Models\Api\HappyLikelyList;
use App\Models\Api\Industry;
use App\Models\Api\PlaceTypes;
use App\Models\Api\Player;
use App\Models\Api\ProductType;
use App\Models\Api\Region;
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
 * Class AreasController
 */
class AreasController extends Controller
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
        $items = Area::get();
        return view('pages.areas.index', compact('items'))
            ->with('section_name', 'areas')
            ->with('content_header', 'Areas');
    }

    /**
     * Show the create page.
     *
     * @return View
     */
    public function create()
    {
        $regions = Region::all();
        return view('pages.areas.create',compact('regions'))
            ->with('content_header', 'Add Area');
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
            'region_id' => 'required',
        ]);
        Area::create([
            'name_en' => isset($request->name_en) && !empty($request->name_en) ? trim($request->name_en) : null,
            'name_ar' => isset($request->name_ar) && !empty($request->name_ar) ? trim($request->name_ar) : null,
            'region_id' => $request->get('region_id'),
        ]);
        return redirect()->route('admin.areas.index')
            ->with('success', 'Area Successfully added');
    }

    /**
     * Show the edit page.
     *
     * @param $id
     * @return View
     */
    public function edit($id) /*: View*/
    {
        $regions = Region::all();
        $item = Area::findOrFail($id);
        return view('pages.areas.edit', compact('item','regions'))
            ->with('content_header', 'Update Area');
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
            'region_id' => 'required',
        ]);
        $item = Area::findOrFail($id);
        $item->update([
            'name_en' => isset($request->name_en) && !empty($request->name_en) ? trim($request->name_en) : null,
            'name_ar' => isset($request->name_ar) && !empty($request->name_ar) ? trim($request->name_ar) : null,
            'region_id' => $request->get('region_id'),
        ]);
        return redirect()->route('admin.areas.index')
            ->with('success', 'Area Successfully updated');
    }

    /**
     * Remove.
     *
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id) /*: RedirectResponse*/
    {
        $item = Area::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.areas.index')
            ->with('success', 'Area Successfully deleted');
    }

}

