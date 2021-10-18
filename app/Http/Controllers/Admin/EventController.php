<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BannerRequest;
use App\Http\Requests\EventRequest;
use App\Models\Api\Ad;
use App\Models\Api\Banner;
use App\Models\Api\BusinessNews;
use App\Models\Api\Category;
use App\Models\Api\CorporateActivity;
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
 * Class EventController
 */
class EventController extends Controller
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
        $items = Event::orderBy('event_date_from','asc')->get();
        $accessible_sections = $this->sections;
        return view('pages.events.index', compact('items','accessible_sections'))
            ->with('section_name', 'events')
            ->with('content_header', 'Events');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.events.create')
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
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . 'events';
        $fileName = null;
        // check if a file exist and is valid.
        if($request->hasFile('image')) {
            if($request->file('image')->isValid()) {
                $file = $request->file('image');
                $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $fileName);
            }
        }
        $start_datetime = null;
        if(!empty($request->start_datetime)){
            $start_datetime = strtotime($request->start_datetime);
        }
        $end_datetime = null;
        if(!empty($request->end_datetime)){
            $end_datetime = strtotime($request->end_datetime);
        }
        Event::create([
            'admin_id' => auth()->user()->id,
            'event_date_from' => $start_datetime,
            'event_date_to' => $end_datetime,
            'media' => $fileName == null ? 'https://www.youtube.com/watch?v='.trim($request->youtube_id) : $fileName,
            'thumbnail' => $fileName == null ? 'http://img.youtube.com/vi/'.trim($request->youtube_id).'/0.jpg' : null,
            'media_type' => $fileName == null ? 'video' : 'image',
            'youtube_id' => empty($request->youtube_id) ? null : trim($request->youtube_id),
            'caption' => empty($request->caption) ? null : trim($request->caption),
            'link' => empty($request->link) ? null : trim($request->link),
            'description' => empty($request->description) ? null : trim($request->description),
            'latitude' => empty($request->latitude) ? null : trim($request->latitude),
            'longitude' => empty($request->longitude) ? null : trim($request->longitude)
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Successfully create a new Restaurant');
    }

    public function delete_single_image($id)
    {
        $item = Event::findOrFail($id);
        if($item->media_type == 'image' && $item->getMediaName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'events' . DIRECTORY_SEPARATOR . $item->getMediaName();
            File::delete($filePath);
        }
        $item->media = null;
        $item->media_type = null;
        $item->save();

        return redirect()->back()->with('success', 'Successfully deleted the image');
    }

    public function publish($id){
        $item = Event::find($id);
        if($item){
            $item->is_published = !$item->is_published;
            $item->save();
        }
        return redirect()->route('admin.events.index')
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
        $item = Event::findOrFail($id);

        return view('pages.events.edit', compact('item'))
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
        $item = Event::findOrFail($id);

        // public/files/{random_file_name}
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'events';
        $fileName = null;
        if($item->media_type == 'image' && $item->getMediaName() != null) {
            $fileName = $item->getMediaName();
        }
        // check if a file exist and is valid.
        if($request->hasFile('image')) {
            if($request->file('image')->isValid()) {
                $file = $request->file('image');
                $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $fileName);
                if($item->media_type == 'image' && $item->getMediaName() != null) {
                    $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'events' . DIRECTORY_SEPARATOR . $item->getMediaName();
                    File::delete($filePath);
                }
            }
        }
        if(!empty($request->youtube_id)){
            if($item->media_type == 'image' && $item->getMediaName() != null) {
                $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'events' . DIRECTORY_SEPARATOR . $item->getMediaName();
                File::delete($filePath);
            }
        }
        $start_datetime = null;
        if(!empty($request->start_datetime)){
            $start_datetime = strtotime($request->start_datetime);
        }
        $end_datetime = null;
        if(!empty($request->end_datetime)){
            $end_datetime = strtotime($request->end_datetime);
        }
        $item->update([
            'event_date_from' => $start_datetime,
            'event_date_to' => $end_datetime,
            'media' => $fileName == null ? 'https://www.youtube.com/watch?v='.trim($request->youtube_id) : $fileName,
            'thumbnail' => $fileName == null ? 'http://img.youtube.com/vi/'.trim($request->youtube_id).'/0.jpg' : null,
            'media_type' => $fileName == null ? 'video' : 'image',
            'youtube_id' => empty($request->youtube_id) ? null : trim($request->youtube_id),
            'caption' => empty($request->caption) ? null : trim($request->caption),
            'link' => empty($request->link) ? null : trim($request->link),
            'description' => empty($request->description) ? null : trim($request->description),
            'latitude' => empty($request->latitude) ? null : trim($request->latitude),
            'longitude' => empty($request->longitude) ? null : trim($request->longitude)
        ]);
        return redirect()->route('admin.events.index')
            ->with('success', 'Successfully updated the Restaurant');
    }

    public function destroy($id)
    {
        $item = Event::findOrFail($id);
        if($item->media_type == 'image' && $item->getMediaName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'events' . DIRECTORY_SEPARATOR . $item->getMediaName();
            File::delete($filePath);
        }
        $item->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Successfully deleted an Restaurant');
    }
}

