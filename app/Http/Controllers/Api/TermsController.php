<?php

namespace App\Http\Controllers\Api;

use App;
use App\Helpers\JsonBuilder;
use App\Helpers\NetworkTravelHelper;
use App\Helpers\PaymentHelper;
use App\Models\Api\Ad;
use App\Models\Api\Banner;
use App\Models\Api\emailMedia;
use App\Models\Api\Game;
use App\Models\Api\Latest;
use App\Models\Api\PaymentRequest;
use App\Models\Api\PaymentResponse;
use App\Models\Api\Player;
use App\Models\Api\RecordTypeMapping;
use App\Models\Api\RequestsMessages;
use App\Models\Api\SalesRequests;
use App\Models\Api\Terms;
use App\Models\Api\Users;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\Constraints\PageConstraint;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\NetworkHelper;
use App\Helpers\ResponseHelper;
use SoapFault;
use Validator;

class TermsController extends Controller
{
	/**
	 * Get Terms & Conditions
	 * @param Request $request
	 * @return \stdClass
	 */
	public function index($lang = 'en') {
        return Terms::whereType('terms')->first()->{'description_'.$lang};
	}

    public function privacy($lang = 'en') {
        return Terms::whereType('privacy')->first()->{'description_'.$lang};
    }

    public function restaurant_terms($lang = 'en') {
        return Terms::whereType('restaurant_terms')->first()->{'description_'.$lang};
    }

    public function restaurant_privacy($lang = 'en') {
        return Terms::whereType('restaurant_privacy')->first()->{'description_'.$lang};
    }

    public function supplier_terms($lang = 'en') {
        return Terms::whereType('supplier_terms')->first()->{'description_'.$lang};
    }

    public function supplier_privacy($lang = 'en') {
        return Terms::whereType('supplier_privacy')->first()->{'description_'.$lang};
    }

    public function website_terms(){
        return Terms::first()->description;
    }
}
