<?php

namespace App\Http\Controllers\Admin;

use App\Models\Api\AtyappCategory;
use App\Models\Api\Category;
use App\Models\Api\CategoryFish;
use App\Models\Api\CorporateActivity;
use App\Models\Api\CorporateActivityItem;
use App\Models\Api\CuratedCollection;
use App\Models\Api\CustomizationOption;
use App\Models\Api\Fish;
use App\Models\Api\FishSize;
use App\Models\Api\Game;
use App\Models\Api\GiftCard;
use App\Models\Api\GiftCardPrice;
use App\Models\Api\Item;
use App\Models\Api\ItemCustomization;
use App\Models\Api\Media;
use App\Models\Api\Player;
use App\Models\Api\Restaurant;
use App\Models\Api\RestaurantEventType;
use App\Models\Api\RestaurantTiming;
use App\Models\Api\Service;
use App\Models\Api\ServiceCategory;
use App\Models\Api\Songs;
use App\Models\Api\Station;
use App\Models\Api\StationPump;
use App\Models\Api\StationService;
use App\Models\Api\SubCategory;
use App\Models\Api\SubSubCategory;
use App\Models\Api\SupplierSubCategory;
use App\Models\Api\UserCart;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\View\View;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;


/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class SupplierSubCategoriesController
 */
class SupplierSubCategoriesController extends Controller
{
    var $parent_section_name = 'categories';
    var $section_name = 'sub_categories';
    var $sub_section_name = 'supplier_sub_categories';
    var $display_name;
    public function __construct()
    {
        parent::__construct();
        $this->display_name = str_replace('_',' ',ucwords($this->sub_section_name,'_'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $category = SubCategory::findOrFail($id);
        $items = SubSubCategory::whereSubCategoryId($id)->latest()->get();
        return view('pages.'.$this->parent_section_name.'.'.$this->section_name.'.'.$this->sub_section_name.'.index', compact('category','items'))
            ->with('section_name', $this->section_name)
            ->with('content_header', $this->display_name);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $category = SubCategory::findOrFail($id);
        return view('pages.'.$this->parent_section_name.'.'.$this->section_name.'.'.$this->sub_section_name.'.create',compact('category'))
            ->with('content_header', 'Add Sub Sub Category');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id)
    {
        $category = SubCategory::findOrFail($id);

        $max_order = SubSubCategory::whereSubCategoryId($id)->max('item_order');
        $order = $max_order + 1;
        SubSubCategory::create([
            'sub_category_id' => $id,
            'item_order' => $order,
            'is_published' => 1,
            'name_en' => isset($request->name_en) && !empty($request->name_en) ? trim($request->name_en) : null,
            'name_ar' => isset($request->name_ar) && !empty($request->name_ar) ? trim($request->name_ar) : null,
//            'description_en' => isset($request->description_en) && !empty($request->description_en) ? trim($request->description_en) : null,
//            'description_ar' => isset($request->description_ar) && !empty($request->description_ar) ? trim($request->description_ar) : null,
        ]);

        return redirect()->route('admin.'.$this->section_name.'.'.$this->sub_section_name.'.index',$id)
            ->with('success', 'Successfully create the '.$this->display_name);
    }

    public function publish($item_id){
        $item = SubSubCategory::findOrFail($item_id);
        if($item){
            $item->is_published = !$item->is_published;
            $item->save();
        }
        return redirect()->back()
            ->with('success', $this->display_name.' Successfully updated');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $category_id
     * @param  int  $item_id
     * @return \Illuminate\Http\Response
     */
    public function edit($category_id,$item_id)
    {
        $item = SubSubCategory::findOrFail($item_id);
        $category = SubCategory::findOrFail($category_id);
        return view('pages.'.$this->parent_section_name.'.'.$this->section_name.'.'.$this->sub_section_name.'.edit', compact('item','category'))
            ->with('content_header', 'Update '.$this->display_name);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$category_id,$item_id)
    {
        $item = SubSubCategory::findOrFail($item_id);
        $item->update([
            'item_order' => $request->item_order,
            'name_en' => isset($request->name_en) && !empty($request->name_en) ? trim($request->name_en) : null,
            // 'name_ar' => isset($request->name_ar) && !empty($request->name_ar) ? trim($request->name_ar) : null,
            // 'description_en' => isset($request->description_en) && !empty($request->description_en) ? trim($request->description_en) : null,
            // 'description_ar' => isset($request->description_ar) && !empty($request->description_ar) ? trim($request->description_ar) : null,
        ]);

        return redirect()->route('admin.'.$this->section_name.'.'.$this->sub_section_name.'.index',$category_id)
            ->with('success', 'Successfully updated the '.$this->display_name);
    }


    public function destroy($category_id,$item_id)
    {
        $item = SubSubCategory::findOrFail($item_id);
        // if($item->item_order != null){
        //     $all_items = SupplierSubCategory::whereCategoryId($category_id)->where('item_order','>',$item->item_order)->get();
        //     foreach ($all_items as $single_item){
        //         $single_item->item_order = $single_item->item_order - 1;
        //         $single_item->save();
        //     }
        // }
        $item->delete();

        return redirect()->route('admin.'.$this->section_name.'.'.$this->sub_section_name.'.index',$category_id)
            ->with('success', 'Successfully deleted the '.$this->display_name);
    }
}

