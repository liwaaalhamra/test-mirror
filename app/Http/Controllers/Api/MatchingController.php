<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\Item;
use Illuminate\Http\Request;

class MatchingController extends Controller
{
    /**
     * Get matching results
     * @param Request $request
     * @return \stdClass
     */
    public function index(Request $request)
    {
        $user = null;
        if ($request->header('token')) {
            $user = $this->validate_user($request);
        }
        $my_posts = Item::whereUserId(11)->whereType('offer')->get();
        $others_posts = Item::where('user_id', '!=', 11)->whereType('offer')->get();
        foreach ($my_posts as $my_post) {
            $result = $this->returnResult($my_post, $others_posts);
        }
        if (!$result) {
            return response()->json("No matching items");
        }
        return response()->json($result);
    }

    function returnResult($post_1, $post_2)
    {
        $matches = [];

        $priority_1 = $this->priority_1($post_1, $post_2);

        // dd(json_encode($priority_1[0]));

        if ($priority_1 && $priority_1[0] != null) {
            if( !array_key_exists($priority_1[0]["id"], $matches)) {
                $matches[$priority_1[0]["id"]] = $priority_1;
            }
            // array_push($matches, [
            //     $priority_1[0]["id"] => $priority_1
            // ]);
        }

        dd(json_encode($matches));

        $priority_2 = $this->priority_2($post_1, $post_2);
        if ($priority_2 && $priority_2[0] != null) {
            array_push($matches, $priority_2);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_2[$j]['id']) {
                        unset($priority_2[$j]);
                    }
                }
            }
        }
        $priority_3 = $this->priority_3($post_1, $post_2);
        if ($priority_3 && $priority_3[0] != null) {
            array_push($matches, $priority_3);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_3[$j]['id']) {
                        unset($priority_3[$j]);
                    }
                }
            }
        }
        $priority_4 = $this->priority_4($post_1, $post_2);
        if ($priority_4 && $priority_4[0] != null) {
            array_push($matches, $priority_4);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_4[$j]['id']) {
                        unset($priority_4[$j]);
                    }
                }
            }
        }
        $priority_5 = $this->priority_5($post_1, $post_2);
        if ($priority_5 && $priority_5[0] != null) {
            array_push($matches, $priority_5);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_5[$j]['id']) {
                        unset($priority_5[$j]);
                    }
                }
            }
        }
        $priority_6 = $this->priority_6($post_1, $post_2);
        if ($priority_6 && $priority_6[0] != null) {
            array_push($matches, $priority_6);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_6[$j]['id']) {
                        unset($priority_6[$j]);
                    }
                }
            }
        }
        $priority_7 = $this->priority_7($post_1, $post_2);
        if ($priority_7 && $priority_7[0] != null) {
            array_push($matches, $priority_7);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_7[$j]['id']) {
                        unset($priority_7[$j]);
                    }
                }
            }
        }
        $priority_8 = $this->priority_8($post_1, $post_2);
        if ($priority_8 && $priority_8[0] != null) {
            array_push($matches, $priority_8);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_8[$j]['id']) {
                        unset($priority_8[$j]);
                    }
                }
            }
        }
        $priority_9 = $this->priority_9($post_1, $post_2);
        if ($priority_9 && $priority_9[0] != null) {
            array_push($matches, $priority_9);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_9[$j]['id']) {
                        unset($priority_9[$j]);
                    }
                }
            }
        }
        $priority_10 = $this->priority_10($post_1, $post_2);
        if ($priority_10 && $priority_10[0] != null) {
            array_push($matches, $priority_10);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_10[$j]['id']) {
                        unset($priority_10[$j]);
                    }
                }
            }
        }
        $priority_11 = $this->priority_11($post_1, $post_2);
        if ($priority_11 && $priority_11[0] != null) {
            array_push($matches, $priority_11);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_11[$j]['id']) {
                        unset($priority_11[$j]);
                    }
                }
            }
        }
        $priority_12 = $this->priority_12($post_1, $post_2);
        if ($priority_12 && $priority_12[0] != null) {
            array_push($matches, $priority_12);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_12[$j]['id']) {
                        unset($priority_12[$j]);
                    }
                }
            }
        }
        $priority_13 = $this->priority_13($post_1, $post_2);
        if ($priority_13 && $priority_13[0] != null) {
            array_push($matches, $priority_13);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_13[$j]['id']) {
                        unset($priority_13[$j]);
                    }
                }
            }
        }
        $priority_14 = $this->priority_14($post_1, $post_2);
        if ($priority_14 && $priority_14[0] != null) {
            array_push($matches, $priority_14);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_14[$j]['id']) {
                        unset($priority_14[$j]);
                    }
                }
            }
        }
        $priority_15 = $this->priority_15($post_1, $post_2);
        if ($priority_15 && $priority_15[0] != null) {
            array_push($matches, $priority_15);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_15[$j]['id']) {
                        unset($priority_15[$j]);
                    }
                }
            }
        }
        $priority_16 = $this->priority_16($post_1, $post_2);
        if ($priority_16 && $priority_16[0] != null) {
            array_push($matches, $priority_16);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_16[$j]['id']) {
                        unset($priority_16[$j]);
                    }
                }
            }
        }
        $priority_17 = $this->priority_17($post_1, $post_2);
        if ($priority_17 && $priority_17[0] != null) {
            array_push($matches, $priority_17);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_17[$j]['id']) {
                        unset($priority_17[$j]);
                    }
                }
            }
        }
        $priority_18 = $this->priority_18($post_1, $post_2);
        if ($priority_18 && $priority_18[0] != null) {
            array_push($matches, $priority_18);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_18[$j]['id']) {
                        unset($priority_18[$j]);
                    }
                }
            }
        }
        $priority_19 = $this->priority_19($post_1, $post_2);
        if ($priority_19 && $priority_19[0] != null) {
            array_push($matches, $priority_19);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_19[$j]['id']) {
                        unset($priority_19[$j]);
                    }
                }
            }
        }
        $priority_20 = $this->priority_20($post_1, $post_2);
        if ($priority_20 && $priority_20[0] != null) {
            array_push($matches, $priority_20);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_20[$j]['id']) {
                        unset($priority_20[$j]);
                    }
                }
            }
        }
        $priority_21 = $this->priority_21($post_1, $post_2);
        if ($priority_21 && $priority_21[0] != null) {
            array_push($matches, $priority_21);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_21[$j]['id']) {
                        unset($priority_21[$j]);
                    }
                }
            }
        }
        $priority_22 = $this->priority_22($post_1, $post_2);
        if ($priority_22 && $priority_22[0] != null) {
            array_push($matches, $priority_22);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_22[$j]['id']) {
                        unset($priority_22[$j]);
                    }
                }
            }
        }
        $priority_23 = $this->priority_23($post_1, $post_2);
        if ($priority_23 && $priority_23[0] != null) {
            array_push($matches, $priority_23);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_23[$j]['id']) {
                        unset($priority_23[$j]);
                    }
                }
            }
        }
        $priority_24 = $this->priority_24($post_1, $post_2);
        if ($priority_24 && $priority_24[0] != null) {
            array_push($matches, $priority_24);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_24[$j]['id']) {
                        unset($priority_24[$j]);
                    }
                }
            }
        }
        $priority_25 = $this->priority_25($post_1, $post_2);
        if ($priority_25 && $priority_25[0] != null) {
            array_push($matches, $priority_25);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_25[$j]['id']) {
                        unset($priority_25[$j]);
                    }
                }
            }
        }
        $priority_26 = $this->priority_26($post_1, $post_2);
        if ($priority_26 && $priority_26[0] != null) {
            array_push($matches, $priority_26);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_26[$j]['id']) {
                        unset($priority_26[$j]);
                    }
                }
            }
        }
        $priority_27 = $this->priority_27($post_1, $post_2);
        if ($priority_27 && $priority_27[0] != null) {
            array_push($matches, $priority_27);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_27[$j]['id']) {
                        unset($priority_27[$j]);
                    }
                }
            }
        }
        $priority_28 = $this->priority_28($post_1, $post_2);
        if ($priority_28 && $priority_28[0] != null) {
            array_push($matches, $priority_28);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_28[$j]['id']) {
                        unset($priority_28[$j]);
                    }
                }
            }
        }
        $priority_29 = $this->priority_29($post_1, $post_2);
        if ($priority_29 && $priority_29[0] != null) {
            array_push($matches, $priority_29);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_29[$j]['id']) {
                        unset($priority_29[$j]);
                    }
                }
            }
        }
        $priority_30 = $this->priority_30($post_1, $post_2);
        if ($priority_30 && $priority_30[0] != null) {
            array_push($matches, $priority_30);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_30[$j]['id']) {
                        unset($priority_30[$j]);
                    }
                }
            }
        }
        $priority_31 = $this->priority_31($post_1, $post_2);
        if ($priority_31 && $priority_31[0] != null) {
            array_push($matches, $priority_31);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_31[$j]['id']) {
                        unset($priority_31[$j]);
                    }
                }
            }
        }
        $priority_32 = $this->priority_32($post_1, $post_2);
        if ($priority_32 && $priority_32[0] != null) {
            array_push($matches, $priority_32);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_32[$j]['id']) {
                        unset($priority_32[$j]);
                    }
                }
            }
        }
        $priority_33 = $this->priority_33($post_1, $post_2);
        if ($priority_33 && $priority_33[0] != null) {
            array_push($matches, $priority_33);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_33[$j]['id']) {
                        unset($priority_33[$j]);
                    }
                }
            }
        }
        $priority_34 = $this->priority_34($post_1, $post_2);
        if ($priority_34 && $priority_34[0] != null) {
            array_push($matches, $priority_34);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_34[$j]['id']) {
                        unset($priority_34[$j]);
                    }
                }
            }
        }
        $priority_35 = $this->priority_35($post_1, $post_2);
        if ($priority_35 && $priority_35[0] != null) {
            array_push($matches, $priority_35);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_35[$j]['id']) {
                        unset($priority_35[$j]);
                    }
                }
            }
        }
        $priority_36 = $this->priority_36($post_1, $post_2);
        if ($priority_36 && $priority_36[0] != null) {
            array_push($matches, $priority_36);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_36[$j]['id']) {
                        unset($priority_36[$j]);
                    }
                }
            }
        }
        $priority_37 = $this->priority_37($post_1, $post_2);
        if ($priority_37 && $priority_37[0] != null) {
            array_push($matches, $priority_37);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_37[$j]['id']) {
                        unset($priority_37[$j]);
                    }
                }
            }
        }
        $priority_38 = $this->priority_38($post_1, $post_2);
        if ($priority_38 && $priority_38[0] != null) {
            array_push($matches, $priority_38);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_38[$j]['id']) {
                        unset($priority_38[$j]);
                    }
                }
            }
        }
        $priority_39 = $this->priority_39($post_1, $post_2);
        if ($priority_39 && $priority_39[0] != null) {
            array_push($matches, $priority_39);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_39[$j]['id']) {
                        unset($priority_39[$j]);
                    }
                }
            }
        }
        $priority_40 = $this->priority_40($post_1, $post_2);
        if ($priority_40 && $priority_40[0] != null) {
            array_push($matches, $priority_40);
            for ($i = 0; $i < count($matches); $i++) {
                for ($j = 0; $j < count($matches[$i]); $j++) {
                    if ($matches[$i][$j]['id'] == $priority_40[$j]['id']) {
                        unset($priority_40[$j]);
                    }
                }
            }
        }




        // dd(($priority_10));
        return $matches;
    }

    function priority_1($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id == $post_1->sub_category_id && $item->sub_sub_category_id == $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id == $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id == $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();

        // dd($filteredResponce);
        return array_values($filteredResponce);
    }
    function priority_2($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id == $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id == $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id == $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_3($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id != $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id == $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id == $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_4($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id == $post_1->sub_category_id && $item->sub_sub_category_id == $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id == $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_5($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id == $post_1->sub_category_id && $item->sub_sub_category_id == $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id != $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_6($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id == $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id == $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_7($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id != $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id == $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_8($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id == $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id != $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_9($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id != $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id != $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_10($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id == $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id == $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_11($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id == $post_1->sub_category_id && $item->sub_sub_category_id == $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_12($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id == $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_13($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id == $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_14($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id != $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_15($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id != $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_16($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id != $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id == $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id == $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_17($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id != $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id == $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_18($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id != $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id != $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_19($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id == $post_1->sub_category_id && $item->sub_sub_category_id == $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id != $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_20($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id == $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id != $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_21($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id != $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id != $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_22($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id != $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id != $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location != $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_23($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id != $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id != $post_1->return_post->category_id && $item->return_post->sub_category_id != $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_24($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id != $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location != $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_25($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->return_post->category_id != $post_1->return_post->category_id && $item->return_post->sub_category_id != $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_26($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id != $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location != $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_27($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id != $post_1->category_id && $item->sub_category_id != $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_28($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id != $post_1->category_id && $item->sub_category_id != $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location != $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id == $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id == $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_29($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id != $post_1->category_id && $item->sub_category_id != $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location != $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id == $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_30($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id != $post_1->category_id && $item->sub_category_id != $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location != $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id != $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_31($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id != $post_1->category_id && $item->sub_category_id != $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location != $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id == $post_1->return_post->category_id && $item->return_post->sub_category_id != $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location != $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_32($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id != $post_1->category_id && $item->sub_category_id != $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location != $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id != $post_1->return_post->category_id && $item->return_post->sub_category_id != $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location == $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_33($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id != $post_1->category_id && $item->sub_category_id != $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location != $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id != $post_1->return_post->category_id && $item->return_post->sub_category_id != $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location != $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_34($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id == $post_1->sub_category_id && $item->sub_sub_category_id == $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id != $post_1->return_post->category_id && $item->return_post->sub_category_id != $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location != $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_35($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id == $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id != $post_1->return_post->category_id && $item->return_post->sub_category_id != $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location != $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_36($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id != $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id != $post_1->return_post->category_id && $item->return_post->sub_category_id != $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location != $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_37($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id == $post_1->category_id && $item->sub_category_id != $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location != $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id != $post_1->return_post->category_id && $item->return_post->sub_category_id != $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location != $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_38($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item->category_id != $post_1->category_id && $item->sub_category_id != $post_1->sub_category_id && $item->sub_sub_category_id != $post_1->sub_sub_category_id
                && $item->location == $post_1->location && $item->price_from <= $post_1->price && $item->price_to >= $post_1->price
                && $item->return_post->category_id != $post_1->return_post->category_id && $item->return_post->sub_category_id != $post_1->return_post->sub_category_id
                && $item->return_post->sub_sub_category_id != $post_1->return_post->sub_sub_category_id && $item->return_post->location != $post_1->return_post->location
                && $item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_39($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if ($item->return_post->price_from <= $post_1->return_post->price && $item->return_post->price_to >= $post_1->return_post->price) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
    function priority_40($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if ($item->price_from <= $post_1->price && $item->price_to >= $post_1->price) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
}
