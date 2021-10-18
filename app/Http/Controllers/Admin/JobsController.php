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
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\View\View;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use Validator;

/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class JobsController
 */
class JobsController extends Controller
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
        $items = Job::orderBy('job_datetime','asc')->get();
        $intro = IntroSection::first();
        return view('pages.jobs.index', compact('items','intro'))
            ->with('section_name', 'jobs')
            ->with('content_header', 'Jobs');
    }

    public function publish($id){
        $item = Job::find($id);
        if($item){
            $item->is_published = !$item->is_published;
            $item->save();
        }
        return redirect()->route('admin.jobs.index')
            ->with('success', 'Restaurant Status Successfully updated');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.jobs.create')
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
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . 'jobs';
        $fileName = null;
        // check if a file exist and is valid.
        if($request->hasFile('image')) {
            if($request->file('image')->isValid()) {
                $file = $request->file('image');
                try{
                    $data = file_get_contents($file->getPathname());
                    $base64 = base64_encode($data);
                    $bin = base64_decode($base64);
                    $im = imageCreateFromString($bin);
                }catch (\Exception $ex){
                    return redirect()->back()
                        ->with('error_msg', 'Document Corrupted');
                }
                $validator = Validator::make($request->all(), [
                    'image' => 'mimetypes:image/jpeg,image/png',
                ]);
                if($validator->fails()){
                    return redirect()->back()
                        ->with('warning', 'Invalid Media Type');
                }
                $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $fileName);
            }
        }
        $item_datetime = null;
        if(!empty($request->item_datetime)){
            $item_datetime = strtotime($request->item_datetime);
        }
        Job::create([
            'type' => $request->type,
            'image' => $fileName,
            'job_datetime' => $item_datetime,
            'caption' => empty($request->caption) ? null : trim($request->caption),
            'link' => empty($request->link) ? null : trim($request->link),
            'description' => empty($request->description) ? null : trim($request->description)
        ]);

        return redirect()->route('admin.jobs.index')
            ->with('success', 'Successfully create a new Job');
    }

    public function jobs_introduction(Request $request)
    {

        $intro = IntroSection::first();
        $intro->career_title = empty($request->career_title) ? null : trim($request->career_title);
        $intro->career_description = empty($request->career_description) ? null : trim($request->career_description);
        $intro->intern_title = empty($request->intern_title) ? null : trim($request->intern_title);
        $intro->intern_description = empty($request->intern_description) ? null : trim($request->intern_description);
        $intro->save();

        return redirect()->route('admin.jobs.index')
            ->with('success', 'Successfully updated the introduction');
    }

    public function delete_single_image($id)
    {
        $item = Job::findOrFail($id);
        if($item->image != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'jobs' . DIRECTORY_SEPARATOR . $item->getImageName();
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
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'jobs';
        $fileName = null;
        if($item->getImageName() != null) {
            $fileName = $item->getImageName();
        }
        // check if a file exist and is valid.
        if($request->hasFile('image')) {
            if($request->file('image')->isValid()) {
                $file = $request->file('image');
                try{
                    $data = file_get_contents($file->getPathname());
                    $base64 = base64_encode($data);
                    $bin = base64_decode($base64);
                    $im = imageCreateFromString($bin);
                }catch (\Exception $ex){
                    return redirect()->back()
                        ->with('error_msg', 'Document Corrupted');
                }
                $validator = Validator::make($request->all(), [
                    'image' => 'mimetypes:image/jpeg,image/png',
                ]);
                if($validator->fails()){
                    return redirect()->back()
                        ->with('warning', 'Invalid Media Type');
                }
                $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $fileName);
                if($item->getImageName() != null) {
                    $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'jobs' . DIRECTORY_SEPARATOR . $item->getImageName();
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
        $item = Job::findOrFail($id);
        if($item->getImageName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'jobs' . DIRECTORY_SEPARATOR . $item->getImageName();
            File::delete($filePath);
        }
        $item->delete();

        return redirect()->route('admin.jobs.index')
            ->with('success', 'Successfully deleted the Job');
    }
}

