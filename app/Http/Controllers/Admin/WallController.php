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
use App\Models\Api\WallMedia;
use App\Models\Api\WallPost;
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
 * Class WallController
 */
class WallController extends Controller
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
        $items = WallPost::latest()->get();

        return view('pages.wall.index', compact('items'))
            ->with('section_name', 'wall')
            ->with('content_header', 'Wall');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $departments = [
            "BoB Community",
            'Bank of Beirut',
            'Chief Executive Officer (CEO)',
            'Country Manager',
            'Chief Operations Officer',
            'Audit',
            'Retail',
            'Corporate & Commercial',
            'HR',
            'Legal',
            'BNM',
            'Marketing & Communication',
            'Finance',
            'Risk',
            'Corporate Credit',
            'IT',
            'ISBC',
            'Organization',
            'Central Operations',
            'Financial Institutions',
            'Trade Finance',
            'Consumer Lending',
            'Collection',
            'Portfolio & Policy Management',
            'Treasury & Capital Markets',
            'Private Banking',
            'CSR',
            'Compliance',
            'SME',
            'Digital Media',
        ];
        return view('pages.wall.create',compact('departments'))
            ->with('content_header', 'Add Post');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images' . DIRECTORY_SEPARATOR . 'user_posts';
        $fileName = null;
        $fileName1 = null;
        // check if a file exist and is valid.
        if($request->hasFile('image')) {
            if($request->file('image')->isValid()) {
                $file = $request->file('image');
                $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $fileName);
            }
        }
        if($request->hasFile('icon')) {
            if($request->file('icon')->isValid()) {
                $file1 = $request->file('icon');
                $fileName1 = str_slug(str_random(20)) . '.' . $file1->getClientOriginalExtension();
                $file1->move($destinationDir, $fileName1);
            }
        }
        $post_media_type = 'video';
        if(isset($request->post_media_type) && $request->post_media_type == 'on'){
            $post_media_type = 'image';
        }
        $post = WallPost::create([
            'user_id' => auth()->user()->id,
            'post_source' => 'cms',
            'post_media_type' => $post_media_type,
            'platform_type' => 'cms',
            'platform_id' => null,
            'created_datetime' => strtotime('now'),
            'post_source_url' => $request->url ? trim($request->url) : null,
            'title' => $request->title ? trim($request->title) : null,
            'description' => $request->description ? trim($request->description) : null,
            'user_name' => $request->department,
            'user_image' => $fileName1,
        ]);
        if($fileName != null){
            list($width, $height, $type, $attr) = getimagesize($destinationDir.'/'.$fileName);
            WallMedia::create([
                'post_id' => $post->id,
                'type' => 'image',
                'source' => 'local',
                'url' => $fileName,
                'height' => $height,
                'width' => $width,
                'video_thumbnail' => null
            ]);
        }

        return redirect()->route('admin.wall.index')
            ->with('success', 'Successfully create a new Post');
    }

    public function delete_single_image($id,$type)
    {
        $item = WallPost::findOrFail($id);
        if($type == 'icon'){
            $media = $item->media()->where('type','=','image')->latest()->first();
            if($media->getUrlName() != null) {
                $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'user_posts' . DIRECTORY_SEPARATOR . $media->getUrlName();
                File::delete($filePath);
            }
            $media->url = null;
            $media->save();
        }else{
            if($item->getUserImageName() != null) {
                $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'user_posts' . DIRECTORY_SEPARATOR . $item->getUserImageName();
                File::delete($filePath);
            }
            $item->user_image = null;
        }

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
        $item = WallPost::findOrFail($id);
        $departments = [
            "BoB Community",
            'Bank of Beirut',
            'Chief Executive Officer (CEO)',
            'Country Manager',
            'Chief Operations Officer',
            'Audit',
            'Retail',
            'Corporate & Commercial',
            'HR',
            'Legal',
            'BNM',
            'Marketing & Communication',
            'Finance',
            'Risk',
            'Corporate Credit',
            'IT',
            'ISBC',
            'Organization',
            'Central Operations',
            'Financial Institutions',
            'Trade Finance',
            'Consumer Lending',
            'Collection',
            'Portfolio & Policy Management',
            'Treasury & Capital Markets',
            'Private Banking',
            'CSR',
            'Compliance',
            'SME',
            'Digital Media',
        ];
        return view('pages.wall.edit', compact('item','departments'))
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

        $item = WallPost::findOrFail($id);

        // public/files/{random_file_name}
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'user_posts';
        $fileName1 = null;
        if($item->getUserImageName() != null) {
            $fileName1 = $item->getUserImageName();
        }
        $fileName = null;
        $media = $item->media()->where('type','=','image')->latest()->first();
        if($media != null && $media->getUrlName() != null) {
            $fileName = $media->getUrlName();
        }
        // check if a file exist and is valid.
        if($request->hasFile('image')) {
            if($request->file('image')->isValid()) {
                $file = $request->file('image');
                $fileName = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $fileName);
                if($media != null && $media->getUrlName() != null) {
                    $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'user_posts' . DIRECTORY_SEPARATOR . $media->getUrlName();
                    File::delete($filePath);
                }
            }
        }
        if($request->hasFile('icon')) {
            if($request->file('icon')->isValid()) {
                $file1 = $request->file('icon');
                $fileName1 = str_slug(str_random(20)) . '.' . $file1->getClientOriginalExtension();
                $file1->move($destinationDir, $fileName1);
                if($item->getUserImageName() != null) {
                    $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'user_posts' . DIRECTORY_SEPARATOR . $item->getUserImageName();
                    File::delete($filePath);
                }
            }
        }
        $post_media_type = 'video';
        if(isset($request->post_media_type) && $request->post_media_type == 'on'){
            $post_media_type = 'image';
        }
        $item->update([
            'title' => $request->title ? trim($request->title) : null,
            'post_source_url' => $request->url ? trim($request->url) : null,
            'description' => $request->description ? trim($request->description) : null,
            'user_name' => $request->department,
            'post_media_type' => $post_media_type,
            'user_image' => $fileName1,
        ]);
        if($fileName != null){
            list($width, $height, $type, $attr) = getimagesize($destinationDir.'/'.$fileName);
            $wall_media = WallMedia::wherePostId($item->id)->first();
            if($wall_media){
                $wall_media->url = $fileName;
                $wall_media->height = $height;
                $wall_media->width = $width;
                $wall_media->save();
            }else{
                WallMedia::create([
                    'post_id' => $item->id,
                    'type' => 'image',
                    'source' => 'local',
                    'url' => $fileName,
                    'height' => $height,
                    'width' => $width,
                    'video_thumbnail' => null
                ]);
            }

        }
        return redirect()->route('admin.wall.index')
            ->with('success', 'Successfully updated the Restaurant');
    }

    public function destroy($id)
    {
        $item = WallPost::findOrFail($id);
        if($item->user_image != null && $item->post_source == 'cms') {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'user_posts' . DIRECTORY_SEPARATOR . $item->getUserImageName();
            File::delete($filePath);
        }
        foreach ($item->media as $media){
            if($media->url != null && $media->source == 'local') {
                $filePath = public_path() . DIRECTORY_SEPARATOR . 'uploads/images'  . DIRECTORY_SEPARATOR . 'user_posts' . DIRECTORY_SEPARATOR . $media->getUrlName();
                File::delete($filePath);
            }
            $media->delete();
        }
        $item->delete();

        return redirect()->route('admin.wall.index')
            ->with('success', 'Successfully deleted an Restaurant');
    }
}

