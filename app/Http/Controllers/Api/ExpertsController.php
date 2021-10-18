<?php

namespace App\Http\Controllers\Api;

use App\Helpers\JsonBuilder;
use App\Helpers\NetworkTravelHelper;
use App\Helpers\PaymentHelper;
use App\Models\Api\Ad;
use App\Models\Api\Area;
use App\Models\Api\Banner;
use App\Models\Api\emailMedia;
use App\Models\Api\Expert;
use App\Models\Api\FAQ;
use App\Models\Api\Game;
use App\Models\Api\Latest;
use App\Models\Api\News;
use App\Models\Api\Obituary;
use App\Models\Api\PaymentRequest;
use App\Models\Api\PaymentResponse;
use App\Models\Api\Player;
use App\Models\Api\PublicHoliday;
use App\Models\Api\RecordTypeMapping;
use App\Models\Api\Region;
use App\Models\Api\RequestsMessages;
use App\Models\Api\RoadAssistance;
use App\Models\Api\SalesRequests;
use App\Models\Api\Terms;
use App\Models\Api\Users;
use Illuminate\Foundation\Testing\Constraints\PageConstraint;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\NetworkHelper;
use App\Helpers\ResponseHelper;
use Validator;

class ExpertsController extends Controller
{

    /**
     * Get Regions
     * @param Request $request
     * @return \stdClass
     */
    public function regions(Request $request) {
        if(!Input::has('country_id')){
            return parent::return_error("Missing Country ID", 401, 'messages.missing_parameter');
        }
        $regions = Region::whereCountryId($request->country_id)->get();
        return response()->json($regions);
    }
	/**
	 * @param Request $request
	 * @return \stdClass
	 */
	public function index(Request $request) {
//        if($access_token = parent::valid_access_token($request)) {
//            if($user = parent::get_user_by_access_token($access_token)) {
                if(Input::has('search')) {
                    $search = Input::get('search');
                    /**
                     * i will leave both cases, search by area or search by expert name, cannot be both at same time
                     */
//                    $areas_ids = Area::pluck('id')->toArray();
//                    $areas = Area::whereIn('id', $areas_ids)->whereRaw("LOWER(name) like ? ", [ "%".strtolower($search)."%"])
//                        ->orderByRaw("LOWER(name) asc")->get()->map(function ($item, $index) {
//                            $item->experts = Expert::whereAreaId($item->id)
//                                ->orderByRaw("LOWER(name) asc")->get();
//                        return $item;
//                    });
                    $areas_ids = Expert::whereRaw("LOWER(name) like ? ", [ "%".strtolower($search)."%"])->pluck('area_id')->toArray();
                    $areas = Area::whereIn('id', $areas_ids)
                        ->orderByRaw("LOWER(name) asc")->get()->filter(function ($item, $key) use ($search) {
                            $experts = Expert::whereAreaId($item->id)
                                ->whereRaw("LOWER(name) like ? ", [ "%".strtolower($search)."%"])
                                ->orderByRaw("LOWER(name) asc")->get();
                            $item->experts = $experts;
                                return count($experts) > 0;
                            })->values();
                }else{
                    if(Input::has('region_id')) {
                        $areas_ids = Area::whereRegionId($request->region_id)->pluck('id')->toArray();
                    }else{
                        $areas_ids = Area::pluck('id')->toArray();
                    }
                    $areas = Area::whereIn('id', $areas_ids)->orderByRaw("LOWER(name) asc")->get()
                        ->filter(function ($item, $key) {
                            $experts = Expert::whereAreaId($item->id)
                                ->orderByRaw("LOWER(name) asc")->get();
                            $item->experts = $experts;
                            return count($experts) > 0;
                        })->values();
                }
                return response()->json($areas);
//            }else{
//                return parent::return_error("Invalid Access Token", 101, 'messages.invalid_access_token');
//            }
//        }else{
//            return parent::return_error("Missing Access Token", 102, 'messages.authentication_missing');
//        }
	}
}
