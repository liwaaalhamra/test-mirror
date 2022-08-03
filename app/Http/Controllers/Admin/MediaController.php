<?php

namespace App\Http\Controllers\Admin;

use App\Models\Api\AboutUs;
use App\Models\Api\Category;
use App\Models\Api\News;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use Validator;

/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class MediaController
 */
class MediaController extends Controller
{
    /**
     * AboutUsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function view_media($class,$item_id,$media_type = 'image') /*: View*/
    {

        $item = null;
        $path = null;
        switch ($class) {
            case "about_us":
                $item = AboutUs::find($item_id);
                break;
            case "news":
                $item = News::find($item_id);
                break;
            case "categories":
                $item = Category::find($item_id);
                if($media_type == 'selected_image'){
                    $path = public_path('uploads/images/categories/'.$item->getSelectedMediaName());
                }else{
                    $path = public_path('uploads/images/categories/'.$item->getMediaName());
                }

                break;
            default:
                $item = null;
        }
        if($item && auth()->user()){
            return response()->file($path);
        }else{
            auth()->logout();

            // ask to authenticate again.
            return redirect('/login')->with(['unauthorized' => "You are not authorized to access the site!"]);
        }
    }

}
