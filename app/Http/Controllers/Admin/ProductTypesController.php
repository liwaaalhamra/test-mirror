<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TermsRequest;
use App\Models\Api\BonusSubject;
use App\Models\Api\Brand;
use App\Models\Api\Client;
use App\Models\Api\ContactUsCategory;
use App\Models\Api\EventType;
use App\Models\Api\Game;
use App\Models\Api\PlaceTypes;
use App\Models\Api\Player;
use App\Models\Api\ProductType;
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
 * Class ProductTypesController
 */
class ProductTypesController extends Controller
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
        $items = ProductType::get();
        return view('pages.product_types.index', compact('items'))
            ->with('section_name', 'product_types')
            ->with('content_header', 'Product Types');
    }

    /**
     * Show the create page.
     *
     * @return View
     */
    public function create()
    {
        return view('pages.product_types.create')
            ->with('content_header', 'Add Product Type');
    }

    /**
     * Store.
     *
     * @param Request $request
     * @return View
     */
    public function store(Request $request)
    {

        ProductType::create([
            'name_en' => isset($request->name_en) && !empty($request->name_en) ? trim($request->name_en) : null,
            'name_ar' => isset($request->name_ar) && !empty($request->name_ar) ? trim($request->name_ar) : null,
        ]);
        return redirect()->route('admin.product_types.index')
            ->with('success', 'Product Type Successfully added');
    }

    /**
     * Show the edit page.
     *
     * @param $id
     * @return View
     */
    public function edit($id) /*: View*/
    {
        $item = ProductType::findOrFail($id);
        return view('pages.product_types.edit', compact('item'))
            ->with('content_header', 'Update Product Type');
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
        $item = ProductType::findOrFail($id);
        $item->update([
            'name_en' => isset($request->name_en) && !empty($request->name_en) ? trim($request->name_en) : null,
            'name_ar' => isset($request->name_ar) && !empty($request->name_ar) ? trim($request->name_ar) : null,
        ]);
        return redirect()->route('admin.product_types.index')
            ->with('success', 'Product Type Successfully updated');
    }

    /**
     * Remove.
     *
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id) /*: RedirectResponse*/
    {
        $item = ProductType::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.product_types.index')
            ->with('success', 'Product Type Successfully deleted');
    }

}

