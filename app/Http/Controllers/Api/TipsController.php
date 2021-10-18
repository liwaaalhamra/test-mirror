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
use App\Models\Api\Garage;
use App\Models\Api\Latest;
use App\Models\Api\News;
use App\Models\Api\Obituary;
use App\Models\Api\PaymentRequest;
use App\Models\Api\PaymentResponse;
use App\Models\Api\Player;
use App\Models\Api\PublicHoliday;
use App\Models\Api\RecordTypeMapping;
use App\Models\Api\RequestsMessages;
use App\Models\Api\RoadAssistance;
use App\Models\Api\SalesRequests;
use App\Models\Api\Terms;
use App\Models\Api\TipsCategory;
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

class TipsController extends Controller
{
	/**
	 * @param Request $request
	 * @return \stdClass
	 */
	public function index(Request $request) {
        if($access_token = parent::valid_access_token($request)) {
            if($user = parent::get_user_by_access_token($access_token)) {
                $tips = TipsCategory::orderBy('item_order','asc')->get();
                return response()->json($tips);
            }else{
                return parent::return_error("Invalid Access Token", 101, 'messages.invalid_access_token');
            }
        }else{
            return parent::return_error("Missing Access Token", 102, 'messages.authentication_missing');
        }
	}
}
