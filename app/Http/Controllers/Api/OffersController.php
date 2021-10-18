<?php

namespace App\Http\Controllers\Api;

use App\Helpers\JsonBuilder;
use App\Helpers\NetworkTravelHelper;
use App\Helpers\PaymentHelper;
use App\Models\Api\Ad;
use App\Models\Api\Banner;
use App\Models\Api\emailMedia;
use App\Models\Api\Game;
use App\Models\Api\Gift;
use App\Models\Api\Latest;
use App\Models\Api\News;
use App\Models\Api\Offer;
use App\Models\Api\PaymentRequest;
use App\Models\Api\PaymentResponse;
use App\Models\Api\Player;
use App\Models\Api\RecordTypeMapping;
use App\Models\Api\RequestsMessages;
use App\Models\Api\SalesRequests;
use App\Models\Api\Terms;
use App\Models\Api\UserNews;
use App\Models\Api\Users;
use App\Traits\HITrait;
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

class OffersController extends Controller
{
    use HITrait;
	/**
	 * Get All Items
	 * @param Request $request
	 * @return \stdClass
	 */
	public function items(Request $request) {
        if($request->header('token')) {
            $user = $this->validate_user($request);
            $targets = ['private','both'];
        }else{
            $targets = ['public','both'];
            $user = null;
        }
        $query = Offer::whereIn('target', $targets)->whereIsPublished(1)->orderBy('item_order','asc');
        if(Input::has('search')){
            $search = Input::get('search');
            $query->where(function($query) use ($search){
                $query->whereRaw("LOWER(title_en) like ? ", [ "%".strtolower($search)."%"])
                ->orwhereRaw("LOWER(title_ar) like ? ", [ "%".strtolower($search)."%"])
                ->orwhereRaw("LOWER(subtitle_en) like ? ", [ "%".strtolower($search)."%"])
                ->orwhereRaw("LOWER(subtitle_ar) like ? ", [ "%".strtolower($search)."%"]);
            });
        }
        $items = $query->get();
        return response()->json($items);
	}


    /**
     * Share News
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function share_news(Request $request) {
        $user = $this->validate_user($request);
        if(!Input::has('item_id')){
            return parent::return_error('Missing Restaurant ID',401,'messages.missing_parameter');
        }
        if(!$item = News::whereIsPublished(1)->whereId($request->item_id)->first()){
            return parent::return_error("Invalid Restaurant", 402, 'messages.invalid_parameters');
        }

        $share = UserNews::whereNewsId($item->id)->whereUserId($user->id)->first();
        if($share){
            return parent::return_error("News Already Shared", 459, 'messages.news_already_shared');
        }
        UserNews::create([
            'user_id' => $user->id,
            'news_id' => $item->id,
        ]);

        $user->points()->create([
            'meta_value' => $item->id,
            'meta_name' => 'news',
            'points' => 5,
            'description' => 'successfully news shared: '.$item->caption
        ]);

        $total = 0;
        foreach ($user->points as $row) {
            $total += $row->points;
        }
        $user->total_points = $total;
        $user->save();
        return response()->json(['status' => 'News Shared successfully']);
    }

    /**
     * Get Single Restaurant
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function single_item(Request $request) {
        if(!Input::has('item_id')){
            return parent::return_error('Missing Restaurant ID',401,'messages.missing_parameter');
        }
        if(!$item = Offer::whereIsPublished(1)->whereId($request->item_id)->first()){
            return parent::return_error("Invalid Restaurant", 402, 'messages.invalid_parameters');
        }
        if($request->header('token')) {
            $user = $this->validate_user($request);
        }
        return response()->json($item);
    }
}
