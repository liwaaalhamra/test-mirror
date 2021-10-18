<?php

namespace App\Http\Controllers\Api;

use App\Helpers\JsonBuilder;
use App\Helpers\NetworkTravelHelper;
use App\Helpers\PaymentHelper;
use App\Models\Api\AboutUs;
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

class AboutUsController extends Controller
{
	/**
	 * Get About Us
	 * @param Request $request
	 * @return \stdClass
	 */
	public function index(Request $request) {

	    $about_us = AboutUs::first();
	    $about_us->image = $about_us->image == null ? null : secure_url('public/uploads/images/about_us/'.$about_us->image);
        return response()->json($about_us);
	}

    public function feedback(){
        return view('feedback.index',compact('about_us'))
            ->with('section_name', 'feedback')
            ->with('content_header', 'Feedback');
    }
}
