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
use App\Models\Api\ItemSize;
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
 * Class SizesController
 */
class SizesController extends Controller
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
        $items = ItemSize::get();
        return view('pages.sizes.index', compact('items'))
            ->with('section_name', 'sizes')
            ->with('content_header', 'Preparation Methods');
    }

    /**
     * Show the create page.
     *
     * @return View
     */
    public function create()
    {
        return view('pages.sizes.create')
            ->with('content_header', 'Add Preparation Method');
    }

    /**
     * Store.
     *
     * @param Request $request
     * @return View
     */
    public function store(Request $request)
    {

        ItemSize::create([
            'name' => $request->get('name'),
        ]);
        return redirect()->route('admin.sizes.index')
            ->with('success', 'Preparation Method Successfully added');
    }

    /**
     * Show the edit page.
     *
     * @param $id
     * @return View
     */
    public function edit($id) /*: View*/
    {
        $item = ItemSize::findOrFail($id);
        return view('pages.sizes.edit', compact('item'))
            ->with('content_header', 'Update Preparation Method');
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
        $item = ItemSize::findOrFail($id);
        $item->update([
            'name' => $request->get('name'),
        ]);
        return redirect()->route('admin.sizes.index')
            ->with('success', 'Preparation Method Successfully updated');
    }

    /**
     * Remove.
     *
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id) /*: RedirectResponse*/
    {
        $item = ItemSize::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.sizes.index')
            ->with('success', 'Preparation Method Successfully deleted');
    }

}

