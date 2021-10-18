<?php

namespace App\Http\Controllers\Api;


use App\Models\Api\Banner;
use App\Traits\HITrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class BannersController extends Controller
{
    use HITrait;
    /**
     * Get Banners
     * @param Request $request
     * @return \stdClass
     */
    public function index(Request $request) {
        if($request->header('token')) {
            $user = $this->validate_user($request);
            $targets = ['private','both'];
        }else{
            $targets = ['public','both'];
            $user = null;
        }
        $query = Banner::whereIn('target', $targets)->whereIsPublished(1)->orderBy('banner_order','asc');
        $items = $query->get();
        return response()->json($items);
    }
}
