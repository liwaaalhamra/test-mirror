<?php

namespace App\Http\Controllers\Admin;

use App\Models\Api\MainSection;
use App\Models\Api\Media;
use App\Models\Api\Terms;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

/**
 * @author Hassan Itani <hassan.itani@tedmob.com>
 * Class MainPageController
 */
class MainPageController extends Controller
{
    public function __construct()
    {
        parent::__construct();
//        if(auth()->user() == null || (!in_array('all',$this->sections))){
//            abort(401);
//        }
    }
    /**
     * Show Data.
     *
     * @return View
     */
    public function index() /*: View*/
    {
        $stores = MainSection::whereName('stores_shops')->first();
        $restaurants = MainSection::whereName('restaurants_cafes')->first();
        $entertainment = MainSection::whereName('entertainment')->first();
        $news = MainSection::whereName('news_events')->first();
        $offers = MainSection::whereName('offers_promotions')->first();
        $cinema = MainSection::whereName('cinema')->first();
        $directory = MainSection::whereName('directory')->first();
        $gift_card = MainSection::whereName('gift_card')->first();

        return view('pages.main_page.index',compact('stores','restaurants','entertainment','news','offers','cinema','directory','gift_card'))
            ->with('section_name', 'main_page')
            ->with('content_header', 'Home Page Images');
    }

    /**
     * Delete Image.
     *
     * @return View
     */
    public function delete_single_image($category_type)
    {
        $item = MainSection::whereName($category_type)->first();
        if($item->getMediaName() != null) {
            $filePath = public_path() . DIRECTORY_SEPARATOR . 'images'  . DIRECTORY_SEPARATOR . 'main_sections' . DIRECTORY_SEPARATOR . $item->getMediaName();
            File::delete($filePath);
        }
        $item->image = null;
        $item->save();

        return redirect()->back()->with('success', 'Successfully deleted the image');
    }

    /**
     * Update Data.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function set(Request $request)
    {
        $stores = MainSection::whereName('stores_shops')->first();
        $restaurants = MainSection::whereName('restaurants_cafes')->first();
        $entertainment = MainSection::whereName('entertainment')->first();
        $news = MainSection::whereName('news_events')->first();
        $offers = MainSection::whereName('offers_promotions')->first();
        $cinema = MainSection::whereName('cinema')->first();
        $directory = MainSection::whereName('directory')->first();
        $gift_card = MainSection::whereName('gift_card')->first();
        $destinationDir = public_path() . DIRECTORY_SEPARATOR . 'images'  . DIRECTORY_SEPARATOR .'main_sections' ;
        $stores_file_name = null;
        $restaurants_file_name = null;
        $entertainment_file_name = null;
        $news_file_name = null;
        $offers_file_name = null;
        $cinema_file_name = null;
        $directory_file_name = null;
        $gift_card_file_name = null;

        if($request->hasFile('stores_shops')) {
            if($request->file('stores_shops')->isValid()) {
                $file = $request->file('stores_shops');
                $stores_file_name = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $stores_file_name);
                if($stores) {
                    if($stores->getMediaName() != null){
                        $filePath = public_path() . DIRECTORY_SEPARATOR . 'images'  . DIRECTORY_SEPARATOR . 'main_sections' . DIRECTORY_SEPARATOR . $stores->getMediaName();
                        File::delete($filePath);
                    }
                    $stores->image = $stores_file_name;
                    $stores->save();
                }
            }
        }
        if($stores) {
            $stores->title = empty($request->stores_title) ? null : trim($request->stores_title);
            $stores->save();
        }

        if($request->hasFile('restaurants_cafes')) {
            if($request->file('restaurants_cafes')->isValid()) {
                $file = $request->file('restaurants_cafes');
                $restaurants_file_name = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $restaurants_file_name);
                if($restaurants) {
                    if($restaurants->getMediaName() != null){
                        $filePath = public_path() . DIRECTORY_SEPARATOR . 'images'  . DIRECTORY_SEPARATOR . 'main_sections' . DIRECTORY_SEPARATOR . $restaurants->getMediaName();
                        File::delete($filePath);
                    }
                    $restaurants->image = $restaurants_file_name;
                    $restaurants->save();
                }
            }
        }
        if($restaurants) {
            $restaurants->title = empty($request->restaurants_title) ? null : trim($request->restaurants_title);
            $restaurants->save();
        }

        if($request->hasFile('entertainment')) {
            if($request->file('entertainment')->isValid()) {
                $file = $request->file('entertainment');
                $entertainment_file_name = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $entertainment_file_name);
                if($entertainment) {
                    if($entertainment->getMediaName() != null){
                        $filePath = public_path() . DIRECTORY_SEPARATOR . 'images'  . DIRECTORY_SEPARATOR . 'main_sections' . DIRECTORY_SEPARATOR . $entertainment->getMediaName();
                        File::delete($filePath);
                    }
                    $entertainment->image = $entertainment_file_name;
                    $entertainment->save();
                }
            }
        }
        if($entertainment) {
            $entertainment->title = empty($request->entertainment_title) ? null : trim($request->entertainment_title);
            $entertainment->save();
        }

        if($request->hasFile('news_events')) {
            if($request->file('news_events')->isValid()) {
                $file = $request->file('news_events');
                $news_file_name = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $news_file_name);
                if($news) {
                    if($news->getMediaName() != null){
                        $filePath = public_path() . DIRECTORY_SEPARATOR . 'images'  . DIRECTORY_SEPARATOR . 'main_sections' . DIRECTORY_SEPARATOR . $news->getMediaName();
                        File::delete($filePath);
                    }
                    $news->image = $news_file_name;
                    $news->save();
                }
            }
        }
        if($news) {
            $news->title = empty($request->news_title) ? null : trim($request->news_title);
            $news->save();
        }

        if($request->hasFile('offers_promotions')) {
            if($request->file('offers_promotions')->isValid()) {
                $file = $request->file('offers_promotions');
                $offers_file_name = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $offers_file_name);
                if($offers) {
                    if($offers->getMediaName() != null){
                        $filePath = public_path() . DIRECTORY_SEPARATOR . 'images'  . DIRECTORY_SEPARATOR . 'main_sections' . DIRECTORY_SEPARATOR . $offers->getMediaName();
                        File::delete($filePath);
                    }
                    $offers->image = $offers_file_name;
                    $offers->save();
                }
            }
        }
        if($offers) {
            $offers->title = empty($request->offers_title) ? null : trim($request->offers_title);
            $offers->save();
        }

        if($request->hasFile('cinema')) {
            if($request->file('cinema')->isValid()) {
                $file = $request->file('cinema');
                $cinema_file_name = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $cinema_file_name);
                if($cinema) {
                    if($cinema->getMediaName() != null){
                        $filePath = public_path() . DIRECTORY_SEPARATOR . 'images'  . DIRECTORY_SEPARATOR . 'main_sections' . DIRECTORY_SEPARATOR . $cinema->getMediaName();
                        File::delete($filePath);
                    }
                    $cinema->image = $cinema_file_name;
                    $cinema->save();
                }
            }
        }
        if($cinema) {
            $cinema->title = empty($request->cinema_title) ? null : trim($request->cinema_title);
            $cinema->save();
        }

        if($request->hasFile('directory')) {
            if($request->file('directory')->isValid()) {
                $file = $request->file('directory');
                $directory_file_name = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $directory_file_name);
                if($directory) {
                    if($directory->getMediaName() != null){
                        $filePath = public_path() . DIRECTORY_SEPARATOR . 'images'  . DIRECTORY_SEPARATOR . 'main_sections' . DIRECTORY_SEPARATOR . $directory->getMediaName();
                        File::delete($filePath);
                    }
                    $directory->image = $directory_file_name;
                    $directory->save();
                }
            }
        }
        if($directory) {
            $directory->title = empty($request->directory_title) ? null : trim($request->directory_title);
            $directory->save();
        }

        if($request->hasFile('gift_card')) {
            if($request->file('gift_card')->isValid()) {
                $file = $request->file('gift_card');
                $gift_card_file_name = str_slug(str_random(20)) . '.' . $file->getClientOriginalExtension();
                $file->move($destinationDir, $gift_card_file_name);
                if($gift_card) {
                    if($gift_card->getMediaName() != null){
                        $filePath = public_path() . DIRECTORY_SEPARATOR . 'images'  . DIRECTORY_SEPARATOR . 'main_sections' . DIRECTORY_SEPARATOR . $gift_card->getMediaName();
                        File::delete($filePath);
                    }
                    $gift_card->image = $gift_card_file_name;
                    $gift_card->save();
                }
            }
        }
        if($gift_card) {
            $gift_card->title = empty($request->gift_card_title) ? null : trim($request->gift_card_title);
            $gift_card->save();
        }

        return redirect()->back()->with('success', 'Successfully updated the sections');
    }
}
