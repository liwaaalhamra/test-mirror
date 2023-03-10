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
    public function index($user_id)
    {
        $my_posts = Item::whereUserId($user_id)->whereType('offer')->get();
        $others_posts = Item::where('user_id', '!=', $user_id)->whereType('offer')->get();
        foreach ($my_posts as $my_post) {
            $result = $this->returnResult($my_post, $others_posts);
        }
        if (!$result) {
            return "No matching records";
        }
        return $result;
    }

    function returnResult($post_1, $post_2)
    {
        $matches = [];
        $priority_1 = $this->priority_1($post_1, $post_2);
        if (!empty($priority_1[0])) {
            $matches[$priority_1[0]["id"]] = $priority_1;
        }
        $priority_2 = $this->priority_2($post_1, $post_2);
        // dd($priority_2);
        // if(empty($priority_2[0])){
        //     dd(1);
        // }else{
        //     dd(2);
        // }
        if (!empty($priority_2[0])) {
            for($i=0;$i<count($priority_2);$i++) {
                if( !array_key_exists($priority_2[$i]['id'], $priority_1[0]['id'])) {
                    $matches[$priority_2[0]["id"]] = $priority_2;
                }
            }
        }
        $priority_3 = $this->priority_3($post_1, $post_2);
        if (!empty($priority_3[0])) {
            for($i=0;$i<count($priority_3);$i++) {
                if( !array_key_exists($priority_3[$i]['id'], $priority_2[0]['id'])) {
                    $matches[$priority_3[0]["id"]] = $priority_3;
                }
            }
        }
        $priority_4 = $this->priority_4($post_1, $post_2);
        if (!empty($priority_4[0])) {
            for($i=0;$i<count($priority_4);$i++) {
                if( !array_key_exists($priority_4[$i]['id'], $priority_3[0]['id'])) {
                    $matches[$priority_4[0]["id"]] = $priority_4;
                }
            }
        }
        $priority_5 = $this->priority_5($post_1, $post_2);
        if (!empty($priority_5[0])) {
            for($i=0;$i<count($priority_5);$i++) {
                if( !array_key_exists($priority_5[$i]['id'], $priority_4[0]['id'])) {
                    $matches[$priority_5[0]["id"]] = $priority_5;
                }
            }
        }
        $priority_6 = $this->priority_6($post_1, $post_2);
        if (!empty($priority_6[0])) {
            for($i=0;$i<count($priority_6);$i++) {
                if( !array_key_exists($priority_6[$i]['id'], $priority_5[0]['id'])) {
                    $matches[$priority_6[0]["id"]] = $priority_6;
                }
            }
        }
        $priority_7 = $this->priority_7($post_1, $post_2);
        if (!empty($priority_7[0])) {
            for($i=0;$i<count($priority_7);$i++) {
                if( !array_key_exists($priority_7[$i]['id'], $priority_6[0]['id'])) {
                    $matches[$priority_7[0]["id"]] = $priority_7;
                }
            }
        }
        $priority_8 = $this->priority_8($post_1, $post_2);
        if (!empty($priority_8[0])) {
            for($i=0;$i<count($priority_8);$i++) {
                if( !array_key_exists($priority_8[$i]['id'], $priority_7[0]['id'])) {
                    $matches[$priority_8[0]["id"]] = $priority_8;
                }
            }
        }
        $priority_9 = $this->priority_9($post_1, $post_2);
        if (!empty($priority_9[0])) {
            for($i=0;$i<count($priority_9);$i++) {
                if( !array_key_exists($priority_9[$i]['id'], $priority_8[0]['id'])) {
                    $matches[$priority_9[0]["id"]] = $priority_9;
                }
            }
        }
        $priority_10 = $this->priority_10($post_1, $post_2);
        if (!empty($priority_10[0])) {
            for($i=0;$i<count($priority_10);$i++) {
                if( !array_key_exists($priority_10[$i]['id'], $priority_9[0]['id'])) {
                    $matches[$priority_10[0]["id"]] = $priority_10;
                }
            }
        }
        $priority_11 = $this->priority_11($post_1, $post_2);
        if (!empty($priority_11[0])) {
            for($i=0;$i<count($priority_11);$i++) {
                if( !array_key_exists($priority_11[$i]['id'], $priority_10[0]['id'])) {
                    $matches[$priority_11[0]["id"]] = $priority_11;
                }
            }
        }
        $priority_12 = $this->priority_12($post_1, $post_2);
        if (!empty($priority_12[0])) {
            for($i=0;$i<count($priority_12);$i++) {
                if( !array_key_exists($priority_12[$i]['id'], $priority_11[0]['id'])) {
                    $matches[$priority_12[0]["id"]] = $priority_12;
                }
            }
        }
        $priority_13 = $this->priority_13($post_1, $post_2);
        if (!empty($priority_13[0])) {
            for($i=0;$i<count($priority_13);$i++) {
                if( !array_key_exists($priority_13[$i]['id'], $priority_12[0]['id'])) {
                    $matches[$priority_13[0]["id"]] = $priority_13;
                }
            }
        }
        $priority_14 = $this->priority_14($post_1, $post_2);
        if (!empty($priority_14[0])) {
            for($i=0;$i<count($priority_14);$i++) {
                if( !array_key_exists($priority_14[$i]['id'], $priority_13[0]['id'])) {
                    $matches[$priority_14[0]["id"]] = $priority_14;
                }
            }
        }
        $priority_15 = $this->priority_15($post_1, $post_2);
        if (!empty($priority_15[0])) {
            for($i=0;$i<count($priority_15);$i++) {
                if( !array_key_exists($priority_15[$i]['id'], $priority_14[0]['id'])) {
                    $matches[$priority_15[0]["id"]] = $priority_15;
                }
            }
        }
        $priority_16 = $this->priority_16($post_1, $post_2);
        if (!empty($priority_16[0])) {
            for($i=0;$i<count($priority_16);$i++) {
                if( !array_key_exists($priority_16[$i]['id'], $priority_15[0]['id'])) {
                    $matches[$priority_16[0]["id"]] = $priority_16;
                }
            }
        }
        $priority_17 = $this->priority_17($post_1, $post_2);
        if (!empty($priority_17[0])) {
            for($i=0;$i<count($priority_17);$i++) {
                if( !array_key_exists($priority_17[$i]['id'], $priority_16[0]['id'])) {
                    $matches[$priority_17[0]["id"]] = $priority_17;
                }
            }
        }
        $priority_18 = $this->priority_18($post_1, $post_2);
        if (!empty($priority_18[0])) {
            for($i=0;$i<count($priority_18);$i++) {
                if( !array_key_exists($priority_18[$i]['id'], $priority_17[0]['id'])) {
                    $matches[$priority_18[0]["id"]] = $priority_18;
                }
            }
        }
        $priority_19 = $this->priority_19($post_1, $post_2);
        if (!empty($priority_19[0])) {
            for($i=0;$i<count($priority_19);$i++) {
                if( !array_key_exists($priority_19[$i]['id'], $priority_18[0]['id'])) {
                    $matches[$priority_19[0]["id"]] = $priority_19;
                }
            }
        }
        $priority_20 = $this->priority_20($post_1, $post_2);
        if (!empty($priority_20[0])) {
            for($i=0;$i<count($priority_20);$i++) {
                if( !array_key_exists($priority_20[$i]['id'], $priority_19[0]['id'])) {
                    $matches[$priority_20[0]["id"]] = $priority_20;
                }
            }
        }
        $priority_21 = $this->priority_21($post_1, $post_2);
        if (!empty($priority_21[0])) {
            for($i=0;$i<count($priority_21);$i++) {
                if( !array_key_exists($priority_21[$i]['id'], $priority_20[0]['id'])) {
                    $matches[$priority_21[0]["id"]] = $priority_21;
                }
            }
        }
        $priority_22 = $this->priority_22($post_1, $post_2);
        if (!empty($priority_22[0])) {
            for($i=0;$i<count($priority_22);$i++) {
                if( !array_key_exists($priority_22[$i]['id'], $priority_21[0]['id'])) {
                    $matches[$priority_22[0]["id"]] = $priority_22;
                }
            }
        }
        $priority_23 = $this->priority_23($post_1, $post_2);
        if (!empty($priority_23[0])) {
            for($i=0;$i<count($priority_23);$i++) {
                if( !array_key_exists($priority_23[$i]['id'], $priority_22[0]['id'])) {
                    $matches[$priority_23[0]["id"]] = $priority_23;
                }
            }
        }
        $priority_24 = $this->priority_24($post_1, $post_2);
        if (!empty($priority_24[0])) {
            for($i=0;$i<count($priority_24);$i++) {
                if( !array_key_exists($priority_24[$i]['id'], $priority_23[0]['id'])) {
                    $matches[$priority_24[0]["id"]] = $priority_24;
                }
            }
        }
        $priority_25 = $this->priority_25($post_1, $post_2);
        if (!empty($priority_25[0])) {
            for($i=0;$i<count($priority_25);$i++) {
                if( !array_key_exists($priority_25[$i]['id'], $priority_24[0]['id'])) {
                    $matches[$priority_25[0]["id"]] = $priority_25;
                }
            }
        }
        $priority_26 = $this->priority_26($post_1, $post_2);
        if (!empty($priority_26[0])) {
            for($i=0;$i<count($priority_26);$i++) {
                if( !array_key_exists($priority_26[$i]['id'], $priority_25[0]['id'])) {
                    $matches[$priority_26[0]["id"]] = $priority_26;
                }
            }
        }
        $priority_27 = $this->priority_27($post_1, $post_2);
        if (!empty($priority_27[0])) {
            for($i=0;$i<count($priority_27);$i++) {
                if( !array_key_exists($priority_27[$i]['id'], $priority_26[0]['id'])) {
                    $matches[$priority_27[0]["id"]] = $priority_27;
                }
            }
        }
        $priority_28 = $this->priority_28($post_1, $post_2);
        if (!empty($priority_28[0])) {
            for($i=0;$i<count($priority_28);$i++) {
                if( !array_key_exists($priority_28[$i]['id'], $priority_27[0]['id'])) {
                    $matches[$priority_28[0]["id"]] = $priority_28;
                }
            }
        }
        $priority_29 = $this->priority_29($post_1, $post_2);
        if (!empty($priority_29[0])) {
            for($i=0;$i<count($priority_29);$i++) {
                if( !array_key_exists($priority_29[$i]['id'], $priority_28[0]['id'])) {
                    $matches[$priority_29[0]["id"]] = $priority_29;
                }
            }
        }
        $priority_30 = $this->priority_30($post_1, $post_2);
        if (!empty($priority_30[0])) {
            for($i=0;$i<count($priority_30);$i++) {
                if( !array_key_exists($priority_30[$i]['id'], $priority_29[0]['id'])) {
                    $matches[$priority_30[0]["id"]] = $priority_30;
                }
            }
        }
        $priority_31 = $this->priority_31($post_1, $post_2);
        if (!empty($priority_31[0])) {
            for($i=0;$i<count($priority_31);$i++) {
                if( !array_key_exists($priority_31[$i]['id'], $priority_30[0]['id'])) {
                    $matches[$priority_31[0]["id"]] = $priority_31;
                }
            }
        }
        $priority_32 = $this->priority_32($post_1, $post_2);
        if (!empty($priority_32[0])) {
            for($i=0;$i<count($priority_32);$i++) {
                if( !array_key_exists($priority_32[$i]['id'], $priority_31[0]['id'])) {
                    $matches[$priority_32[0]["id"]] = $priority_32;
                }
            }
        }
        $priority_33 = $this->priority_33($post_1, $post_2);
        if (!empty($priority_33[0])) {
            for($i=0;$i<count($priority_33);$i++) {
                if( !array_key_exists($priority_33[$i]['id'], $priority_32[0]['id'])) {
                    $matches[$priority_33[0]["id"]] = $priority_33;
                }
            }
        }
        $priority_34 = $this->priority_34($post_1, $post_2);
        if (!empty($priority_34[0])) {
            for($i=0;$i<count($priority_34);$i++) {
                if( !array_key_exists($priority_34[$i]['id'], $priority_33[0]['id'])) {
                    $matches[$priority_34[0]["id"]] = $priority_34;
                }
            }
        }
        $priority_35 = $this->priority_35($post_1, $post_2);
        if (!empty($priority_35[0])) {
            for($i=0;$i<count($priority_35);$i++) {
                if( !array_key_exists($priority_35[$i]['id'], $priority_34[0]['id'])) {
                    $matches[$priority_35[0]["id"]] = $priority_35;
                }
            }
        }
        $priority_36 = $this->priority_36($post_1, $post_2);
        if (!empty($priority_36[0])) {
            for($i=0;$i<count($priority_36);$i++) {
                if( !array_key_exists($priority_36[$i]['id'], $priority_35[0]['id'])) {
                    $matches[$priority_36[0]["id"]] = $priority_36;
                }
            }
        }
        $priority_37 = $this->priority_37($post_1, $post_2);
        if (!empty($priority_37[0])) {
            for($i=0;$i<count($priority_37);$i++) {
                if( !array_key_exists($priority_37[$i]['id'], $priority_36[0]['id'])) {
                    $matches[$priority_37[0]["id"]] = $priority_37;
                }
            }
        }
        $priority_38 = $this->priority_38($post_1, $post_2);
        if (!empty($priority_38[0])) {
            for($i=0;$i<count($priority_38);$i++) {
                if( !array_key_exists($priority_38[$i]['id'], $priority_37[0]['id'])) {
                    $matches[$priority_38[0]["id"]] = $priority_38;
                }
            }
        }
        $priority_39 = $this->priority_39($post_1, $post_2);
        if (!empty($priority_39[0])) {
            for($i=0;$i<count($priority_39);$i++) {
                if( !array_key_exists($priority_39[$i]['id'], $priority_38[0]['id'])) {
                    $matches[$priority_39[0]["id"]] = $priority_39;
                }
            }
        }
        $priority_40 = $this->priority_40($post_1, $post_2);
        if (!empty($priority_40[0])) {
            for($i=0;$i<count($priority_40);$i++) {
                if( !array_key_exists($priority_40[$i]['id'], $priority_39[0]['id'])) {
                    $matches[$priority_40[0]["id"]] = $priority_40;
                }
            }
        }
        return $matches;
    }

    function priority_1($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] == $post_1['sub_category_id'] && $item['sub_sub_category_id'] == $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] == $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] == $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
            ) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        })->toArray();
       return array_values($filteredResponce);
    }
    function priority_2($post_1, $post_2)
    {
        $responce = $post_2->map(function ($item) use ($post_1) {
            if (
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] == $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] == $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] == $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] != $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] == $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] == $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] == $post_1['sub_category_id'] && $item['sub_sub_category_id'] == $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] == $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] == $post_1['sub_category_id'] && $item['sub_sub_category_id'] == $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] == $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] == $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] != $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] == $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] == $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] != $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
            if ($item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] == $post_1->return_post['sub_category_id']
            && $item->return_post['sub_sub_category_id'] == $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
            && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']) {
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] == $post_1['sub_category_id'] && $item['sub_sub_category_id'] == $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
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
                $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] == $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] == $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
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
                $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] != $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] != $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] == $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] == $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] != $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] == $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] != $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] == $post_1['sub_category_id'] && $item['sub_sub_category_id'] == $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] == $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] != $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] != $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] != $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] != $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] != $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] != $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item->return_post['category_id'] != $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] != $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] != $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
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
                $item['category_id'] != $post_1['category_id'] && $item['sub_category_id'] != $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
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
                $item['category_id'] != $post_1['category_id'] && $item['sub_category_id'] != $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] != $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] == $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] == $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] != $post_1['category_id'] && $item['sub_category_id'] != $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] != $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] == $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] != $post_1['category_id'] && $item['sub_category_id'] != $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] != $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] != $post_1['category_id'] && $item['sub_category_id'] != $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] != $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] == $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] != $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] != $post_1['category_id'] && $item['sub_category_id'] != $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] != $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] != $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] == $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] != $post_1['category_id'] && $item['sub_category_id'] != $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] != $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] != $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] != $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] == $post_1['sub_category_id'] && $item['sub_sub_category_id'] == $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] != $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] != $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] == $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] != $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] != $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] != $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] != $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] != $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] == $post_1['category_id'] && $item['sub_category_id'] != $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] != $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] != $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] != $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
                $item['category_id'] != $post_1['category_id'] && $item['sub_category_id'] != $post_1['sub_category_id'] && $item['sub_sub_category_id'] != $post_1['sub_sub_category_id']
                && $item['location'] == $post_1['location'] && $item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']
                && $item->return_post['category_id'] != $post_1->return_post['category_id'] && $item->return_post['sub_category_id'] != $post_1->return_post['sub_category_id']
                && $item->return_post['sub_sub_category_id'] != $post_1->return_post['sub_sub_category_id'] && $item->return_post['location'] != $post_1->return_post['location']
                && $item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']
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
            if ($item->return_post['price_from'] <= $post_1->return_post['price'] && $item->return_post['price_to'] >= $post_1->return_post['price']) {
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
            if ($item['price_from'] <= $post_1['price'] && $item['price_to'] >= $post_1['price']) {
                return $item;
            }
        });
        $filteredResponce = $responce->filter(function ($value) {
            return !is_null($value);
        });
        return $filteredResponce;
    }
}
