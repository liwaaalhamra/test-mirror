<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BannerRequest;
use App\Http\Requests\EventRequest;
use App\Models\Api\Ad;
use App\Models\Api\Banner;
use App\Models\Api\BusinessNews;
use App\Models\Api\Category;
use App\Models\Api\CorporateActivity;
use App\Models\Api\Coupon;
use App\Models\Api\Event;
use App\Models\Api\Game;
use App\Models\Api\Gift;
use App\Models\Api\LocateUs;
use App\Models\Api\Media;
use App\Models\Api\OfferPromotion;
use App\Models\Api\Player;
use App\Models\Api\Service;
use App\Models\Api\Songs;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\View\View;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;

/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class PromoCodesController
 */
class PromoCodesController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Coupon::orderBy('start_date','asc')->get();
        $accessible_sections = $this->sections;
        return view('pages.promo_codes.index', compact('items','accessible_sections'))
            ->with('section_name', 'promo_codes')
            ->with('content_header', 'Promo Codes');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = [];
        $types['fixed'] = 'Fixed Value on Total Cart';
        $types['percentage'] = 'Percentage on Total Cart';
        return view('pages.promo_codes.create',compact('types'))
            ->with('content_header', 'Add Restaurant');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EventRequest $request)
    {
        $start_datetime = null;
        if(!empty($request->start_datetime)){
            $start_datetime = strtotime($request->start_datetime);
        }
        $end_datetime = null;
        if(!empty($request->end_datetime)){
            $end_datetime = strtotime($request->end_datetime);
        }
        Coupon::create([
            'start_date' => $start_datetime,
            'end_date' => $end_datetime,
            'code' => empty($request->code) ? null : trim($request->code),
            'max_uses' => empty($request->max_uses) ? null : trim($request->max_uses),
            'type' => empty($request->type) ? null : trim($request->type),
            'amount' => empty($request->amount) ? null : trim($request->amount)
        ]);

        return redirect()->route('admin.promo_codes.index')
            ->with('success', 'Successfully create a new Restaurant');
    }

    public function publish($id){
        $item = Coupon::find($id);
        if($item){
            $item->is_published = !$item->is_published;
            $item->save();
        }
        return redirect()->route('admin.promo_codes.index')
            ->with('success', 'Restaurant Status Successfully updated');
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Coupon::findOrFail($id);
        $types = [];
        $types['fixed'] = 'Fixed Value on Total Cart';
        $types['percentage'] = 'Percentage on Total Cart';
        return view('pages.promo_codes.edit', compact('item','types'))
            ->with('content_header', 'Update Restaurant');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EventRequest $request, $id)
    {
        $item = Coupon::findOrFail($id);
        $start_datetime = null;
        if(!empty($request->start_datetime)){
            $start_datetime = strtotime($request->start_datetime);
        }
        $end_datetime = null;
        if(!empty($request->end_datetime)){
            $end_datetime = strtotime($request->end_datetime);
        }
        $item->update([
            'start_date' => $start_datetime,
            'end_date' => $end_datetime,
            'code' => empty($request->code) ? null : trim($request->code),
            'max_uses' => empty($request->max_uses) ? null : trim($request->max_uses),
            'type' => empty($request->type) ? null : trim($request->type),
            'amount' => empty($request->amount) ? null : trim($request->amount)
        ]);
        return redirect()->route('admin.promo_codes.index')
            ->with('success', 'Successfully updated the Restaurant');
    }

    public function destroy($id)
    {
        $item = Coupon::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.promo_codes.index')
            ->with('success', 'Successfully deleted an Restaurant');
    }
}

