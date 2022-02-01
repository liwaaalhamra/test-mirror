<?php
//prefixed by 'api'

/**
 * Push Routes
 */
Route::group(['prefix' => 'push'],function () {
    Route::post('/set_user_push', ['uses' => 'PushController@set_user_push', 'as' => 'api.push.setToken']);
    Route::post('/inbox', ['uses' => 'PushController@inbox', 'as' => 'api.push.inbox']);
    Route::get('/unread_count', ['uses' => 'PushController@unread_count', 'as' => 'api.push.unread_count']);
});

/**
 * Cart Routes
 */
Route::group(['prefix' => 'cart'],function () {
    Route::get('count', 'OrderController@count');
    Route::get('get', 'OrderController@get');

    Route::get('history', 'OrderController@history');
    Route::post('remove', 'OrderController@delete_selected_items');
    Route::post('remove_single', 'OrderController@delete_item');
    Route::post('add', 'OrderController@add_item');
    Route::post('edit', 'OrderController@edit_item');
    Route::get('clear', 'OrderController@clear');
    Route::post('total_price', 'OrderController@total_price');
    Route::post('book', 'OrderController@book');
    Route::post('reorder', 'OrderController@reorder');
    Route::post('apply_coupon_code', 'OrderController@apply_coupon_code');
    Route::get('remove_coupon_code', 'OrderController@remove_coupon_code');
});

/**
 * Supplier Cart Routes
 */
Route::group(['prefix' => 'supplier/cart'],function () {
    Route::get('count', 'SupplierOrderController@count');
    Route::get('get', 'SupplierOrderController@get');

    Route::get('history', 'SupplierOrderController@history');
    Route::post('remove', 'SupplierOrderController@delete_selected_items');
    Route::post('remove_single', 'SupplierOrderController@delete_item');
    Route::post('add', 'SupplierOrderController@add_item');
    Route::post('edit', 'SupplierOrderController@edit_item');
    Route::get('clear', 'SupplierOrderController@clear');
    Route::post('total_price', 'SupplierOrderController@total_price');
    Route::post('book', 'SupplierOrderController@book');
    Route::post('reorder', 'SupplierOrderController@reorder');
    Route::post('apply_coupon_code', 'SupplierOrderController@apply_coupon_code');
    Route::get('remove_coupon_code', 'SupplierOrderController@remove_coupon_code');
});

/**
 * Restaurants Routes
 */
Route::group(['prefix' => 'restaurants'],function () {
    Route::post('/login', ['uses' => 'RestaurantsController@login']);
    Route::get('/profile', ['uses' => 'RestaurantsController@get']);
    Route::get('/logout', ['uses' => 'RestaurantsController@logout']);
    Route::get('/reset_orders', ['uses' => 'RestaurantsController@reset_orders']);
    Route::post('/status', ['uses' => 'RestaurantsController@set_status']);
    Route::post('/set_merchant_push', ['uses' => 'RestaurantsController@set_merchant_push']);
    Route::post('/all_requests', ['uses' => 'RestaurantsController@all_requests']);
    Route::post('/set_order_status', ['uses' => 'RestaurantsController@set_order_status']);
    Route::post('/order_details', ['uses' => 'RestaurantsController@order_details']);
    Route::post('/set_reservation_status', ['uses' => 'RestaurantsController@set_reservation_status']);

    Route::post('menu_categories', 'ItemsController@restaurant_menu_categories');
    Route::post('', 'ItemsController@restaurants');
    Route::post('pagination', 'ItemsController@restaurants_with_pagination');
    Route::get('sizes', 'ItemsController@sizes');
    Route::post('gifts', 'ItemsController@gifts');
    Route::post('single', 'ItemsController@single_restaurant');
    Route::post('reserve_table', 'ItemsController@reserve_table');
    Route::post('review', 'ItemsController@review');
    Route::post('review/action', 'ItemsController@review_action');
    /**
     * Favorites Routes
     */
    Route::group(['prefix' => 'favorites'],function () {
        Route::post('/', 'ItemsController@favorites');
        Route::post('/action', 'ItemsController@favorites_action');
    });
});

Route::post('explore_reviews', 'ItemsController@explore_reviews');
Route::get('order_status_list', 'OrderController@order_status_list');


/**
 * Supplier Routes
 */
Route::group(['prefix' => 'supplier'],function () {
    Route::post('/categories', 'ItemsController@supplier_categories');
    Route::post('/food_groups', 'ItemsController@supplier_food_groups');
    Route::post('/items', 'ItemsController@supplier_items');
    Route::post('/items/single', 'ItemsController@supplier_single_item');
    Route::post('/items/pagination', 'ItemsController@supplier_items_with_pagination');
    Route::post('menu_categories', 'ItemsController@supplier_menu_categories');

    Route::post('/login', ['uses' => 'SupplierController@login']);
    Route::get('/profile', ['uses' => 'SupplierController@get']);
    Route::get('/reset_orders', ['uses' => 'SupplierController@reset_orders']);
    Route::get('/logout', ['uses' => 'SupplierController@logout']);
    Route::post('/status', ['uses' => 'SupplierController@set_status']);
    Route::post('/set_merchant_push', ['uses' => 'SupplierController@set_merchant_push']);
    Route::post('/all_requests', ['uses' => 'SupplierController@all_requests']);
    Route::post('/order_details', ['uses' => 'SupplierController@order_details']);
    Route::post('/set_request_status', ['uses' => 'SupplierController@set_request_status']);
});

/**
 * Items Routes
 */
Route::group(['prefix' => 'products/items'],function () {
    Route::post('', 'ItemsController@product_items');
    Route::post('pagination', 'ItemsController@product_items_with_pagination');
    Route::post('single', 'ItemsController@product_single_item');

    /**
     * Favorites Routes
     */
    Route::group(['prefix' => 'favorites'],function () {
        Route::post('/', 'ItemsController@favorites');
        Route::post('/action', 'ItemsController@favorites_action');
    });
});
Route::group(['prefix' => 'services/items'],function () {
    Route::post('', 'ItemsController@service_items');
    Route::post('pagination', 'ItemsController@service_items_with_pagination');
    Route::post('single', 'ItemsController@service_single_item');

    /**
     * Favorites Routes
     */
    Route::group(['prefix' => 'favorites'],function () {
        Route::post('/', 'ItemsController@favorites');
        Route::post('/action', 'ItemsController@favorites_action');
    });
});

/**
 * Chat Routes
 */
Route::group(['prefix' => 'chat'],function () {
    Route::get('/friends', 'ChatController@friends');
    Route::get('/conversations', 'ChatController@conversations');
    Route::post('/messages', 'ChatController@messages');
    Route::post('/send_message', 'ChatController@send');
});

/**
 * Categories Routes
 */

Route::post('/products', 'ItemsController@products');
Route::post('/services', 'ItemsController@services');
Route::post('/category_subs', 'ItemsController@category_subs');
Route::post('/filters', 'ItemsController@filters');
Route::post('/custom_fields', 'ItemsController@custom_fields');
Route::get('/home_top_categories', 'ItemsController@home_top_categories');
Route::get('/home_top_categories/{slug}/items', 'ItemsController@home_top_categories_items');

Route::get('/conditions', 'ItemsController@conditions');
Route::get('/fuel_types', 'ItemsController@fuel_types');
Route::get('/body_types', 'ItemsController@body_types');
Route::get('/transmission_types', 'ItemsController@transmission_types');
Route::get('/door_types', 'ItemsController@door_types');
Route::get('/colors', 'ItemsController@colors');
Route::get('/rent_periods', 'ItemsController@rent_periods');

/**
 * Location Routes
 */
Route::group(['prefix' => 'locations'],function () {
    Route::post('add', 'LocationController@add');
    Route::post('edit', 'LocationController@edit');
    Route::post('delete', 'LocationController@delete');
    Route::post('set_as_default', 'LocationController@set_as_default');
    Route::get('', 'LocationController@get');
    Route::post('regions', 'LocationController@regions');
});

/**
 * Cron Routes
 */
Route::get('cron/retrieve_orders', 'CronController@retrieve_orders');
Route::get('cron/update_dashboard', 'CronController@update_dashboard');
Route::get('cron/check_expired_items', 'CronController@check_expired_items');

/**
 * Tips & More Routes
 */
Route::get('tips', 'TipsController@index');

/**
 * Payment Routes
 */
Route::get('success_response', 'PaymentController@success_response');
Route::get('error_response', 'PaymentController@error_response');
Route::get('payment_methods/', 'PaymentController@payment_methods');
Route::get('payment_response/{order_id}', 'PaymentController@payment_response');
Route::get('payment_success_response', 'PaymentController@payment_success_response');
Route::get('payment_error_response', 'PaymentController@payment_error_response');
Route::get('order/check_purchased', 'PaymentController@check_result');
Route::post('order/items', 'OrderController@order_cart_items');
Route::post('supplier/order/items', 'SupplierOrderController@order_cart_items');
Route::post('pay', 'PaymentController@pay');
Route::post('kuwait_payment', 'PaymentController@show_myfatoorah_pg');
Route::post('myfatoorah_get_payment_methods', 'PaymentController@myfatoorah_get_payment_methods');


/**
 * Media Routes
 */
Route::group(['prefix' => 'media'],function () {
    Route::post('/upload', ['uses' => 'MediaController@upload']);
    Route::post('/delete', ['uses' => 'MediaController@delete']);
});

/**
 * Rating Route
 */
Route::post('rating', 'RatingController@index');

/**
 * Login Routes
 */
Route::post('register', 'LoginController@register');
Route::post('resend_code', 'LoginController@resend_code');
Route::post('login', 'LoginController@login_password');
Route::post('login_email', 'LoginController@login_email');
Route::post('login_mobile', 'LoginController@login_mobile');
Route::post('login_google', 'LoginController@login_google');
Route::post('login_facebook', 'LoginController@login_facebook');
Route::post('explore', 'LoginController@explore');
Route::post('activate', 'LoginController@activate');
Route::get('activate/{code}', 'ProfileController@activate_email');
Route::get('logout', 'LoginController@logout');


/**
 * Locate Us Routes
 */
Route::post('/locate_us', 'LocateUsController@index');

/**
 * News Routes
 */
Route::group(['prefix' => 'news'],function () {
    Route::post('', 'NewsController@items');
    Route::post('single', 'NewsController@single_item');
    Route::post('share', 'NewsController@share_news');
});

/**
 * Offers Routes
 */
Route::group(['prefix' => 'offers'],function () {
    Route::post('', 'OffersController@items');
    Route::post('single', 'OffersController@single_item');
    Route::post('share', 'OffersController@share_offer');
});

/**
 * Events Routes
 */
Route::group(['prefix' => 'events'],function () {
    Route::post('items', 'NewsController@items');
    Route::post('single', 'NewsController@single_item');
    Route::post('share', 'NewsController@share_news');
});


/**
 * Talk To Us Routes
 */
Route::group(['prefix' => 'talk_to_us'],function () {
    Route::get('categories', 'SupportController@categories');
    Route::post('submit', 'SupportController@submit');
});

/**
 * Contact Us Routes
 */
Route::group(['prefix' => 'contact_us'],function () {
    Route::get('categories', 'ContactUsController@categories');
    Route::post('submit', 'ContactUsController@submit');
});

/**
 * Suggestion Route
 */
Route::post('suggestion', 'SuggestionController@index');

/**
 * Terms & Conditions Route
 */
Route::get('terms/{lang?}', 'TermsController@index');
Route::get('restaurant_terms/{lang?}', 'TermsController@restaurant_terms');
Route::get('supplier_terms/{lang?}', 'TermsController@supplier_terms');
Route::get('privacy/{lang?}', ['uses' => 'TermsController@privacy', 'as' => 'privacy.show']);
Route::get('restaurant_privacy/{lang?}', 'TermsController@restaurant_privacy');
Route::get('supplier_privacy/{lang?}', 'TermsController@supplier_privacy');


/**
 * About Us Route
 */
Route::get('about_us', 'AboutUsController@index');

/**
 * Profile Routes
 */
Route::group(['prefix' => 'profile'],function () {
    Route::get('delete', 'ProfileController@delete');
    Route::get('points', 'ProfileController@points');
    Route::get('reviews', 'ProfileController@reviews');
    Route::get('get', 'ProfileController@get');
    Route::post('set', 'ProfileController@set');
    Route::post('send_activation', 'ProfileController@send_activation');
    Route::post('activate', 'ProfileController@activate');
    Route::post('forgot_password', 'ProfileController@sendPasswordCode');
    //the below 2 apis are for reset if reset code and new password are in different page
    Route::post('verify_reset_code', 'ProfileController@verify_reset_code');
    Route::post('update_reset_password', 'ProfileController@update_reset_password');
    //the below api is for reset if reset code and new password are in same page
    Route::post('reset_password', 'ProfileController@reset_password');
    Route::post('change_password', 'ProfileController@changePassword');
    Route::post('verify_email', 'ProfileController@set_email');
});

/**
 * Countries Routes
 */
Route::get('/countries', 'LoginController@countries');
Route::get('/nationalities', 'LoginController@nationalities');

/**
 * Settings Routes
 */
Route::get('/settings', 'SettingsController@index');

/**
 * FAQs Routes
 */
Route::group(['prefix' => 'faq'],function () {
    Route::get('/{lang?}', ['uses' => 'FaqController@show', 'as' => 'faqs.show']);//webview
    Route::post('/', 'FaqController@index');
    Route::post('/paginated', 'FaqController@index_paginated');
});

/**
 * Banners Routes
 */
Route::group(['prefix' => 'banners'],function () {
    Route::get('/', 'BannersController@index');
});