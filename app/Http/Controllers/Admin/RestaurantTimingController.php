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
use App\Models\Api\RestaurantTiming;
use App\Models\Api\Service;
use App\Models\Api\ServiceCategory;
use App\Models\Api\Songs;
use App\Models\Api\Station;
use App\Models\Api\StationPump;
use App\Models\Api\StationService;
use App\Models\Api\SubCategory;
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
 * Class RestaurantTimingController
 */
class RestaurantTimingController extends Controller
{
    var $section_name = 'restaurants';
    var $sub_section_name = 'timing';
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
        $restaurant = Restaurant::findOrFail($id);
        $items = RestaurantTiming::whereItemId($id)->orderBy('type','desc')->orderBy('day','asc')->get();
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
        $restaurant = Restaurant::findOrFail($id);
        $types = ['opening','delivery'];
        $days = [1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday',7=>'Sunday'];

        return view('pages.'.$this->section_name.'.'.$this->sub_section_name.'.create',compact('restaurant','types','days'))
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
        $restaurant = Restaurant::findOrFail($id);
        $time_from = null;
        if(!empty($request->start_datetime)){
            $time_from = Carbon::createFromFormat('g:i A', $request->start_datetime)->timestamp;
        }
        $time_to = null;
        if(!empty($request->end_datetime)){
            $time_to = Carbon::createFromFormat('g:i A', $request->end_datetime)->timestamp;
        }
        RestaurantTiming::updateOrCreate(['type'=>$request->type,'day'=>$request->day,'item_id'=>$id],[
            'time_from' => $time_from,
            'time_to' => $time_to,
        ]);

        return redirect()->route('admin.'.$this->section_name.'.'.$this->sub_section_name.'.index',$id)
            ->with('success', 'Successfully create the '.$this->display_name);
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
        $item = RestaurantTiming::findOrFail($item_id);
        $restaurant = Restaurant::findOrFail($category_id);
        $types = ['opening','delivery'];
        $days = [1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday',7=>'Sunday'];

        return view('pages.'.$this->section_name.'.'.$this->sub_section_name.'.edit', compact('item','restaurant','types','days'))
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
        $item = RestaurantTiming::findOrFail($item_id);
        if(RestaurantTiming::whereItemId($item_id)->where('id','<>',$item_id)->whereType($request->type)->where('day','=',$request->day)->first()){
            return redirect()->back()
                ->with('warning', $this->display_name.' is already exists');
        }
        $time_from = null;
        if(!empty($request->start_datetime)){
            $time_from = Carbon::createFromFormat('g:i A', $request->start_datetime)->timestamp;
        }
        $time_to = null;
        if(!empty($request->end_datetime)){
            $time_to = Carbon::createFromFormat('g:i A', $request->end_datetime)->timestamp;
        }
        $item->update([
            'day' => $request->day,
            'type' => $request->type,
            'time_from' => $time_from,
            'time_to' => $time_to,
        ]);

        return redirect()->route('admin.'.$this->section_name.'.'.$this->sub_section_name.'.index',$category_id)
            ->with('success', 'Successfully updated the '.$this->display_name);
    }


    public function destroy($category_id,$item_id)
    {
        $item = RestaurantTiming::findOrFail($item_id);
        $item->delete();

        return redirect()->route('admin.'.$this->section_name.'.'.$this->sub_section_name.'.index',$category_id)
            ->with('success', 'Successfully deleted the '.$this->display_name);
    }
}

