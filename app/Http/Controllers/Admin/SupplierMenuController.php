<?php

namespace App\Http\Controllers\Admin;

use App\Models\Api\AtyappCategory;
use App\Models\Api\Category;
use App\Models\Api\CategoryFish;
use App\Models\Api\CorporateActivity;
use App\Models\Api\CorporateActivityItem;
use App\Models\Api\Fish;
use App\Models\Api\FishSize;
use App\Models\Api\Game;
use App\Models\Api\GiftCard;
use App\Models\Api\GiftCardPrice;
use App\Models\Api\Item;
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
use App\Models\Api\SupplierFoodGroup;
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
 * Class SupplierMenuController
 */
class SupplierMenuController extends Controller
{
    var $section_name = 'suppliers';
    var $sub_section_name = 'menu';
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
        $restaurant = SupplierFoodGroup::findOrFail($id);
        $items = SubCategory::whereCategoryType('supplier')->whereCategoryId($id)->latest()->get();
        return view('pages.'.$this->section_name.'.'.$this->sub_section_name.'.index', compact('restaurant','items'))
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
        $restaurant = SupplierFoodGroup::findOrFail($id);

        return view('pages.'.$this->section_name.'.'.$this->sub_section_name.'.create',compact('restaurant'))
            ->with('content_header', 'Add '.$this->display_name);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id)
    {
        $restaurant = SupplierFoodGroup::findOrFail($id);

        $max_order = DB::table('sub_categories')->whereCategoryType('supplier')->whereCategoryId($id)->max('item_order');
        $order = $max_order + 1;
        SubCategory::create([
            'category_id' => $id,
            'category_type' => 'supplier',
            'item_order' => $order,
            'name_en' => isset($request->name_en) && !empty($request->name_en) ? trim($request->name_en) : null,
            'name_ar' => isset($request->name_ar) && !empty($request->name_ar) ? trim($request->name_ar) : null,
            'subtitle_en' => isset($request->subtitle_en) && !empty($request->subtitle_en) ? trim($request->subtitle_en) : null,
            'subtitle_ar' => isset($request->subtitle_ar) && !empty($request->subtitle_ar) ? trim($request->subtitle_ar) : null,
        ]);

        return redirect()->route('admin.'.$this->section_name.'.'.$this->sub_section_name.'.index',$id)
            ->with('success', 'Successfully create the '.$this->display_name);
    }

    public function publish($item_id){
        $item = SubCategory::findOrFail($item_id);
        if($item){
            $item->is_published = !$item->is_published;
            $item->save();
        }
        return redirect()->route('admin.'.$this->section_name.'.'.$this->sub_section_name.'.index',$item->category_id)
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
        $item = SubCategory::findOrFail($item_id);
        $restaurant = SupplierFoodGroup::findOrFail($category_id);

        return view('pages.'.$this->section_name.'.'.$this->sub_section_name.'.edit', compact('item','restaurant'))
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
        $item = SubCategory::findOrFail($item_id);
        $item->update([
            'item_order' => $request->item_order,
            'name_en' => isset($request->name_en) && !empty($request->name_en) ? trim($request->name_en) : null,
            'name_ar' => isset($request->name_ar) && !empty($request->name_ar) ? trim($request->name_ar) : null,
            'subtitle_en' => isset($request->subtitle_en) && !empty($request->subtitle_en) ? trim($request->subtitle_en) : null,
            'subtitle_ar' => isset($request->subtitle_ar) && !empty($request->subtitle_ar) ? trim($request->subtitle_ar) : null,
        ]);

        return redirect()->route('admin.'.$this->section_name.'.'.$this->sub_section_name.'.index',$category_id)
            ->with('success', 'Successfully updated the '.$this->display_name);
    }


    public function destroy($category_id,$item_id)
    {
        $item = SubCategory::findOrFail($item_id);
        if($item->item_order != null){
            $all_items = SubCategory::whereCategoryType('supplier')->whereCategoryId($category_id)->where('item_order','>',$item->item_order)->get();
            foreach ($all_items as $single_item){
                $single_item->item_order = $single_item->item_order - 1;
                $single_item->save();
            }
        }
        $item->delete();

        return redirect()->route('admin.'.$this->section_name.'.'.$this->sub_section_name.'.index',$category_id)
            ->with('success', 'Successfully deleted the '.$this->display_name);
    }
}

