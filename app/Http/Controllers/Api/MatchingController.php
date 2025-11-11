<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Api\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class MatchingController extends Controller
{
    /**
     * Get matching results
     * @param Request $request
     * @return \stdClass
     */

    public function matchingResult($user)
    {
        $matching_ids = [];
        $merged_array = [];
        $p1 = [];
        $p2 = [];
        $p3 = [];
        $p4 = [];
        $p5 = [];
        $p6 = [];
        $p7 = [];
        $p8 = [];
        $p9 = [];
        $p10 = [];
        $p11 = [];
        $p12 = [];
        $p13 = [];
        $p14 = [];
        $p15 = [];
        $p16 = [];
        $p17 = [];
        $p18 = [];
        $p19 = [];
        $p20 = [];
        $p21 = [];
        $p22 = [];
        $p23 = [];
        $p24 = [];
        $p25 = [];
        $p26 = [];
        $p27 = [];
        $p28 = [];
        $p29 = [];
        $p30 = [];
        $p31 = [];
        $p32 = [];
        $p33 = [];
        $p34 = [];
        $p35 = [];
        $p36 = [];
        $p37 = [];
        $p38 = [];
        $p39 = [];
        $p40 = [];
        $p41 = [];
        $p42 = [];
        $p43 = [];
        $p44 = [];
        $p45 = [];
        $p46 = [];
        $p47 = [];
        $p48 = [];
        $p49 = [];
        $p50 = [];
        $p51 = [];
        $p52 = [];
        $p53 = [];
        $p54 = [];
        $p55 = [];
        $p56 = [];
        $p57 = [];
        $p58 = [];
        $p59 = [];
        $p60 = [];
        $p61 = [];

        $matches = collect();

        $max = 999999999;

        $my_posts = Item::whereUserId($user)->whereType('offer')->get();

        foreach ($my_posts as $my_post) {
            // Labels of priorities section 9
            $label_9 = [
                ['return_items.category_id', '=', $my_post->category_id],
                ['return_items.sub_category_id', '=', $my_post->sub_category_id],
                ['return_items.sub_sub_category_id', '=', $my_post->sub_sub_category_id],
                ['return_items.location_id', '=', $my_post->location_id],
                ['return_items.price_from', '<=', $my_post->price ? $my_post->price : $max],
                ['return_items.price_to', '>=', $my_post->price ? $my_post->price : 0],
            ];
            $label_9_b = [
                ['return_items.category_id', '=', $my_post->category_id],
                ['return_items.sub_category_id', '=', $my_post->sub_category_id],
                ['return_items.sub_sub_category_id', '!=', $my_post->sub_sub_category_id],
                ['return_items.location_id', '=', $my_post->location_id],
                ['return_items.price_from', '<=', $my_post->price ? $my_post->price : $max],
                ['return_items.price_to', '>=', $my_post->price ? $my_post->price : 0],
            ];
            $label_9_c = [
                ['return_items.category_id', '=', $my_post->category_id],
                ['return_items.sub_category_id', '!=', $my_post->sub_category_id],
                ['return_items.sub_sub_category_id', '!=', $my_post->sub_sub_category_id],
                ['return_items.location_id', '=', $my_post->location_id],
                ['return_items.price_from', '<=', $my_post->price ? $my_post->price : $max],
                ['return_items.price_to', '>=', $my_post->price ? $my_post->price : 0],
            ];
            $label_9_e = [
                ['return_items.category_id', '=', $my_post->category_id],
                ['return_items.sub_category_id', '!=', $my_post->sub_category_id],
                ['return_items.sub_sub_category_id', '!=', $my_post->sub_sub_category_id],
                ['return_items.location_id', '!=', $my_post->location_id],
                ['return_items.price_from', '<=', $my_post->price ? $my_post->price : $max],
                ['return_items.price_to', '>=', $my_post->price ? $my_post->price : 0],
            ];
            $label_9_d = [
                ['return_items.category_id', '!=', $my_post->category_id],
                ['return_items.sub_category_id', '!=', $my_post->sub_category_id],
                ['return_items.sub_sub_category_id', '!=', $my_post->sub_sub_category_id],
                ['return_items.location_id', '=', $my_post->location_id],
                ['return_items.price_from', '<=', $my_post->price ? $my_post->price : $max],
                ['return_items.price_to', '>=', $my_post->price ? $my_post->price : 0],
            ];
            $label_9_f = [
                ['return_items.category_id', '!=', $my_post->category_id],
                ['return_items.sub_category_id', '!=', $my_post->sub_category_id],
                ['return_items.sub_sub_category_id', '!=', $my_post->sub_sub_category_id],
                ['return_items.location_id', '!=', $my_post->location_id],
                ['return_items.price_from', '<=', $my_post->price ? $my_post->price : $max],
                ['return_items.price_to', '>=', $my_post->price ? $my_post->price : 0],
            ];

            // Labels of priorities section 10
            $label_10 = [
                ['items.category_id', '=', $my_post->return_post['category_id']],
                ['items.sub_category_id', '=', $my_post->return_post['sub_category_id']],
                ['items.sub_sub_category_id', '=', $my_post->return_post['sub_sub_category_id']],
                ['items.location_id', '=', $my_post->return_post['location_id']],
                ['items.price', '>=', $my_post->return_post['price_from'] ? $my_post->return_post['price_from'] : 0],
                ['items.price', '<=', $my_post->return_post['price_to'] ? $my_post->return_post['price_to'] : $max],
            ];
            $label_10_b = [
                ['items.category_id', '=', $my_post->return_post['category_id']],
                ['items.sub_category_id', '=', $my_post->return_post['sub_category_id']],
                ['items.sub_sub_category_id', '!=', $my_post->return_post['sub_sub_category_id']],
                ['items.location_id', '=', $my_post->return_post['location_id']],
                ['items.price', '>=', $my_post->return_post['price_from'] ? $my_post->return_post['price_from'] : 0],
                ['items.price', '<=', $my_post->return_post['price_to'] ? $my_post->return_post['price_to'] : $max],
            ];
            $label_10_c = [
                ['items.category_id', '=', $my_post->return_post['category_id']],
                ['items.sub_category_id', '!=', $my_post->return_post['sub_category_id']],
                ['items.sub_sub_category_id', '!=', $my_post->return_post['sub_sub_category_id']],
                ['items.location_id', '=', $my_post->return_post['location_id']],
                ['items.price', '>=', $my_post->return_post['price_from'] ? $my_post->return_post['price_from'] : 0],
                ['items.price', '<=', $my_post->return_post['price_to'] ? $my_post->return_post['price_to'] : $max],
            ];
            $label_10_e = [
                ['items.category_id', '=', $my_post->return_post['category_id']],
                ['items.sub_category_id', '!=', $my_post->return_post['sub_category_id']],
                ['items.sub_sub_category_id', '!=', $my_post->return_post['sub_sub_category_id']],
                ['items.location_id', '!=', $my_post->return_post['location_id']],
                ['items.price', '>=', $my_post->return_post['price_from'] ? $my_post->return_post['price_from'] : 0],
                ['items.price', '<=', $my_post->return_post['price_to'] ? $my_post->return_post['price_to'] : $max],
            ];
            $label_10_d = [
                ['items.category_id', '!=', $my_post->return_post['category_id']],
                ['items.sub_category_id', '!=', $my_post->return_post['sub_category_id']],
                ['items.sub_sub_category_id', '!=', $my_post->return_post['sub_sub_category_id']],
                ['items.location_id', '=', $my_post->return_post['location_id']],
                ['items.price', '>=', $my_post->return_post['price_from'] ? $my_post->return_post['price_from'] : 0],
                ['items.price', '<=', $my_post->return_post['price_to'] ? $my_post->return_post['price_to'] : $max],
            ];
            $label_10_f = [
                ['items.category_id', '!=', $my_post->return_post['category_id']],
                ['items.sub_category_id', '!=', $my_post->return_post['sub_category_id']],
                ['items.sub_sub_category_id', '!=', $my_post->return_post['sub_sub_category_id']],
                ['items.location_id', '!=', $my_post->return_post['location_id']],
                ['items.price', '>=', $my_post->return_post['price_from'] ? $my_post->return_post['price_from'] : 0],
                ['items.price', '<=', $my_post->return_post['price_to'] ? $my_post->return_post['price_to'] : $max],
            ];

            /////////////////////////////////////////////////////////
            // DOUBLE MATCH : P1 => P9 , P16 => P31 , P36 => P46   //
            // SINGLE MATCH : P10 => P15 , P32 => P35 , P47 => P48 //
            /////////////////////////////////////////////////////////

            // PRIORITY1   
            $matching_posts_p1 = Item::where('items.user_id', '!=', $user)
                ->where($label_10)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p1->isEmpty()) {
                foreach ($matching_posts_p1 as $matching_post_p1) {
                    $value1 = [$matching_post_p1->id => 'priority_1', 'created_at' => $matching_post_p1->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 1' . $matching_post_p1);
                    array_push($p1, $value1);
                }
                $matches->push(...$matching_posts_p1);
            }

            // PRIORITY2
            $matching_posts_p2 = Item::where('items.user_id', '!=', $user)
                ->where($label_10)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_b)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p2->isEmpty()) {
                foreach ($matching_posts_p2 as $matching_post_p2) {
                    $value2 = [$matching_post_p2->id => 'priority_2', 'created_at' => $matching_post_p2->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 2' . $matching_post_p2);
                    array_push($p2, $value2);
                }
                $matches->push(...$matching_posts_p2);
            }

            // PRIORITY3
            $matching_posts_p3 = Item::where('items.user_id', '!=', $user)
                ->where($label_10)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_c)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p3->isEmpty()) {
                foreach ($matching_posts_p3 as $matching_post_p3) {
                    $value3 = [$matching_post_p3->id => 'priority_3', 'created_at' => $matching_post_p3->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 3' . $matching_post_p3);
                    array_push($p3, $value3);
                }
                $matches->push(...$matching_posts_p3);
            }

            // PRIORITY4
            $matching_posts_p4 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_b)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p4->isEmpty()) {
                foreach ($matching_posts_p4 as $matching_post_p4) {
                    $value4 = [$matching_post_p4->id => 'priority_4', 'created_at' => $matching_post_p4->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 4' . $matching_post_p4);
                    array_push($p4, $value4);
                }
                $matches->push(...$matching_posts_p4);
            }

            // PRIORITY5
            $matching_posts_p5 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_c)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p5->isEmpty()) {
                foreach ($matching_posts_p5 as $matching_post_p5) {
                    $value5 = [$matching_post_p5->id => 'priority_5', 'created_at' => $matching_post_p5->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 5' . $matching_post_p5);
                    array_push($p5, $value5);
                }
                $matches->push(...$matching_posts_p5);
            }

            // PRIORITY6
            $matching_posts_p6 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_b)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_b)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p6->isEmpty()) {
                foreach ($matching_posts_p6 as $matching_post_p6) {
                    $value6 = [$matching_post_p6->id => 'priority_6', 'created_at' => $matching_post_p6->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 6' . $matching_post_p6);
                    array_push($p6, $value6);
                }
                $matches->push(...$matching_posts_p6);
            }

            // PRIORITY7
            $matching_posts_p7 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_b)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_c)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p7->isEmpty()) {
                foreach ($matching_posts_p7 as $matching_post_p7) {
                    $value7 = [$matching_post_p7->id => 'priority_7', 'created_at' => $matching_post_p7->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 7' . $matching_post_p7);
                    array_push($p7, $value7);
                }
                $matches->push(...$matching_posts_p7);
            }

            // PRIORITY8
            $matching_posts_p8 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_c)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_b)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p8->isEmpty()) {
                foreach ($matching_posts_p8 as $matching_post_p8) {
                    $value8 = [$matching_post_p8->id => 'priority_8', 'created_at' => $matching_post_p8->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 8' . $matching_post_p8);
                    array_push($p8, $value8);
                }
                $matches->push(...$matching_posts_p8);
            }

            // PRIORITY9
            $matching_posts_p9 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_c)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_c)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p9->isEmpty()) {
                foreach ($matching_posts_p9 as $matching_post_p9) {
                    $value9 = [$matching_post_p9->id => 'priority_9', 'created_at' => $matching_post_p9->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 9' . $matching_post_p9);
                    array_push($p9, $value9);
                }
                $matches->push(...$matching_posts_p9);
            }

            // PRIORITY10
            $matching_posts_p10 = Item::where('items.user_id', '!=', $user)
                ->where($label_10)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where([
                    ['return_items.category_id', '!=', $my_post->category_id],
                    ['return_items.sub_category_id', '!=', $my_post->sub_category_id],
                    ['return_items.sub_sub_category_id', '!=', $my_post->sub_sub_category_id],
                    ['return_items.location_id', '!=', $my_post->location_id],
                    ['return_items.price_from', '>', $my_post->price ? $my_post->price : $max],
                    ['return_items.price_to', '<', $my_post->price ? $my_post->price : 0],
                ])
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p10->isEmpty()) {
                foreach ($matching_posts_p10 as $matching_post_p10) {
                    $value10 = [$matching_post_p10->id => 'priority_10', 'created_at' => $matching_post_p10->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 10' . $matching_post_p10);
                    array_push($p10, $value10);
                }
                $matches->push(...$matching_posts_p10);
            }

            // PRIORITY11
            $data_p11 = Item::where('items.user_id', '!=', $user)
                ->where([
                    ['items.category_id', '!=', $my_post->return_post['category_id']],
                    ['items.sub_category_id', '!=', $my_post->return_post['sub_category_id']],
                    ['items.sub_sub_category_id', '!=', $my_post->return_post['sub_sub_category_id']],
                    ['items.location_id', '!=', $my_post->return_post['location_id']],
                    ['items.price', '<', $my_post->return_post['price_from'] ? $my_post->return_post['price_from'] : 0],
                    ['items.price', '>', $my_post->return_post['price_to'] ? $my_post->return_post['price_to'] : $max],
                ])
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            $ids_p11 = [];
            foreach ($data_p11 as $d_p11) {
                array_push($ids_p11, $d_p11->id);
            }
            $matching_posts_p11 = Item::whereIn('id', $ids_p11)->get();
            if (!$matching_posts_p11->isEmpty()) {
                foreach ($matching_posts_p11 as $matching_post_p11) {
                    $value11 = [$matching_post_p11->id => 'priority_11', 'created_at' => $matching_post_p11->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 11' . $matching_post_p11);
                    array_push($p11, $value11);
                }
                $matches->push(...$matching_posts_p11);
            }

            // PRIORITY12
            $matching_posts_p12 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_b)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where([
                    ['return_items.category_id', '!=', $my_post->category_id],
                    ['return_items.sub_category_id', '!=', $my_post->sub_category_id],
                    ['return_items.sub_sub_category_id', '!=', $my_post->sub_sub_category_id],
                    ['return_items.location_id', '!=', $my_post->location_id],
                    ['return_items.price_from', '>', $my_post->price ? $my_post->price : $max],
                    ['return_items.price_to', '<', $my_post->price ? $my_post->price : 0],
                ])
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p12->isEmpty()) {
                foreach ($matching_posts_p12 as $matching_post_p12) {
                    $value12 = [$matching_post_p12->id => 'priority_12', 'created_at' => $matching_post_p12->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 12' . $matching_post_p12);
                    array_push($p12, $value12);
                }
                $matches->push(...$matching_posts_p12);
            }

            // PRIORITY13
            $data_p13 = Item::where('items.user_id', '!=', $user)
                ->where([
                    ['items.category_id', '!=', $my_post->return_post['category_id']],
                    ['items.sub_category_id', '!=', $my_post->return_post['sub_category_id']],
                    ['items.sub_sub_category_id', '!=', $my_post->return_post['sub_sub_category_id']],
                    ['items.location_id', '!=', $my_post->return_post['location_id']],
                    ['items.price', '<', $my_post->return_post['price_from'] ? $my_post->return_post['price_from'] : 0],
                    ['items.price', '>', $my_post->return_post['price_to'] ? $my_post->return_post['price_to'] : $max],
                ])
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_b)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            $ids_p13 = [];
            foreach ($data_p13 as $d_p13) {
                array_push($ids_p13, $d_p13->id);
            }
            $matching_posts_p13 = Item::whereIn('id', $ids_p13)->get();
            if (!$matching_posts_p13->isEmpty()) {
                foreach ($matching_posts_p13 as $matching_post_p13) {
                    $value13 = [$matching_post_p13->id => 'priority_13', 'created_at' => $matching_post_p13->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 13' . $matching_post_p13);
                    array_push($p13, $value13);
                }
                $matches->push(...$matching_posts_p13);
            }

            // PRIORITY14
            $matching_posts_p14 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_c)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where([
                    ['return_items.category_id', '!=', $my_post->category_id],
                    ['return_items.sub_category_id', '!=', $my_post->sub_category_id],
                    ['return_items.sub_sub_category_id', '!=', $my_post->sub_sub_category_id],
                    ['return_items.location_id', '!=', $my_post->location_id],
                    ['return_items.price_from', '>', $my_post->price ? $my_post->price : $max],
                    ['return_items.price_to', '<', $my_post->price ? $my_post->price : 0],
                ])
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p14->isEmpty()) {
                foreach ($matching_posts_p14 as $matching_post_p14) {
                    $value14 = [$matching_post_p14->id => 'priority_14', 'created_at' => $matching_post_p14->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 14' . $matching_post_p14);
                    array_push($p14, $value14);
                }
                $matches->push(...$matching_posts_p14);
            }

            // PRIORITY15
            $data_p15 = Item::where('items.user_id', '!=', $user)
                ->where([
                    ['items.category_id', '!=', $my_post->return_post['category_id']],
                    ['items.sub_category_id', '!=', $my_post->return_post['sub_category_id']],
                    ['items.sub_sub_category_id', '!=', $my_post->return_post['sub_sub_category_id']],
                    ['items.location_id', '!=', $my_post->return_post['location_id']],
                    ['items.price', '<', $my_post->return_post['price_from'] ? $my_post->return_post['price_from'] : 0],
                    ['items.price', '>', $my_post->return_post['price_to'] ? $my_post->return_post['price_to'] : $max],
                ])
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_c)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            $ids_p15 = [];
            foreach ($data_p15 as $d_p15) {
                array_push($ids_p15, $d_p15->id);
            }
            $matching_posts_p15 = Item::whereIn('id', $ids_p15)->get();
            if (!$matching_posts_p15->isEmpty()) {
                foreach ($matching_posts_p15 as $matching_post_p15) {
                    $value15 = [$matching_post_p15->id => 'priority_15', 'created_at' => $matching_post_p15->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 15' . $matching_post_p15);
                    array_push($p15, $value15);
                }
                $matches->push(...$matching_posts_p15);
            }

            // PRIORITY16
            $matching_posts_p16 = Item::where('items.user_id', '!=', $user)
                ->where($label_10)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_e)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p16->isEmpty()) {
                foreach ($matching_posts_p16 as $matching_post_p16) {
                    $value16 = [$matching_post_p16->id => 'priority_16', 'created_at' => $matching_post_p16->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 16' . $matching_post_p16);
                    array_push($p16, $value16);
                }
                $matches->push(...$matching_posts_p16);
            }

            // PRIORITY17
            $matching_posts_p17 = Item::where('items.user_id', '!=', $user)
                ->where($label_10)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_d)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p17->isEmpty()) {
                foreach ($matching_posts_p17 as $matching_post_p17) {
                    $value17 = [$matching_post_p17->id => 'priority_17', 'created_at' => $matching_post_p17->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 17' . $matching_post_p17);
                    array_push($p17, $value17);
                }
                $matches->push(...$matching_posts_p17);
            }

            // PRIORITY18
            $matching_posts_p18 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_b)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_e)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p18->isEmpty()) {
                foreach ($matching_posts_p18 as $matching_post_p18) {
                    $value18 = [$matching_post_p18->id => 'priority_18', 'created_at' => $matching_post_p18->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 18' . $matching_post_p18);
                    array_push($p18, $value18);
                }
                $matches->push(...$matching_posts_p18);
            }

            // PRIORITY19
            $matching_posts_p19 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_b)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_d)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p19->isEmpty()) {
                foreach ($matching_posts_p19 as $matching_post_p19) {
                    $value19 = [$matching_post_p19->id => 'priority_19', 'created_at' => $matching_post_p19->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 19' . $matching_post_p19);
                    array_push($p19, $value19);
                }
                $matches->push(...$matching_posts_p19);
            }

            // PRIORITY20
            $matching_posts_p20 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_c)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_e)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p20->isEmpty()) {
                foreach ($matching_posts_p20 as $matching_post_p20) {
                    $value20 = [$matching_post_p20->id => 'priority_20', 'created_at' => $matching_post_p20->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 20' . $matching_post_p20);
                    array_push($p20, $value20);
                }
                $matches->push(...$matching_posts_p20);
            }

            // PRIORITY21
            $matching_posts_p21 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_c)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_d)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p21->isEmpty()) {
                foreach ($matching_posts_p21 as $matching_post_p21) {
                    $value21 = [$matching_post_p21->id => 'priority_21', 'created_at' => $matching_post_p21->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 21' . $matching_post_p21);
                    array_push($p21, $value21);
                }
                $matches->push(...$matching_posts_p21);
            }

            // PRIORITY22
            $matching_posts_p22 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_e)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p22->isEmpty()) {
                foreach ($matching_posts_p22 as $matching_post_p22) {
                    $value22 = [$matching_post_p22->id => 'priority_22', 'created_at' => $matching_post_p22->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 22' . $matching_post_p22);
                    array_push($p22, $value22);
                }
                $matches->push(...$matching_posts_p22);
            }

            // PRIORITY23
            $matching_posts_p23 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_d)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p23->isEmpty()) {
                foreach ($matching_posts_p23 as $matching_post_p23) {
                    $value23 = [$matching_post_p23->id => 'priority_23', 'created_at' => $matching_post_p23->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 23' . $matching_post_p23);
                    array_push($p23, $value23);
                }
                $matches->push(...$matching_posts_p23);
            }

            // PRIORITY24
            $matching_posts_p24 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_e)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_b)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p24->isEmpty()) {
                foreach ($matching_posts_p24 as $matching_post_p24) {
                    $value24 = [$matching_post_p24->id => 'priority_24', 'created_at' => $matching_post_p24->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 24' . $matching_post_p24);
                    array_push($p24, $value24);
                }
                $matches->push(...$matching_posts_p24);
            }

            // PRIORITY25
            $matching_posts_p25 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_d)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_b)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p25->isEmpty()) {
                foreach ($matching_posts_p25 as $matching_post_p25) {
                    $value25 = [$matching_post_p25->id => 'priority_25', 'created_at' => $matching_post_p25->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 25' . $matching_post_p25);
                    array_push($p25, $value25);
                }
                $matches->push(...$matching_posts_p25);
            }

            // PRIORITY26
            $matching_posts_p26 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_e)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_c)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p26->isEmpty()) {
                foreach ($matching_posts_p26 as $matching_post_p26) {
                    $value26 = [$matching_post_p26->id => 'priority_26', 'created_at' => $matching_post_p26->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 26' . $matching_post_p26);
                    array_push($p26, $value26);
                }
                $matches->push(...$matching_posts_p26);
            }

            // PRIORITY27
            $matching_posts_p27 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_d)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_c)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p27->isEmpty()) {
                foreach ($matching_posts_p27 as $matching_post_p27) {
                    $value27 = [$matching_post_p27->id => 'priority_27', 'created_at' => $matching_post_p27->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 27' . $matching_post_p27);
                    array_push($p27, $value27);
                }
                $matches->push(...$matching_posts_p27);
            }

            // PRIORITY28
            $matching_posts_p28 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_e)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_e)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p28->isEmpty()) {
                foreach ($matching_posts_p28 as $matching_post_p28) {
                    $value28 = [$matching_post_p28->id => 'priority_28', 'created_at' => $matching_post_p28->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 28' . $matching_post_p28);
                    array_push($p28, $value28);
                }
                $matches->push(...$matching_posts_p28);
            }

            // PRIORITY29
            $matching_posts_p29 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_e)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_d)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p29->isEmpty()) {
                foreach ($matching_posts_p29 as $matching_post_p29) {
                    $value29 = [$matching_post_p29->id => 'priority_29', 'created_at' => $matching_post_p29->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 29' . $matching_post_p29);
                    array_push($p29, $value29);
                }
                $matches->push(...$matching_posts_p29);
            }

            // PRIORITY30
            $matching_posts_p30 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_d)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_e)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p30->isEmpty()) {
                foreach ($matching_posts_p30 as $matching_post_p30) {
                    $value30 = [$matching_post_p30->id => 'priority_30', 'created_at' => $matching_post_p30->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 30' . $matching_post_p30);
                    array_push($p30, $value30);
                }
                $matches->push(...$matching_posts_p30);
            }

            // PRIORITY31
            $matching_posts_p31 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_d)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_d)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p31->isEmpty()) {
                foreach ($matching_posts_p31 as $matching_post_p31) {
                    $value31 = [$matching_post_p31->id => 'priority_31', 'created_at' => $matching_post_p31->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 31' . $matching_post_p31);
                    array_push($p31, $value31);
                }
                $matches->push(...$matching_posts_p31);
            }

            // PRIORITY32
            $matching_posts_p32 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_e)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where([
                    ['return_items.category_id', '!=', $my_post->category_id],
                    ['return_items.sub_category_id', '!=', $my_post->sub_category_id],
                    ['return_items.sub_sub_category_id', '!=', $my_post->sub_sub_category_id],
                    ['return_items.location_id', '!=', $my_post->location_id],
                    ['return_items.price_from', '>', $my_post->price ? $my_post->price : $max],
                    ['return_items.price_to', '<', $my_post->price ? $my_post->price : 0],
                ])
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p32->isEmpty()) {
                foreach ($matching_posts_p32 as $matching_post_p32) {
                    $value32 = [$matching_post_p32->id => 'priority_32', 'created_at' => $matching_post_p32->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 32' . $matching_post_p32);
                    array_push($p32, $value32);
                }
                $matches->push(...$matching_posts_p32);
            }

            // PRIORITY33
            $matching_posts_p33 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_d)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where([
                    ['return_items.category_id', '!=', $my_post->category_id],
                    ['return_items.sub_category_id', '!=', $my_post->sub_category_id],
                    ['return_items.sub_sub_category_id', '!=', $my_post->sub_sub_category_id],
                    ['return_items.location_id', '!=', $my_post->location_id],
                    ['return_items.price_from', '>', $my_post->price ? $my_post->price : $max],
                    ['return_items.price_to', '<', $my_post->price ? $my_post->price : 0],
                ])
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p33->isEmpty()) {
                foreach ($matching_posts_p33 as $matching_post_p33) {
                    $value33 = [$matching_post_p33->id => 'priority_33', 'created_at' => $matching_post_p33->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 33' . $matching_post_p33);
                    array_push($p33, $value33);
                }
                $matches->push(...$matching_posts_p33);
            }

            // PRIORITY34
            $data_p34 = Item::where('items.user_id', '!=', $user)
                ->where([
                    ['items.category_id', '!=', $my_post->return_post['category_id']],
                    ['items.sub_category_id', '!=', $my_post->return_post['sub_category_id']],
                    ['items.sub_sub_category_id', '!=', $my_post->return_post['sub_sub_category_id']],
                    ['items.location_id', '!=', $my_post->return_post['location_id']],
                    ['items.price', '<', $my_post->return_post['price_from'] ? $my_post->return_post['price_from'] : 0],
                    ['items.price', '>', $my_post->return_post['price_to'] ? $my_post->return_post['price_to'] : $max],
                ])
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_e)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            $ids_p34 = [];
            foreach ($data_p34 as $d_p34) {
                array_push($ids_p34, $d_p34->id);
            }
            $matching_posts_p34 = Item::whereIn('id', $ids_p34)->get();
            if (!$matching_posts_p34->isEmpty()) {
                foreach ($matching_posts_p34 as $matching_post_p34) {
                    $value34 = [$matching_post_p34->id => 'priority_34', 'created_at' => $matching_post_p34->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 34' . $matching_post_p34);
                    array_push($p34, $value34);
                }
                $matches->push(...$matching_posts_p34);
            }

            // PRIORITY35
            $data_p35 = Item::where('items.user_id', '!=', $user)
                ->where([
                    ['items.category_id', '!=', $my_post->return_post['category_id']],
                    ['items.sub_category_id', '!=', $my_post->return_post['sub_category_id']],
                    ['items.sub_sub_category_id', '!=', $my_post->return_post['sub_sub_category_id']],
                    ['items.location_id', '!=', $my_post->return_post['location_id']],
                    ['items.price', '<', $my_post->return_post['price_from'] ? $my_post->return_post['price_from'] : 0],
                    ['items.price', '>', $my_post->return_post['price_to'] ? $my_post->return_post['price_to'] : $max],
                ])
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_d)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            $ids_p35 = [];
            foreach ($data_p35 as $d_p35) {
                array_push($ids_p35, $d_p35->id);
            }
            $matching_posts_p35 = Item::whereIn('id', $ids_p35)->get();
            if (!$matching_posts_p35->isEmpty()) {
                foreach ($matching_posts_p35 as $matching_post_p35) {
                    $value35 = [$matching_post_p35->id => 'priority_35', 'created_at' => $matching_post_p35->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 35' . $matching_post_p35);
                    array_push($p35, $value35);
                }
                $matches->push(...$matching_posts_p35);
            }

            // PRIORITY36
            $matching_posts_p36 = Item::where('items.user_id', '!=', $user)
                ->where($label_10)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_f)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p36->isEmpty()) {
                foreach ($matching_posts_p36 as $matching_post_p36) {
                    $value36 = [$matching_post_p36->id => 'priority_36', 'created_at' => $matching_post_p36->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 36' . $matching_post_p36);
                    array_push($p36, $value36);
                }
                $matches->push(...$matching_posts_p36);
            }

            // PRIORITY37
            $matching_posts_p37 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_b)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_f)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p37->isEmpty()) {
                foreach ($matching_posts_p37 as $matching_post_p37) {
                    $value37 = [$matching_post_p37->id => 'priority_37', 'created_at' => $matching_post_p37->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 37' . $matching_post_p37);
                    array_push($p37, $value37);
                }
                $matches->push(...$matching_posts_p37);
            }

            // PRIORITY38
            $matching_posts_p38 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_c)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_f)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p38->isEmpty()) {
                foreach ($matching_posts_p38 as $matching_post_p38) {
                    $value38 = [$matching_post_p38->id => 'priority_38', 'created_at' => $matching_post_p38->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 38' . $matching_post_p38);
                    array_push($p38, $value38);
                }
                $matches->push(...$matching_posts_p38);
            }

            // PRIORITY39
            $matching_posts_p39 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_e)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_f)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p39->isEmpty()) {
                foreach ($matching_posts_p39 as $matching_post_p39) {
                    $value39 = [$matching_post_p39->id => 'priority_39', 'created_at' => $matching_post_p39->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 39' . $matching_post_p39);
                    array_push($p39, $value39);
                }
                $matches->push(...$matching_posts_p39);
            }

            // PRIORITY40
            $matching_posts_p40 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_d)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_f)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p40->isEmpty()) {
                foreach ($matching_posts_p40 as $matching_post_p40) {
                    $value40 = [$matching_post_p40->id => 'priority_40', 'created_at' => $matching_post_p40->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 40' . $matching_post_p40);
                    array_push($p40, $value40);
                }
                $matches->push(...$matching_posts_p40);
            }

            // PRIORITY41
            $matching_posts_p41 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_f)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_f)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p41->isEmpty()) {
                foreach ($matching_posts_p41 as $matching_post_p41) {
                    $value41 = [$matching_post_p41->id => 'priority_41', 'created_at' => $matching_post_p41->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 41' . $matching_post_p41);
                    array_push($p41, $value41);
                }
                $matches->push(...$matching_posts_p41);
            }

            // PRIORITY42
            $matching_posts_p42 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_f)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p42->isEmpty()) {
                foreach ($matching_posts_p42 as $matching_post_p42) {
                    $value42 = [$matching_post_p42->id => 'priority_42', 'created_at' => $matching_post_p42->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 42' . $matching_post_p42);
                    array_push($p42, $value42);
                }
                $matches->push(...$matching_posts_p42);
            }

            // PRIORITY43
            $matching_posts_p43 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_f)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_b)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p43->isEmpty()) {
                foreach ($matching_posts_p43 as $matching_post_p43) {
                    $value43 = [$matching_post_p43->id => 'priority_43', 'created_at' => $matching_post_p43->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 43' . $matching_post_p43);
                    array_push($p43, $value43);
                }
                $matches->push(...$matching_posts_p43);
            }

            // PRIORITY44
            $matching_posts_p44 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_f)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_c)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p44->isEmpty()) {
                foreach ($matching_posts_p44 as $matching_post_p44) {
                    $value44 = [$matching_post_p44->id => 'priority_44', 'created_at' => $matching_post_p44->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 44' . $matching_post_p44);
                    array_push($p44, $value44);
                }
                $matches->push(...$matching_posts_p44);
            }

            // PRIORITY45
            $matching_posts_p45 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_f)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_e)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p45->isEmpty()) {
                foreach ($matching_posts_p45 as $matching_post_p45) {
                    $value45 = [$matching_post_p45->id => 'priority_45', 'created_at' => $matching_post_p45->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 45' . $matching_post_p45);
                    array_push($p45, $value45);
                }
                $matches->push(...$matching_posts_p45);
            }

            // PRIORITY 46
            $matching_posts_p46 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_f)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_d)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p46->isEmpty()) {
                foreach ($matching_posts_p46 as $matching_post_p46) {
                    $value46 = [$matching_post_p46->id => 'priority_46', 'created_at' => $matching_post_p46->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 46' . $matching_post_p46);
                    array_push($p46, $value46);
                }
                $matches->push(...$matching_posts_p46);
            }

            // PRIORITY47
            $matching_posts_p47 = Item::where('items.user_id', '!=', $user)
                ->where($label_10_f)
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where([
                    ['return_items.category_id', '!=', $my_post->category_id],
                    ['return_items.sub_category_id', '!=', $my_post->sub_category_id],
                    ['return_items.sub_sub_category_id', '!=', $my_post->sub_sub_category_id],
                    ['return_items.location_id', '!=', $my_post->location_id],
                    ['return_items.price_from', '>', $my_post->price ? $my_post->price : $max],
                    ['return_items.price_to', '<', $my_post->price ? $my_post->price : 0],
                ])
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            if (!$matching_posts_p47->isEmpty()) {
                foreach ($matching_posts_p47 as $matching_post_p47) {
                    $value47 = [$matching_post_p47->id => 'priority_47', 'created_at' => $matching_post_p47->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 47' . $matching_post_p47);
                    array_push($p47, $value47);
                }
                $matches->push(...$matching_posts_p47);
            }

            // PRIORITY48
            $data_p48 = Item::where('items.user_id', '!=', $user)
                ->where([
                    ['items.category_id', '!=', $my_post->return_post['category_id']],
                    ['items.sub_category_id', '!=', $my_post->return_post['sub_category_id']],
                    ['items.sub_sub_category_id', '!=', $my_post->return_post['sub_sub_category_id']],
                    ['items.location_id', '!=', $my_post->return_post['location_id']],
                    ['items.price', '<', $my_post->return_post['price_from'] ? $my_post->return_post['price_from'] : 0],
                    ['items.price', '>', $my_post->return_post['price_to'] ? $my_post->return_post['price_to'] : $max],
                ])
                ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                ->where($label_9_f)
                ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                ->orderBy('items.created_at', 'desc')
                ->get(['items.id', 'items.created_at']);
            $ids_p48 = [];
            foreach ($data_p48 as $d_p48) {
                array_push($ids_p48, $d_p48->id);
            }
            $matching_posts_p48 = Item::whereIn('id', $ids_p48)->get();
            if (!$matching_posts_p48->isEmpty()) {
                foreach ($matching_posts_p48 as $matching_post_p48) {
                    $value48 = [$matching_post_p48->id => 'priority_48', 'created_at' => $matching_post_p48->created_at];
                    Log::info($my_post->id);
                    Log::info('Priority 48' . $matching_post_p48);
                    array_push($p48, $value48);
                }
                $matches->push(...$matching_posts_p48);
            }

            if ($my_post->return_id == null) {
                // // PRIORITY49 TODO
                // $matching_posts_p49 = Item::where('user_id','!=',$user)
                // ->where('type','offer')->where('return_id',null)
                // ->where([
                //     ['category_id', '=', $my_post->return_post['category_id']],
                //     ['sub_category_id', '=', $my_post->return_post['sub_category_id']],
                //     ['sub_sub_category_id', '=', $my_post->return_post['sub_sub_category_id']],
                //     ['location_id', '=', $my_post->return_post['location_id']],
                //     ['price', '>=', $my_post->return_post['price_from'] ? $my_post->return_post['price_from'] : 0],
                //     ['price', '<=', $my_post->return_post['price_to'] ? $my_post->return_post['price_to'] : $max],
                // ])
                // ->where([
                //     ['price', '>=', $my_post->price_from ? $my_post->price_from / 2 : 0],
                //     ['price', '<=', $my_post->price_to ? $my_post->price_to * 2 : $max],
                // ])
                // ->where('expired_at','>',Carbon::now()->getTimestamp())
                // ->orderBy('created_at','desc')->get();
                // if(!$matching_posts_p49->isEmpty()){
                //     foreach ($matching_posts_p49 as $matching_post_p49) {
                //     $value49 = [$matching_post_p49->id => 'priority_49' , 'created_at' => $matching_post_p49->created_at];
                //         Log::info($my_post->id);    
                //         Log::info('Priority 49' . $matching_post_p49);
                //         array_push($p49, $value49);
                //     }
                //     $matches->push(...$matching_posts_p49);
                // }

                // // PRIORITY50 TODO
                // $matching_posts_p50 = Item::where('user_id','!=',$user)
                // ->where('type','offer')->where('return_id',null)
                // ->where([
                //     ['category_id', '=', $my_post->return_post['category_id']],
                //     ['sub_category_id', '=', $my_post->return_post['sub_category_id']],
                //     ['sub_sub_category_id', '!=', $my_post->return_post['sub_sub_category_id']],
                //     ['location_id', '=', $my_post->return_post['location_id']],
                //     ['price', '>=', $my_post->return_post['price_from'] ? $my_post->return_post['price_from'] : 0],
                //     ['price', '<=', $my_post->return_post['price_to'] ? $my_post->return_post['price_to'] : $max],
                // ])
                // ->where([
                //     ['price', '>=', $my_post->price_from ? $my_post->price_from / 2 : 0],
                //     ['price', '<=', $my_post->price_to ? $my_post->price_to * 2 : $max],
                // ])
                // ->where('expired_at','>',Carbon::now()->getTimestamp())
                // ->orderBy('created_at','desc')->get();
                // if(!$matching_posts_p50->isEmpty()){
                //     foreach ($matching_posts_p50 as $matching_post_p50) {
                //     $value50 = [$matching_post_p50->id => 'priority_50' , 'created_at' => $matching_post_p50->created_at];
                //         Log::info($my_post->id);    
                //         Log::info('Priority 50' . $matching_post_p50);
                //         array_push($p50, $value50);
                //     }
                //     $matches->push(...$matching_posts_p50);
                // }

                // // PRIORITY51 TODO
                // $matching_posts_p51 = Item::where('user_id','!=',$user)
                // ->where('type','offer')->where('return_id',null)
                // ->where([
                //     ['category_id', '=', $my_post->return_post['category_id']],
                //     ['sub_category_id', '!=', $my_post->return_post['sub_category_id']],
                //     ['sub_sub_category_id', '!=', $my_post->return_post['sub_sub_category_id']],
                //     ['location_id', '=', $my_post->return_post['location_id']],
                //     ['price', '>=', $my_post->return_post['price_from'] ? $my_post->return_post['price_from'] : 0],
                //     ['price', '<=', $my_post->return_post['price_to'] ? $my_post->return_post['price_to'] : $max],
                // ])
                // ->where([
                //     ['price', '>=', $my_post->price_from ? $my_post->price_from / 2 : 0],
                //     ['price', '<=', $my_post->price_to ? $my_post->price_to * 2 : $max],
                // ])
                // ->where('expired_at','>',Carbon::now()->getTimestamp())
                // ->orderBy('created_at','desc')->get();
                // if(!$matching_posts_p51->isEmpty()){
                //     foreach ($matching_posts_p51 as $matching_post_p51) {
                //     $value51 = [$matching_post_p51->id => 'priority_51' , 'created_at' => $matching_post_p51->created_at];
                //         Log::info($my_post->id);    
                //         Log::info('Priority 51' . $matching_post_p51);
                //         array_push($p51, $value51);
                //     }
                //     $matches->push(...$matching_posts_p51);
                // }

                // // PRIORITY52 TODO
                // $matching_posts_p52 = Item::where('user_id','!=',$user)
                // ->where('type','offer')->where('return_id',null)
                // ->where([
                //     ['category_id', '=', $my_post->return_post['category_id']],
                //     ['sub_category_id', '!=', $my_post->return_post['sub_category_id']],
                //     ['sub_sub_category_id', '!=', $my_post->return_post['sub_sub_category_id']],
                //     ['location_id', '!=', $my_post->return_post['location_id']],
                //     ['price', '>=', $my_post->return_post['price_from'] ? $my_post->return_post['price_from'] : 0],
                //     ['price', '<=', $my_post->return_post['price_to'] ? $my_post->return_post['price_to'] : $max],
                // ])
                // ->where([
                //     ['price', '>=', $my_post->price_from ? $my_post->price_from / 2 : 0],
                //     ['price', '<=', $my_post->price_to ? $my_post->price_to * 2 : $max],
                // ])
                // ->where('expired_at','>',Carbon::now()->getTimestamp())
                // ->orderBy('created_at','desc')->get();
                // if(!$matching_posts_p52->isEmpty()){
                //     foreach ($matching_posts_p52 as $matching_post_p52) {
                //     $value52 = [$matching_post_p52->id => 'priority_52' , 'created_at' => $matching_post_p52->created_at];
                //         Log::info($my_post->id);    
                //         Log::info('Priority 52' . $matching_post_p52);
                //         array_push($p52, $value52);
                //     }
                //     $matches->push(...$matching_posts_p52);
                // }

                // // PRIORITY53 TODO
                // $matching_posts_p53 = Item::where('user_id','!=',$user)
                // ->where('type','offer')->where('return_id',null)
                // ->where([
                //     ['category_id', '!=', $my_post->return_post['category_id']],
                //     ['sub_category_id', '!=', $my_post->return_post['sub_category_id']],
                //     ['sub_sub_category_id', '!=', $my_post->return_post['sub_sub_category_id']],
                //     ['location_id', '=', $my_post->return_post['location_id']],
                //     ['price', '>=', $my_post->return_post['price_from'] ? $my_post->return_post['price_from'] : 0],
                //     ['price', '<=', $my_post->return_post['price_to'] ? $my_post->return_post['price_to'] : $max],
                // ])
                // ->where([
                //     ['price', '>=', $my_post->price_from ? $my_post->price_from / 2 : 0],
                //     ['price', '<=', $my_post->price_to ? $my_post->price_to * 2 : $max],
                // ])
                // ->where('expired_at','>',Carbon::now()->getTimestamp())
                // ->orderBy('created_at','desc')->get();
                // if(!$matching_posts_p53->isEmpty()){
                //     foreach ($matching_posts_p53 as $matching_post_p53) {
                //     $value53 = [$matching_post_p53->id => 'priority_53' , 'created_at' => $matching_post_p53->created_at];
                //         Log::info($my_post->id);    
                //         Log::info('Priority 53' . $matching_post_p53);
                //         array_push($p53, $value53);
                //     }
                //     $matches->push(...$matching_posts_p53);
                // }

                // PRIORITY54
                $matching_posts_p54 = Item::where('items.user_id', '!=', $user)
                    ->where([
                        ['items.category_id', '!=', $my_post->category_id],
                        ['items.sub_category_id', '!=', $my_post->sub_category_id],
                        ['items.sub_sub_category_id', '!=', $my_post->sub_sub_category_id],
                        ['items.location_id', '!=', $my_post->location_id],
                        ['items.price', '>=', $my_post->price ? $my_post->price - ($my_post->price / 2) : 0],
                        ['items.price', '<=', $my_post->price ? $my_post->price * 2 : $max],
                    ])
                    ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                    ->where($label_9)
                    ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                    ->orderBy('items.created_at', 'desc')
                    ->get(['items.id', 'items.created_at']);
                if (!$matching_posts_p54->isEmpty()) {
                    foreach ($matching_posts_p54 as $matching_post_p54) {
                        $value54 = [$matching_post_p54->id => 'priority_54', 'created_at' => $matching_post_p54->created_at];
                        Log::info($my_post->id);
                        Log::info('Priority 54' . $matching_post_p54);
                        array_push($p54, $value54);
                    }
                    $matches->push(...$matching_posts_p54);
                }

                // PRIORITY55
                $matching_posts_p55 = Item::where('items.user_id', '!=', $user)
                    ->where([
                        ['items.category_id', '!=', $my_post->category_id],
                        ['items.sub_category_id', '!=', $my_post->sub_category_id],
                        ['items.sub_sub_category_id', '!=', $my_post->sub_sub_category_id],
                        ['items.location_id', '!=', $my_post->location_id],
                        ['items.price', '>=', $my_post->price ? $my_post->price - ($my_post->price / 2) : 0],
                        ['items.price', '<=', $my_post->price ? $my_post->price * 2 : $max],
                    ])
                    ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                    ->where($label_9_b)
                    ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                    ->orderBy('items.created_at', 'desc')
                    ->get(['items.id', 'items.created_at']);
                if (!$matching_posts_p55->isEmpty()) {
                    foreach ($matching_posts_p55 as $matching_post_p55) {
                        $value55 = [$matching_post_p55->id => 'priority_55', 'created_at' => $matching_post_p55->created_at];
                        Log::info($my_post->id);
                        Log::info('Priority 55' . $matching_post_p55);
                        array_push($p55, $value55);
                    }
                    $matches->push(...$matching_posts_p55);
                }

                // PRIORITY56
                $matching_posts_p56 = Item::where('items.user_id', '!=', $user)
                    ->where([
                        ['items.category_id', '!=', $my_post->category_id],
                        ['items.sub_category_id', '!=', $my_post->sub_category_id],
                        ['items.sub_sub_category_id', '!=', $my_post->sub_sub_category_id],
                        ['items.location_id', '!=', $my_post->location_id],
                        ['items.price', '>=', $my_post->price ? $my_post->price - ($my_post->price / 2) : 0],
                        ['items.price', '<=', $my_post->price ? $my_post->price * 2 : $max],
                    ])
                    ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                    ->where($label_9_c)
                    ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                    ->orderBy('items.created_at', 'desc')
                    ->get(['items.id', 'items.created_at']);
                if (!$matching_posts_p56->isEmpty()) {
                    foreach ($matching_posts_p56 as $matching_post_p56) {
                        $value56 = [$matching_post_p56->id => 'priority_56', 'created_at' => $matching_post_p56->created_at];
                        Log::info($my_post->id);
                        Log::info('Priority 56' . $matching_post_p56);
                        array_push($p56, $value56);
                    }
                    $matches->push(...$matching_posts_p56);
                }

                // PRIORITY57
                $matching_posts_p57 = Item::where('items.user_id', '!=', $user)
                    ->where([
                        ['items.category_id', '!=', $my_post->category_id],
                        ['items.sub_category_id', '!=', $my_post->sub_category_id],
                        ['items.sub_sub_category_id', '!=', $my_post->sub_sub_category_id],
                        ['items.location_id', '!=', $my_post->location_id],
                        ['items.price', '>=', $my_post->price ? $my_post->price - ($my_post->price / 2) : 0],
                        ['items.price', '<=', $my_post->price ? $my_post->price * 2 : $max],
                    ])
                    ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                    ->where($label_9_e)
                    ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                    ->orderBy('items.created_at', 'desc')
                    ->get(['items.id', 'items.created_at']);
                if (!$matching_posts_p57->isEmpty()) {
                    foreach ($matching_posts_p57 as $matching_post_p57) {
                        $value57 = [$matching_post_p57->id => 'priority_57', 'created_at' => $matching_post_p57->created_at];
                        Log::info($my_post->id);
                        Log::info('Priority 57' . $matching_post_p57);
                        array_push($p57, $value57);
                    }
                    $matches->push(...$matching_posts_p57);
                }

                // PRIORITY58
                $matching_posts_p58 = Item::where('items.user_id', '!=', $user)
                    ->where([
                        ['items.category_id', '!=', $my_post->category_id],
                        ['items.sub_category_id', '!=', $my_post->sub_category_id],
                        ['items.sub_sub_category_id', '!=', $my_post->sub_sub_category_id],
                        ['items.location_id', '!=', $my_post->location_id],
                        ['items.price', '>=', $my_post->price ? $my_post->price - ($my_post->price / 2) : 0],
                        ['items.price', '<=', $my_post->price ? $my_post->price * 2 : $max],
                    ])
                    ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                    ->where($label_9_d)
                    ->where('items.expired_at', '>', Carbon::now()->getTimestamp())
                    ->orderBy('items.created_at', 'desc')
                    ->get(['items.id', 'items.created_at']);
                if (!$matching_posts_p58->isEmpty()) {
                    foreach ($matching_posts_p58 as $matching_post_p58) {
                        $value58 = [$matching_post_p58->id => 'priority_58', 'created_at' => $matching_post_p58->created_at];
                        Log::info($my_post->id);
                        Log::info('Priority 58' . $matching_post_p58);
                        array_push($p58, $value58);
                    }
                    $matches->push(...$matching_posts_p58);
                }

                // PRIORITY59
                $matching_posts_p59 = Item::where('user_id', '!=', $user)
                    ->where('type', 'offer')->where('return_id', null)
                    ->where([
                        ['category_id', '!=', $my_post->category_id],
                        ['sub_category_id', '!=', $my_post->sub_category_id],
                        ['sub_sub_category_id', '!=', $my_post->sub_sub_category_id],
                        ['location_id', '!=', $my_post->location_id],
                        ['price', '>=', $my_post->price ? $my_post->price - ($my_post->price / 2) : 0],
                        ['price', '<=', $my_post->price ? $my_post->price * 2 : $max],
                    ])
                    ->where('expired_at', '>', Carbon::now()->getTimestamp())
                    ->orderBy('created_at', 'desc')
                    ->get(['id', 'created_at']);

                if (!empty($matching_posts_p59[0])) {
                    $price_from = $matching_posts_p59 ? $matching_posts_p59[0]['price'] - ($matching_posts_p59[0]['price'] / 2) : 0;
                    $price_to = $matching_posts_p59 ? $matching_posts_p59[0]['price'] * 2 : $max;

                    if (!$matching_posts_p59->isEmpty() && $my_post->price >= $price_from && $my_post->price <= $price_to) {
                        foreach ($matching_posts_p59 as $matching_post_p59) {
                            $value59 = [$matching_post_p59->id => 'priority_59', 'created_at' => $matching_post_p59->created_at];
                            Log::info($my_post->id);
                            Log::info('Priority 59' . $matching_post_p59);
                            array_push($p59, $value59);
                        }
                        $matches->push(...$matching_posts_p59);
                    }
                }

                // // PRIORITY60 TODO
                // $matching_posts_p60 = Item::where('items.user_id','!=',$user)
                // ->join('items AS return_items', 'items.return_id', '=', 'return_items.id')
                // ->where([
                //     ['items.category_id', '!=', $my_post->category_id],
                //     ['items.sub_category_id', '!=', $my_post->sub_category_id],
                //     ['items.sub_sub_category_id', '!=', $my_post->sub_sub_category_id],
                //     ['items.location_id', '!=', $my_post->location_id],
                //     ['items.price', '>=', $my_post->price_from ? $my_post->price_from : 0],
                //     ['items.price', '<=', $my_post->price_to ? $my_post->price_to : $max],
                // ])
                // ->where([
                //     ['items.price', '>=', $my_post->price ? $my_post->price / 2 : 0],
                //     ['items.price', '<=', $my_post->price ? $my_post->price * 2 : $max],
                // ])
                // ->where('items.expired_at','>',Carbon::now()->getTimestamp())
                // ->orderBy('items.created_at','desc')
                // ->get(['items.id','items.created_at']);
                // if(!$matching_posts_p60->isEmpty()){
                //     foreach ($matching_posts_p60 as $matching_post_p60) {
                //     $value60 = [$matching_post_p60->id => 'priority_60' , 'created_at' => $matching_post_p60->created_at];
                //         Log::info($my_post->id);    
                //         Log::info('Priority 60' . $matching_post_p60);
                //         array_push($p60, $value60);
                //     }
                //     $matches->push(...$matching_posts_p60);
                // }

                // PRIORITY61
                $matching_posts_p61 = Item::where('user_id', '!=', $user)
                    ->where('type', 'offer')->where('return_id', null)
                    ->where([
                        ['category_id', '!=', $my_post->category_id],
                        ['sub_category_id', '!=', $my_post->sub_category_id],
                        ['sub_sub_category_id', '!=', $my_post->sub_sub_category_id],
                        ['location_id', '!=', $my_post->location_id],
                        ['price', '>=', $my_post->price ? $my_post->price - ($my_post->price / 2) : 0],
                        ['price', '<=', $my_post->price ? $my_post->price * 2 : $max],
                    ])
                    ->where('expired_at', '>', Carbon::now()->getTimestamp())
                    ->orderBy('created_at', 'desc')
                    ->get(['id', 'created_at']);
                if (!$matching_posts_p61->isEmpty()) {
                    foreach ($matching_posts_p61 as $matching_post_p61) {
                        $value61 = [$matching_post_p61->id => 'priority_61', 'created_at' => $matching_post_p61->created_at];
                        Log::info($my_post->id);
                        Log::info('Priority 61' . $matching_post_p61);
                        array_push($p61, $value61);
                    }
                    $matches->push(...$matching_posts_p61);
                }
            }
        }

        $timestamps = array_column($p1, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p1);
        $merged_array = array_merge($merged_array, $p1);
        $timestamps = array_column($p2, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p2);
        $merged_array = array_merge($merged_array, $p2);
        $timestamps = array_column($p3, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p3);
        $merged_array = array_merge($merged_array, $p3);
        $timestamps = array_column($p4, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p4);
        $merged_array = array_merge($merged_array, $p4);
        $timestamps = array_column($p5, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p5);
        $merged_array = array_merge($merged_array, $p5);
        $timestamps = array_column($p6, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p6);
        $merged_array = array_merge($merged_array, $p6);
        $timestamps = array_column($p7, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p7);
        $merged_array = array_merge($merged_array, $p7);
        $timestamps = array_column($p8, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p8);
        $merged_array = array_merge($merged_array, $p8);
        $timestamps = array_column($p9, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p9);
        $merged_array = array_merge($merged_array, $p9);
        $timestamps = array_column($p10, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p10);
        $merged_array = array_merge($merged_array, $p10);
        $timestamps = array_column($p11, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p11);
        $merged_array = array_merge($merged_array, $p11);
        $timestamps = array_column($p12, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p12);
        $merged_array = array_merge($merged_array, $p12);
        $timestamps = array_column($p13, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p13);
        $merged_array = array_merge($merged_array, $p13);
        $timestamps = array_column($p14, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p14);
        $merged_array = array_merge($merged_array, $p14);
        $timestamps = array_column($p15, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p15);
        $merged_array = array_merge($merged_array, $p15);
        $timestamps = array_column($p16, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p16);
        $merged_array = array_merge($merged_array, $p16);
        $timestamps = array_column($p17, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p17);
        $merged_array = array_merge($merged_array, $p17);
        $timestamps = array_column($p18, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p18);
        $merged_array = array_merge($merged_array, $p18);
        $timestamps = array_column($p19, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p19);
        $merged_array = array_merge($merged_array, $p19);
        $timestamps = array_column($p20, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p20);
        $merged_array = array_merge($merged_array, $p20);
        $timestamps = array_column($p21, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p21);
        $merged_array = array_merge($merged_array, $p21);
        $timestamps = array_column($p22, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p22);
        $merged_array = array_merge($merged_array, $p22);
        $timestamps = array_column($p23, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p23);
        $merged_array = array_merge($merged_array, $p23);
        $timestamps = array_column($p24, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p24);
        $merged_array = array_merge($merged_array, $p24);
        $timestamps = array_column($p25, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p25);
        $merged_array = array_merge($merged_array, $p25);
        $timestamps = array_column($p26, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p26);
        $merged_array = array_merge($merged_array, $p26);
        $timestamps = array_column($p27, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p27);
        $merged_array = array_merge($merged_array, $p27);
        $timestamps = array_column($p28, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p28);
        $merged_array = array_merge($merged_array, $p28);
        $timestamps = array_column($p29, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p29);
        $merged_array = array_merge($merged_array, $p29);
        $timestamps = array_column($p30, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p30);
        $merged_array = array_merge($merged_array, $p30);
        $timestamps = array_column($p31, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p31);
        $merged_array = array_merge($merged_array, $p31);
        $timestamps = array_column($p32, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p32);
        $merged_array = array_merge($merged_array, $p32);
        $timestamps = array_column($p33, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p33);
        $merged_array = array_merge($merged_array, $p33);
        $timestamps = array_column($p34, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p34);
        $merged_array = array_merge($merged_array, $p34);
        $timestamps = array_column($p35, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p35);
        $merged_array = array_merge($merged_array, $p35);
        $timestamps = array_column($p36, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p36);
        $merged_array = array_merge($merged_array, $p36);
        $timestamps = array_column($p37, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p37);
        $merged_array = array_merge($merged_array, $p37);
        $timestamps = array_column($p38, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p38);
        $merged_array = array_merge($merged_array, $p38);
        $timestamps = array_column($p39, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p39);
        $merged_array = array_merge($merged_array, $p39);
        $timestamps = array_column($p40, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p40);
        $merged_array = array_merge($merged_array, $p40);
        $timestamps = array_column($p41, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p41);
        $merged_array = array_merge($merged_array, $p41);
        $timestamps = array_column($p42, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p42);
        $merged_array = array_merge($merged_array, $p42);
        $timestamps = array_column($p43, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p43);
        $merged_array = array_merge($merged_array, $p43);
        $timestamps = array_column($p44, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p44);
        $merged_array = array_merge($merged_array, $p44);
        $timestamps = array_column($p45, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p45);
        $merged_array = array_merge($merged_array, $p45);
        $timestamps = array_column($p46, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p46);
        $merged_array = array_merge($merged_array, $p46);
        $timestamps = array_column($p47, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p47);
        $merged_array = array_merge($merged_array, $p47);
        $timestamps = array_column($p48, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p48);
        $merged_array = array_merge($merged_array, $p48);
        $timestamps = array_column($p49, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p49);
        $merged_array = array_merge($merged_array, $p49);
        $timestamps = array_column($p50, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p50);
        $merged_array = array_merge($merged_array, $p50);
        $timestamps = array_column($p51, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p51);
        $merged_array = array_merge($merged_array, $p51);
        $timestamps = array_column($p52, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p52);
        $merged_array = array_merge($merged_array, $p52);
        $timestamps = array_column($p53, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p53);
        $merged_array = array_merge($merged_array, $p53);
        $timestamps = array_column($p54, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p54);
        $merged_array = array_merge($merged_array, $p54);
        $timestamps = array_column($p55, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p55);
        $merged_array = array_merge($merged_array, $p55);
        $timestamps = array_column($p56, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p56);
        $merged_array = array_merge($merged_array, $p56);
        $timestamps = array_column($p57, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p57);
        $merged_array = array_merge($merged_array, $p57);
        $timestamps = array_column($p58, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p58);
        $merged_array = array_merge($merged_array, $p58);
        $timestamps = array_column($p59, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p59);
        $merged_array = array_merge($merged_array, $p59);
        $timestamps = array_column($p60, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p60);
        $merged_array = array_merge($merged_array, $p60);
        $timestamps = array_column($p61, 'created_at');
        array_multisort($timestamps, SORT_DESC, $p61);
        $merged_array = array_merge($merged_array, $p61);

        array_push($matching_ids, $merged_array);

        $uniqueKeys = array_map(function ($item) {
            return array_unique(array_keys($item));
        }, $matching_ids[0]);

        $uniqueKeys = array_merge(...$uniqueKeys);

        $uniqueValues = array_unique(array_values($uniqueKeys));

        $uniqueValues = array_filter($uniqueValues, function ($value) {
            return $value !== "created_at";
        });
        return array_values($uniqueValues);
    }
}
