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
        $my_returns = Item::whereUserId(11)->whereType('return')->get();
        $others_returns = Item::where('user_id', '!=', 11)->whereType('return')->get();

        $result = $this->returnCondition($my_posts, $my_returns, $others_posts, $others_returns);


        return response()->json($result);
    }

    function returnCondition($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $matches = [];

        $priority_1 = $this->priority_1($post_1, $return_post_2);
        if($priority_1[0][0] != null) {
            for ($i = 0; $i <= count($priority_1[0]); $i++) {
            array_push($matches, $priority_1[0][$i]['id']);
            }
        }


        // if ($priority_1[0][0] != null) {
        //     array_push($matches, $priority_1[0]);
        //     for ($i = 0; $i < 32; $i++) {
        //         $result = $priority_1[0][$i]['id'];
        //         // array_push($matches, $result);
        //         // array_push($matches, $priority_1[0][$i]);
        //     }
        // }
        // $matches = $priority_1[0];


        $priority_2 = $this->priority_2($post_1, $return_post_2);
        if ($priority_2[0][0] != null) {
            // for ($i = 0; $i < 31; $i++) {
                array_push($matches, $priority_2);
            // }
        }

        // dd(array_diff($priority_1, $priority_2));

        // $priority_3 = $this->priority_3($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_3[0][0] != null) array_push($matches, $priority_3);

        // $priority_4 = $this->priority_4($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_4[0][0] != null) array_push($matches, $priority_4);

        // $priority_5 = $this->priority_5($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_5[0][0] != null) array_push($matches, $priority_5);

        // $priority_6 = $this->priority_6($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_6[0][0] != null) array_push($matches, $priority_6);

        // $priority_7 = $this->priority_7($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_7[0][0] != null) array_push($matches, $priority_7);

        // $priority_8 = $this->priority_8($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_8[0][0] != null) array_push($matches, $priority_8);

        // $priority_9 = $this->priority_9($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_9[0][0] != null) array_push($matches, $priority_9);

        // $priority_10 = $this->priority_10($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_10[0][0] != null) array_push($matches, $priority_10);

        // $priority_11 = $this->priority_11($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_11[0][0] != null) array_push($matches, $priority_11);

        // $priority_12 = $this->priority_12($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_12[0][0] != null) array_push($matches, $priority_12);

        // $priority_13 = $this->priority_13($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_13[0][0] != null) array_push($matches, $priority_13);

        // $priority_14 = $this->priority_14($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_14[0][0] != null) array_push($matches, $priority_14);

        // $priority_15 = $this->priority_15($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_15[0][0] != null) array_push($matches, $priority_15);

        // $priority_16 = $this->priority_16($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_16[0][0] != null) array_push($matches, $priority_16);

        // $priority_17 = $this->priority_17($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_17[0][0] != null) array_push($matches, $priority_17);

        // $priority_18 = $this->priority_18($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_18[0][0] != null) array_push($matches, $priority_18);

        // $priority_19 = $this->priority_19($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_19[0][0] != null) array_push($matches, $priority_19);

        // $priority_20 = $this->priority_20($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_20[0][0] != null) array_push($matches, $priority_20);

        // $priority_21 = $this->priority_21($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_21[0][0] != null) array_push($matches, $priority_21);

        // $priority_22 = $this->priority_22($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_22[0][0] != null) array_push($matches, $priority_22);

        // $priority_23 = $this->priority_23($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_23[0][0] != null) array_push($matches, $priority_23);

        // $priority_24 = $this->priority_24($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_24[0][0] != null) array_push($matches, $priority_24);

        // $priority_25 = $this->priority_25($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_25[0][0] != null) array_push($matches, $priority_25);

        // $priority_26 = $this->priority_26($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_26[0][0] != null) array_push($matches, $priority_26);

        // $priority_27 = $this->priority_27($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_27[0][0] != null) array_push($matches, $priority_27);

        // $priority_28 = $this->priority_28($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_28[0][0] != null) array_push($matches, $priority_28);

        // $priority_29 = $this->priority_29($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_29[0][0] != null) array_push($matches, $priority_29);

        // $priority_30 = $this->priority_30($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_30[0][0] != null) array_push($matches, $priority_30);

        // $priority_31 = $this->priority_31($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_31[0][0] != null) array_push($matches, $priority_31);

        // $priority_32 = $this->priority_32($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_32[0][0] != null) array_push($matches, $priority_32);

        // $priority_33 = $this->priority_33($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_33[0][0] != null) array_push($matches, $priority_33);

        // $priority_34 = $this->priority_34($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_34[0][0] != null) array_push($matches, $priority_34);

        // $priority_35 = $this->priority_35($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_35[0][0] != null) array_push($matches, $priority_35);

        // $priority_36 = $this->priority_36($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_36[0][0] != null) array_push($matches, $priority_36);

        // $priority_37 = $this->priority_37($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_37[0][0] != null) array_push($matches, $priority_37);

        // $priority_38 = $this->priority_38($post_1, $return_post_1, $post_2, $return_post_2);
        // if ($priority_38[0][0] != null) array_push($matches, $priority_38);

        return $matches;
    }

    function priority_1($post_1, $post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1) {
            return $post['return_id'] == $post_1[0]['category_id'] && $post['category_id'] == $post_1[0]['return_id']
                && $post['sub_category_id'] == $post_1[0]['sub_category_id'] && $post['sub_sub_category_id'] == $post_1[0]['sub_sub_category_id']
                && $post['location_id'] == $post_1[0]['location_id'] && $post['price_from'] <= $post_1[0]['price'] && $post['price_to'] >= $post_1[0]['price'];
        });
        array_push($col, $collection);
        // dd($post_1->id);
        return $col;
    }

    function priority_2($post_1, $post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1) {
            return $post['return_id'] == $post_1[0]['category_id'] && $post['category_id'] == $post_1[0]['return_id']
                && $post['sub_category_id'] == $post_1[0]['sub_category_id'] && $post['location_id'] == $post_1[0]['location_id']
                && $post['price_from'] <= $post_1[0]['price'] && $post['price_to'] >= $post_1[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    // function priority_2($post_1, $return_post_1, $post_2, $return_post_2)
    // {
    //     $col = [];
    //     $collection = collect($post_2)->filter(function ($post) use ($post_1) {
    //         return $post['return_id'] == $return_post_1[0]['category_id']  && $post['sub_category_id'] == $return_post_1[0]['sub_category_id']
    //             && $post['location_id'] == $return_post_1[0]['location_id']
    //             && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
    //             && $post_1['return_id'] == $return_post_2[0]['category_id'] && $post_1['sub_category_id'] == $return_post_2[0]['sub_category_id']
    //             && $post_1['sub_sub_category_id'] == $return_post_2[0]['sub_sub_category_id'] && $post_1['location_id'] == $return_post_2[0]['location_id']
    //             && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
    //     });
    //     array_push($col, $collection);
    //     return $col;
    // }

    function priority_3($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id']
                && $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['return_id'] == $return_post_2[0]['category_id'] && $post_1['sub_category_id'] == $return_post_2[0]['sub_category_id']
                && $post_1['sub_sub_category_id'] == $return_post_2[0]['sub_sub_category_id'] && $post_1['location_id'] == $return_post_2[0]['location_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_4($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id'] && $post['sub_category_id'] == $return_post_1[0]['sub_category_id']
                && $post['sub_sub_category_id'] == $return_post_1[0]['sub_sub_category_id'] && $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['return_id'] == $return_post_2[0]['category_id'] && $post_1['sub_category_id'] == $return_post_2[0]['sub_category_id']
                && $post_1['location_id'] == $return_post_2[0]['location_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_5($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id'] && $post['sub_category_id'] == $return_post_1[0]['sub_category_id']
                && $post['sub_sub_category_id'] == $return_post_1[0]['sub_sub_category_id'] && $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['return_id'] == $return_post_2[0]['category_id']
                && $post_1['location_id'] == $return_post_2[0]['location_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_6($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id'] && $post['sub_category_id'] == $return_post_1[0]['sub_category_id']
                && $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['return_id'] == $return_post_2[0]['category_id'] && $post_1['sub_category_id'] == $return_post_2[0]['sub_category_id']
                && $post_1['location_id'] == $return_post_2[0]['location_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_7($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id']
                && $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['return_id'] == $return_post_2[0]['category_id'] && $post_1['sub_category_id'] == $return_post_2[0]['sub_category_id']
                && $post_1['location_id'] == $return_post_2[0]['location_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_8($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id'] && $post['sub_category_id'] == $return_post_1[0]['sub_category_id']
                && $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['return_id'] == $return_post_2[0]['category_id']
                && $post_1['location_id'] == $return_post_2[0]['location_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_9($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id']
                && $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['return_id'] == $return_post_2[0]['category_id']
                && $post_1['location_id'] == $return_post_2[0]['location_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_10($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post_1['sub_category_id'] == $return_post_2[0]['sub_category_id']
                && $post_1['sub_sub_category_id'] == $return_post_2[0]['sub_sub_category_id'] && $post_1['location_id'] == $return_post_2[0]['location_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_11($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id'] && $post['sub_category_id'] == $return_post_1[0]['sub_category_id']
                && $post['sub_sub_category_id'] == $return_post_1[0]['sub_sub_category_id'] && $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_12($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post_1['return_id'] == $return_post_2[0]['category_id'] && $post_1['sub_category_id'] == $return_post_2[0]['sub_category_id']
                && $post_1['location_id'] == $return_post_2[0]['location_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_13($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id'] && $post['sub_category_id'] == $return_post_1[0]['sub_category_id']
                && $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_14($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post_1['return_id'] == $return_post_2[0]['category_id'] && $post_1['location_id'] == $return_post_2[0]['location_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_15($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id'] && $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_16($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id']
                && $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['return_id'] == $return_post_2[0]['category_id'] && $post_1['sub_category_id'] == $return_post_2[0]['sub_category_id']
                && $post_1['sub_sub_category_id'] == $return_post_2[0]['sub_sub_category_id'] && $post_1['location_id'] == $return_post_2[0]['location_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_17($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id']
                && $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['return_id'] == $return_post_2[0]['category_id'] && $post_1['sub_category_id'] == $return_post_2[0]['sub_category_id']
                && $post_1['location_id'] == $return_post_2[0]['location_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_18($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id']
                && $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['return_id'] == $return_post_2[0]['category_id']
                && $post_1['location_id'] == $return_post_2[0]['location_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_19($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id'] && $post['sub_category_id'] == $return_post_1[0]['sub_category_id']
                && $post['sub_sub_category_id'] == $return_post_1[0]['sub_sub_category_id'] && $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['return_id'] == $return_post_2[0]['category_id'] && $post_1['location_id'] == $return_post_2[0]['location_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_20($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id'] && $post['sub_category_id'] == $return_post_1[0]['sub_category_id']
                && $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['return_id'] == $return_post_2[0]['category_id'] && $post_1['location_id'] == $return_post_2[0]['location_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_21($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id'] && $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['return_id'] == $return_post_2[0]['category_id'] && $post_1['location_id'] == $return_post_2[0]['location_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_22($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id'] && $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['return_id'] == $return_post_2[0]['category_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_23($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id'] && $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['location_id'] == $return_post_2[0]['location_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_24($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post_1['return_id'] == $return_post_2[0]['category_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_25($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post_1['location_id'] == $return_post_2[0]['location_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_26($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_27($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_28($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['return_id'] == $return_post_2[0]['category_id'] && $post_1['sub_category_id'] == $return_post_2[0]['sub_category_id']
                && $post_1['sub_sub_category_id'] == $return_post_2[0]['sub_sub_category_id'] && $post_1['location_id'] == $return_post_2[0]['location_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_29($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['return_id'] == $return_post_2[0]['category_id'] && $post_1['sub_category_id'] == $return_post_2[0]['sub_category_id']
                && $post_1['location_id'] == $return_post_2[0]['location_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_30($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['return_id'] == $return_post_2[0]['category_id']
                && $post_1['location_id'] == $return_post_2[0]['location_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_31($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['return_id'] == $return_post_2[0]['category_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_32($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['location_id'] == $return_post_2[0]['location_id']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_33($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_34($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id'] && $post['sub_category_id'] == $return_post_1[0]['sub_category_id']
                && $post['sub_sub_category_id'] == $return_post_1[0]['sub_sub_category_id'] && $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_35($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id'] && $post['sub_category_id'] == $return_post_1[0]['sub_category_id']
                && $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_36($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id'] && $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_37($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['return_id'] == $return_post_1[0]['category_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }

    function priority_38($post_1, $return_post_1, $post_2, $return_post_2)
    {
        $col = [];
        $collection = collect($post_2)->filter(function ($post) use ($post_1, $return_post_1, $return_post_2) {
            return $post['location_id'] == $return_post_1[0]['location_id']
                && $post['price_from'] <= $return_post_1[0]['price'] && $post['price_to'] >= $return_post_1[0]['price']
                && $post_1['price_from'] <= $return_post_2[0]['price'] && $post_1['price_to'] >= $return_post_2[0]['price'];
        });
        array_push($col, $collection);
        return $col;
    }
}
