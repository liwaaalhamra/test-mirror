<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BannerRequest;
use App\Models\Api\Ad;
use App\Models\Api\Banner;
use App\Models\Api\BusinessNews;
use App\Models\Api\Category;
use App\Models\Api\CorporateActivity;
use App\Models\Api\Game;
use App\Models\Api\Gift;
use App\Models\Api\Job;
use App\Models\Api\LocateUs;
use App\Models\Api\Media;
use App\Models\Api\OfferPromotion;
use App\Models\Api\Player;
use App\Models\Api\Service;
use App\Models\Api\Songs;
use App\Models\Api\UserPoints;
use App\Models\Api\Users;
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
 * Class PointsController
 */
class PointsController extends Controller
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
        $items = UserPoints::whereMetaValue(auth()->user()->id)->whereMetaName('system')->orderBy('item_datetime','desc')->get();
        $accessible_sections = $this->sections;
        return view('pages.points.index', compact('items','accessible_sections'))
            ->with('section_name', 'points')
            ->with('content_header', 'Points');
    }

    public function publish($id){
        $item = UserPoints::find($id);
        if($item){
            $item->is_published = !$item->is_published;
            $item->save();
        }
        return redirect()->route('admin.points.index')
            ->with('success', 'Restaurant Status Successfully updated');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $min = 0;
        $max = 100;
        $types = Users::select('cost_center')->distinct()->get();
        $elligible_job_titles = ['Crew','Hostess','Crew Trainer','Delivery','MDS rider','Maintenance','Barista','Barista Leader','Crew exp.','Cleaning','Star'];
        if(auth()->user()->cost_center == null || empty(auth()->user()->cost_center) || strtolower(auth()->user()->cost_center) == 'all'){
            $users = Users::whereIn('job_title', $elligible_job_titles)->get();
        }else{
            $users = Users::whereIn('job_title', $elligible_job_titles)->where('cost_center','=',auth()->user()->cost_center)->get();
        }
        return view('pages.points.create',compact('min','max','types','users'))
            ->with('content_header', 'Add Restaurant');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $item_datetime = null;
        if(!empty($request->item_datetime)){
            $item_datetime = Carbon::createFromFormat('m/d/Y', $request->item_datetime)->getTimestamp();
        }
        foreach ($request->user_ids as $user_id){
            UserPoints::create([
                'item_datetime' => $item_datetime,
                'description' => empty($request->description) ? null : trim($request->description),
                'users_id' => $user_id,
                'meta_name' => 'system',
                'points' => $request->points,
                'meta_value' => auth()->user()->id,
            ]);
            $total = 0;
            $user = Users::where('id','=',$user_id)->first();
            if($user){
                foreach ($user->points as $row) {
                    $total += $row->points;
                }
                $user->total_points = $total;
                $user->save();
            }

        }

        return redirect()->route('admin.points.index')
            ->with('success', 'Points Successfully shared');
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
        $item = Job::findOrFail($id);

        return view('pages.jobs.edit', compact('item'))
            ->with('content_header', 'Update Job');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $item = Job::findOrFail($id);

        $item_datetime = null;
        if(!empty($request->item_datetime)){
            $item_datetime = strtotime($request->item_datetime);
        }
        $item->update([
            'job_datetime' => $item_datetime,
            'caption' => empty($request->caption) ? null : trim($request->caption),
            'link' => empty($request->link) ? null : trim($request->link),
            'description' => empty($request->description) ? null : trim($request->description)
        ]);
        return redirect()->route('admin.jobs.index')
            ->with('success', 'Successfully updated the Job');
    }

    public function destroy($id)
    {
        $item = UserPoints::findOrFail($id);
        $user_id = $item->users_id;
        $item->delete();

        $total = 0;
        $user = Users::where('id','=',$user_id)->first();
        if($user){
            foreach ($user->points as $row) {
                $total += $row->points;
            }
            $user->total_points = $total;
            $user->save();
        }
        return redirect()->route('admin.points.index')
            ->with('success', 'Successfully deleted the Points');
    }
}

