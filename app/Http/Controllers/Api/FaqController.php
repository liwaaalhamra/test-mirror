<?php

namespace App\Http\Controllers\Api;

use App\Helpers\JsonBuilder;
use App\Helpers\NetworkTravelHelper;
use App\Helpers\PaymentHelper;
use App\Models\Api\Ad;
use App\Models\Api\Banner;
use App\Models\Api\emailMedia;
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

class FaqController extends Controller
{
	/**
	 * Get FAQs
	 * @param Request $request
	 * @return \stdClass
	 */
	public function index(Request $request) {
        $query = FAQ::whereIsPublished(1)->orderBy('item_order','asc');
        if(Input::has('search')){
            $search = Input::get('search');
            $query->where(function($query) use ($search){
                $query->whereRaw("LOWER(question_en) like ? ", [ "%".strtolower($search)."%"]);
                $query->orwhereRaw("LOWER(question_ar) like ? ", [ "%".strtolower($search)."%"]);
                $query->orwhereRaw("LOWER(answer_en) like ? ", [ "%".strtolower($search)."%"]);
                $query->orwhereRaw("LOWER(answer_ar) like ? ", [ "%".strtolower($search)."%"]);
            });
        }
        $faqs = $query->get();
        return response()->json($faqs);
	}

    public function show($lang = 'en'){
        $faqs = FAQ::whereIsPublished(1)->orderBy('item_order','asc')->get();
        return view('faqs.index',compact('faqs','lang'));
    }

	public function index_paginated(Request $request) {
        $query = FAQ::whereIsPublished(1)->orderBy('item_order','asc');
        if(Input::has('search')){
            $search = Input::get('search');
            $query->where(function($query) use ($search){
                $query->whereRaw("LOWER(question_en) like ? ", [ "%".strtolower($search)."%"]);
                $query->orwhereRaw("LOWER(question_ar) like ? ", [ "%".strtolower($search)."%"]);
                $query->orwhereRaw("LOWER(answer_en) like ? ", [ "%".strtolower($search)."%"]);
                $query->orwhereRaw("LOWER(answer_ar) like ? ", [ "%".strtolower($search)."%"]);
            });
        }
        $faqs = $query->paginate(15);
        return response()->json($faqs);
	}
}
