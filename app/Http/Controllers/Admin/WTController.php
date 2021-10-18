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
use App\Models\Api\IntroSection;
use App\Models\Api\Job;
use App\Models\Api\LocateUs;
use App\Models\Api\Media;
use App\Models\Api\OfferPromotion;
use App\Models\Api\Player;
use App\Models\Api\Service;
use App\Models\Api\Songs;
use App\Models\Api\TrainingWorkshop;
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
 * Class WTController
 */
class WTController extends Controller
{
    public function __construct()
    {
        parent::__construct();
//        if(auth()->user() == null || !in_array('all',$this->sections)){
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
        $items = TrainingWorkshop::orderBy('wt_datetime','asc')->get();
        $intro = IntroSection::first();
        return view('pages.workshops_training.index', compact('items','intro'))
            ->with('section_name', 'workshops_training')
            ->with('content_header', 'Training & Workshop');
    }

    public function manage_public($id){
        $item = TrainingWorkshop::find($id);
        if($item){
            $item->target = 'public';
            $item->save();
        }
        return redirect()->route('admin.workshops_training.index')
            ->with('success', 'Restaurant Status Successfully updated');
    }
    public function manage_private($id){
        $item = TrainingWorkshop::find($id);
        if($item){
            $item->target = 'private';
            $item->save();
        }
        return redirect()->route('admin.workshops_training.index')
            ->with('success', 'Restaurant Status Successfully updated');
    }

    public function publish($id){
        $item = TrainingWorkshop::find($id);
        if($item){
            $item->is_published = !$item->is_published;
            $item->save();
        }
        return redirect()->route('admin.workshops_training.index')
            ->with('success', 'Restaurant Status Successfully updated');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.workshops_training.create')
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
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . 'training_workshops';
        $fileName = null;
        // check if a file exist and is valid.
        if($request->hasFile('image')) {
            if($request->file('image')->isValid()) {
                $file = $request->file('image');
                $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $fileName);
            }
        }
        $item_datetime = null;
        if(!empty($request->item_datetime)){
            $item_datetime = strtotime($request->item_datetime);
        }
        TrainingWorkshop::create([
            'type' => $request->type,
            'image' => $fileName,
            'wt_datetime' => $item_datetime,
            'caption' => empty($request->caption) ? null : trim($request->caption),
            'link' => empty($request->link) ? null : trim($request->link),
            'description' => empty($request->description) ? null : trim($request->description)
        ]);

        return redirect()->route('admin.workshops_training.index')
            ->with('success', 'Successfully create a new Restaurant');
    }

    public function wt_introduction(Request $request)
    {
        $intro = IntroSection::first();
        $intro->workshop_title = empty($request->workshop_title) ? null : trim($request->workshop_title);
        $intro->workshop_description = empty($request->workshop_description) ? null : trim($request->workshop_description);
        $intro->training_title = empty($request->training_title) ? null : trim($request->training_title);
        $intro->training_description = empty($request->training_description) ? null : trim($request->training_description);
        $intro->save();

        return redirect()->route('admin.workshops_training.index')
            ->with('success', 'Successfully updated the introduction');
    }

    public function delete_single_image($id)
    {
        $item = TrainingWorkshop::findOrFail($id);
        if($item->image != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'training_workshops' . DIRECTORY_SEPARATOR . $item->getImageName();
            File::delete($filePath);
        }
        $item->image = null;
        $item->save();

        return redirect()->back()->with('success', 'Successfully deleted the image');
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
        $item = TrainingWorkshop::findOrFail($id);

        return view('pages.workshops_training.edit', compact('item'))
            ->with('content_header', 'Update Restaurant');
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
        $item = TrainingWorkshop::findOrFail($id);
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'training_workshops';
        $fileName = null;
        if($item->getImageName() != null) {
            $fileName = $item->getImageName();
        }
        // check if a file exist and is valid.
        if($request->hasFile('image')) {
            if($request->file('image')->isValid()) {
                $file = $request->file('image');
                $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $fileName);
                if($item->getImageName() != null) {
                    $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'training_workshops' . DIRECTORY_SEPARATOR . $item->getImageName();
                    File::delete($filePath);
                }
            }
        }
        $item_datetime = null;
        if(!empty($request->item_datetime)){
            $item_datetime = strtotime($request->item_datetime);
        }
        $item->update([
            'type' => $request->type,
            'image' => $fileName,
            'wt_datetime' => $item_datetime,
            'caption' => empty($request->caption) ? null : trim($request->caption),
            'link' => empty($request->link) ? null : trim($request->link),
            'description' => empty($request->description) ? null : trim($request->description)
        ]);
        return redirect()->route('admin.workshops_training.index')
            ->with('success', 'Successfully updated the Job');
    }

    public function destroy($id)
    {
        $item = TrainingWorkshop::findOrFail($id);
        if($item->getImageName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'training_workshops' . DIRECTORY_SEPARATOR . $item->getImageName();
            File::delete($filePath);
        }
        $item->delete();

        return redirect()->route('admin.workshops_training.index')
            ->with('success', 'Successfully deleted the Restaurant');
    }
}

