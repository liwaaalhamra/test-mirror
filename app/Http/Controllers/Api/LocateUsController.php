<?php

namespace App\Http\Controllers\Api;

use App\Helpers\JsonBuilder;
use App\Helpers\NetworkTravelHelper;
use App\Helpers\PaymentHelper;
use App\Models\Api\Ad;
use App\Models\Api\Banner;
use App\Models\Api\emailMedia;
use App\Models\Api\Game;
use App\Models\Api\Latest;
use App\Models\Api\LocateUs;
use App\Models\Api\PaymentRequest;
use App\Models\Api\PaymentResponse;
use App\Models\Api\Player;
use App\Models\Api\RecordTypeMapping;
use App\Models\Api\RequestsMessages;
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

class LocateUsController extends Controller
{
    /**
     * Get Locations
     * @param Request $request
     * @return \stdClass
     */
    public function index(Request $request) {
        if($request->latitude && $request->longitude){
            $latitude = Input::get('latitude');
            $longitude = Input::get('longitude');
            $locations = LocateUs::selectRaw("*,(6371 * 2 * ASIN(SQRT( POWER(SIN(( $latitude - latitude) *  pi()/180 / 2), 2) +COS( $latitude * pi()/180) * COS(latitude * pi()/180) * POWER(SIN(( $longitude - longitude) * pi()/180 / 2), 2) ))) as distance")->orderbyRaw("(6371 * 2 * ASIN(SQRT( POWER(SIN(( $latitude - latitude) *  pi()/180 / 2), 2) +COS( $latitude * pi()/180) * COS(latitude * pi()/180) * POWER(SIN(( $longitude - longitude) * pi()/180 / 2), 2) ))) asc")
                ->get();
        }else{
            $locations = LocateUs::selectRaw("*,0 as distance")->orderBy('title','asc')
                ->get();
        }

        foreach($locations as $item){
            $item->image = $item->image == null ? null : secure_url('uploads/images/locate_us/'.$item->image);
        }
        return response()->json($locations);
    }
}
