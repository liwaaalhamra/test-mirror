<?php

//prefixed by 'admin'
URL::forceSchema('https');

//Route::get('/', function (){
//    return view('vendor.layout');
//});

Route::get('/', ['uses' => 'DashboardController@index', 'as' => 'admin.dashboard.index']);

Route::get('medias/{class}/{class_item_id}/view_media/{media_type?}', ['uses' => 'MediaController@view_media', 'as' => 'admin.medias.view_media']);

Route::group(['prefix' => 'notification'],function () {
    Route::get('/push', ['uses' => 'NotificationController@push', 'as' => 'admin.notification.index']);
    Route::post('/push', ['uses' => 'NotificationController@send_push', 'as' => 'admin.notification.send_push']);
    Route::post('/single_push', ['uses' => 'NotificationController@send_single_push', 'as' => 'admin.notification.send_single_push']);
});

Route::post('/import_items', ['uses' => 'SheetsController@import_items', 'as' => 'admin.import_items']);
Route::get('export_items', ['uses' => 'SettingsController@export_items', 'as' => 'admin.export_items']);

Route::post('/import_supplier_items', ['uses' => 'SheetsController@import_supplier_items', 'as' => 'admin.import_supplier_items']);
Route::get('export_supplier_items', ['uses' => 'SettingsController@export_supplier_items', 'as' => 'admin.export_supplier_items']);


Route::get('orders/',['uses' => 'OrdersController@index', 'as' => 'admin.orders.index']);
Route::delete('orders/{order_id}',['uses' => 'OrdersController@destroy', 'as' => 'admin.orders.destroy']);
Route::get('orders/{order_id}/items',['uses' => 'OrdersController@items', 'as' => 'admin.orders.items.index']);
Route::get('orders/{order_id}/invoice',['uses' => 'OrdersController@invoice', 'as' => 'admin.orders.invoice']);
Route::get('orders/{order_id}/print_invoice',['uses' => 'OrdersController@print_invoice', 'as' => 'admin.orders.print_invoice']);
Route::get('orders/action/{order_id}/{status}',['uses' => 'OrdersController@action', 'as' => 'admin.orders.action']);
Route::get('orders/{order_id}/status/{status}',['uses' => 'OrdersController@status', 'as' => 'admin.orders.status']);

Route::resource('roles', 'RolesController');
Route::resource('promo_codes', 'PromoCodesController');
Route::get('/promo_codes/publish/{promo_id}',['uses' => 'PromoCodesController@publish', 'as' => 'admin.promo_codes.publish']);


Route::get('services/publish/{id}',['uses' => 'ServicesController@publish', 'as' => 'admin.services.publish']);
Route::get('services/{service_id}/delete_single', ['uses' => 'ServicesController@delete_single_image', 'as' => 'admin.services.delete_single']);
Route::resource('services', 'ServicesController');

Route::get('user_rating/publish/{id}',['uses' => 'UserRatingController@publish', 'as' => 'admin.user_rating.publish']);
Route::get('user_rating/{user_rating_id}/delete_single', ['uses' => 'UserRatingController@delete_single_image', 'as' => 'admin.user_rating.delete_single']);
Route::resource('user_rating', 'UserRatingController');

Route::get('review_tags_list/publish/{id}',['uses' => 'ReviewTagsController@publish', 'as' => 'admin.review_tags_list.publish']);
Route::get('review_tags_list/{review_tags_list_id}/delete_single', ['uses' => 'ReviewTagsController@delete_single_image', 'as' => 'admin.review_tags_list.delete_single']);
Route::resource('review_tags_list', 'ReviewTagsController');

Route::get('top_cravings/publish/{id}',['uses' => 'CravingsController@publish', 'as' => 'admin.top_cravings.publish']);
Route::get('top_cravings/{top_cravings_id}/delete_single', ['uses' => 'CravingsController@delete_single_image', 'as' => 'admin.top_cravings.delete_single']);
Route::resource('top_cravings', 'CravingsController');

Route::get('cuisines/publish/{id}',['uses' => 'CuisinesController@publish', 'as' => 'admin.cuisines.publish']);
Route::get('cuisines/{cuisines_id}/delete_single', ['uses' => 'CuisinesController@delete_single_image', 'as' => 'admin.cuisines.delete_single']);
Route::resource('cuisines', 'CuisinesController');

Route::get('cost_levels/publish/{id}',['uses' => 'CostsController@publish', 'as' => 'admin.cost_levels.publish']);
Route::get('cost_levels/{cost_levels_id}/delete_single', ['uses' => 'CostsController@delete_single_image', 'as' => 'admin.cost_levels.delete_single']);
Route::resource('cost_levels', 'CostsController');

Route::get('tags/publish/{id}',['uses' => 'TagsController@publish', 'as' => 'admin.tags.publish']);
Route::get('tags/{tags_id}/delete_single', ['uses' => 'TagsController@delete_single_image', 'as' => 'admin.tags.delete_single']);
Route::resource('tags', 'TagsController');

Route::post('categories/order/items', ['uses' => 'CategoriesController@order', 'as' => 'admin.categories.order']);
Route::get('categories/{category_id}/delete_single', ['uses' => 'CategoriesController@delete_single_image', 'as' => 'admin.categories.delete_single']);
Route::get('categories/{category_id}/list_collections', ['uses' => 'CategoriesController@list_collections', 'as' => 'admin.categories.list_collections']);
Route::get('categories/publish/{category_id}', ['uses' => 'CategoriesController@publish', 'as' => 'admin.categories.publish']);
Route::resource('categories', 'CategoriesController');
Route::get('categories/sub_categories/{sub_category_id}/delete_single', ['uses' => 'CuratedCollectionsController@delete_single_image', 'as' => 'admin.categories.sub_categories.delete_single']);
Route::get('categories/sub_categories/publish/{sub_category_id}', ['uses' => 'CuratedCollectionsController@publish', 'as' => 'admin.categories.sub_categories.publish']);
Route::resource('categories.sub_categories', 'CuratedCollectionsController');

Route::get('sub_categories/supplier_sub_categories/publish/{option_id}', ['uses' => 'SupplierSubCategoriesController@publish', 'as' => 'admin.sub_categories.supplier_sub_categories.publish']);
Route::resource('sub_categories.supplier_sub_categories', 'SupplierSubCategoriesController');

Route::post('/import_restaurants', ['uses' => 'RestaurantsController@import_restaurants', 'as' => 'admin.import_restaurants']);
Route::get('export_restaurants', ['uses' => 'RestaurantsController@export_restaurants', 'as' => 'admin.export_restaurants']);

Route::get('restaurants/{restaurant_id}/{type}/delete_single', ['uses' => 'RestaurantsController@delete_single_image', 'as' => 'admin.restaurants.delete_single']);
Route::get('restaurants/publish/{restaurant_id}', ['uses' => 'RestaurantsController@publish', 'as' => 'admin.restaurants.publish']);
Route::get('restaurants/clone/{restaurant_id}', ['uses' => 'RestaurantsController@duplicate', 'as' => 'admin.restaurants.clone']);
Route::get('restaurants/reset_password/{id}', ['uses' => 'RestaurantsController@reset', 'as' => 'admin.restaurants.reset']);
Route::post('restaurants/reset_password', ['uses' => 'RestaurantsController@reset_password', 'as' => 'admin.restaurants.reset_password']);
Route::post('restaurants/get_uploaded_images','RestaurantsController@get_uploaded_images');
Route::post('restaurants/delete_images','RestaurantsController@delete_images');
Route::resource('restaurants', 'RestaurantsController');

Route::get('restaurants/menu/{menu_id}/delete_single', ['uses' => 'RestaurantMenuController@delete_single_image', 'as' => 'admin.restaurants.menu.delete_single']);
Route::get('restaurants/menu/publish/{menu_id}', ['uses' => 'RestaurantMenuController@publish', 'as' => 'admin.restaurants.menu.publish']);
Route::resource('restaurants.menu', 'RestaurantMenuController');
Route::get('restaurants/event_types/publish/{event_type_id}', ['uses' => 'RestaurantEventTypesController@publish', 'as' => 'admin.restaurants.event_types.publish']);
Route::resource('restaurants.event_types', 'RestaurantEventTypesController');
Route::resource('restaurants.timing', 'RestaurantTimingController');


Route::post('/import_suppliers', ['uses' => 'SuppliersController@import_suppliers', 'as' => 'admin.import_suppliers']);
Route::get('export_suppliers', ['uses' => 'SuppliersController@export_suppliers', 'as' => 'admin.export_suppliers']);

Route::get('suppliers/{restaurant_id}/{type}/delete_single', ['uses' => 'SuppliersController@delete_single_image', 'as' => 'admin.suppliers.delete_single']);
Route::get('suppliers/publish/{restaurant_id}', ['uses' => 'SuppliersController@publish', 'as' => 'admin.suppliers.publish']);
Route::get('suppliers/clone/{restaurant_id}', ['uses' => 'SuppliersController@duplicate', 'as' => 'admin.suppliers.clone']);
Route::get('suppliers/reset_password/{id}', ['uses' => 'SuppliersController@reset', 'as' => 'admin.suppliers.reset']);
Route::post('suppliers/reset_password', ['uses' => 'SuppliersController@reset_password', 'as' => 'admin.suppliers.reset_password']);
Route::post('suppliers/get_uploaded_images','SuppliersController@get_uploaded_images');
Route::post('suppliers/delete_images','SuppliersController@delete_images');
Route::resource('suppliers', 'SuppliersController');

Route::get('suppliers/menu/{menu_id}/delete_single', ['uses' => 'SupplierMenuController@delete_single_image', 'as' => 'admin.suppliers.menu.delete_single']);
Route::get('suppliers/menu/publish/{menu_id}', ['uses' => 'SupplierMenuController@publish', 'as' => 'admin.suppliers.menu.publish']);
Route::resource('suppliers.menu', 'SupplierMenuController');

Route::group(['prefix' => 'supplier_items'],function () {
    Route::post('/order', ['uses' => 'SupplierItemsController@order', 'as' => 'admin.supplier_items.order']);
    Route::get('/publish/{item_id}',['uses' => 'SupplierItemsController@publish', 'as' => 'admin.supplier_items.publish']);
    Route::post('/update_item','SupplierItemsController@update');
    Route::get('/{item_id}/delete_single', ['uses' => 'SupplierItemsController@delete_single_image', 'as' => 'admin.supplier_items.delete_single']);
});
Route::resource('supplier_items', 'SupplierItemsController');
Route::get('supplier_items/supplier_customizations/publish/{customization_id}', ['uses' => 'SupplierItemCustomizationsController@publish', 'as' => 'admin.supplier_items.supplier_customizations.publish']);
Route::resource('supplier_items.supplier_customizations', 'SupplierItemCustomizationsController');
Route::get('supplier_customizations/supplier_options/publish/{option_id}', ['uses' => 'SupplierCustomizationOptionsController@publish', 'as' => 'admin.supplier_customizations.supplier_options.publish']);
Route::resource('supplier_customizations.supplier_options', 'SupplierCustomizationOptionsController');


Route::group(['prefix' => 'items'],function () {
    Route::post('/order', ['uses' => 'ItemsController@order', 'as' => 'admin.items.order']);
    Route::get('/publish/{item_id}',['uses' => 'ItemsController@publish', 'as' => 'admin.items.publish']);
    Route::post('/update_item','ItemsController@update');
    Route::get('/{item_id}/delete_single', ['uses' => 'ItemsController@delete_single_image', 'as' => 'admin.items.delete_single']);
});
Route::resource('items', 'ItemsController');
Route::get('items/customizations/publish/{customization_id}', ['uses' => 'ItemCustomizationsController@publish', 'as' => 'admin.items.customizations.publish']);
Route::resource('items.customizations', 'ItemCustomizationsController');
Route::get('customizations/options/publish/{option_id}', ['uses' => 'CustomizationOptionsController@publish', 'as' => 'admin.customizations.options.publish']);
Route::resource('customizations.options', 'CustomizationOptionsController');

Route::resource('sizes', 'SizesController');
Route::resource('areas', 'AreasController');
Route::resource('regions', 'RegionsController');
Route::resource('points', 'PointsController');
Route::get('faq/publish/{faq_id}',['uses' => 'FaqController@publish', 'as' => 'admin.faq.publish']);
Route::resource('faq', 'FaqController');

Route::resource('experts', 'ExpertsController');
Route::resource('garages', 'GaragesController');
Route::get('/get_areas/{region_id}', ['uses' => 'ExpertsController@areas', 'as' => 'admin.regions.areas']);
Route::get('/get_collections/{category_id}', ['uses' => 'RestaurantsController@collections', 'as' => 'admin.restaurants.collections']);
Route::get('/get_supplier_sub_categories/{category_id}', ['uses' => 'SuppliersController@supplier_sub_categories', 'as' => 'admin.suppliers.supplier_sub_categories']);
Route::get('/get_menu_categories/{category_id}', ['uses' => 'ItemsController@menu_categories', 'as' => 'admin.items.menu_categories']);
Route::get('/get_supplier_menu_categories/{category_id}', ['uses' => 'SupplierItemsController@menu_categories', 'as' => 'admin.supplier_items.menu_categories']);

Route::resource('brands', 'BrandsController');
Route::resource('product_types', 'ProductTypesController');
Route::resource('contact_us_categories', 'ContactUsCategoriesController');
Route::get('contact_us',['uses' => 'ContactUsController@index', 'as' => 'admin.contact_us.index']);

Route::get('users/reset_password/{id}', ['uses' => 'UsersController@reset', 'as' => 'admin.users.reset']);
Route::get('users/block/{id}',['uses' => 'UsersController@block', 'as' => 'admin.users.block']);
Route::post('users/reset_password', ['uses' => 'UsersController@reset_password', 'as' => 'admin.users.reset_password']);
Route::resource('users', 'UsersController');

Route::group(['prefix' => 'terms'],function () {
    Route::get('', ['uses' => 'TermsController@index', 'as' => 'admin.terms.index']);
    Route::post('', ['uses' => 'TermsController@set', 'as' => 'admin.terms.set']);
    Route::get('/delete', ['uses' => 'TermsController@destroy', 'as' => 'admin.terms.delete']);
});

Route::group(['prefix' => 'about_us'],function () {
    Route::get('', ['uses' => 'AboutUsController@index', 'as' => 'admin.about_us.index']);
    Route::post('', ['uses' => 'AboutUsController@set', 'as' => 'admin.about_us.set']);
    Route::get('/delete', ['uses' => 'AboutUsController@destroy', 'as' => 'admin.about_us.delete']);
});

Route::get('cms_users/lock/{id}',['uses' => 'CMSUsersController@lock', 'as' => 'admin.cms_users.lock']);
Route::get('cms_users/reset_password/{id}', ['uses' => 'CMSUsersController@reset', 'as' => 'admin.cms_users.reset']);
Route::post('cms_users/reset_password', ['uses' => 'CMSUsersController@reset_password', 'as' => 'admin.cms_users.reset_password']);
Route::resource('cms_users', 'CMSUsersController');

/*************************************************************               *************************************************************/
/*************************************************************               *************************************************************/
/************************************************************* Backup Routes *************************************************************/
/*************************************************************               *************************************************************/
/*************************************************************               *************************************************************/


Route::get('locate_us/pickup/{id}',['uses' => 'LocateUsController@pickup', 'as' => 'admin.locate_us.pickup']);
Route::get('locate_us/{location_id}/delete_single', ['uses' => 'LocateUsController@delete_single_image', 'as' => 'admin.locate_us.delete_single']);
Route::resource('locate_us', 'LocateUsController');



Route::get('settings', ['uses' => 'SettingsController@index', 'as' => 'admin.settings.index']);
Route::post('settings', ['uses' => 'SettingsController@changePassword', 'as' => 'admin.settings.changepassword']);
Route::post('app_settings', ['uses' => 'SettingsController@app_settings', 'as' => 'admin.app_settings']);

Route::resource('talk_to_us_categories', 'TalkToUsCategoriesController');
Route::get('talk_to_us',['uses' => 'TalkToUsController@index', 'as' => 'admin.talk_to_us.index']);


Route::get('user_gifts/action/{id}/{action}',['uses' => 'UserGiftsController@action', 'as' => 'admin.user_gifts.action']);
Route::resource('user_gifts', 'UserGiftsController');

Route::get('user_redeem_requests/action/{id}',['uses' => 'UserRedeemRequestsController@action', 'as' => 'admin.user_redeem_requests.action']);
Route::resource('user_redeem_requests', 'UserRedeemRequestsController');

Route::get('redeem_items/toggle/{id}',['uses' => 'RedeemController@toggle', 'as' => 'admin.redeem_items.toggle']);
Route::resource('redeem_items', 'RedeemController');
Route::post('redeem_items/order/items', ['uses' => 'RedeemController@order', 'as' => 'admin.redeem_items.order']);

Route::get('gifts/lock/{id}',['uses' => 'GiftsController@lock', 'as' => 'admin.gifts.lock']);
Route::get('gifts/{gift_id}/delete_single', ['uses' => 'GiftsController@delete_single_image', 'as' => 'admin.gifts.delete_single']);
Route::resource('gifts', 'GiftsController');

Route::get('jobs/publish/{job_id}',['uses' => 'JobsController@publish', 'as' => 'admin.jobs.publish']);
Route::get('jobs/{job_id}/delete_single', ['uses' => 'JobsController@delete_single_image', 'as' => 'admin.jobs.delete_single']);
Route::post('jobs_introduction', ['uses' => 'JobsController@jobs_introduction', 'as' => 'admin.jobs.jobs_introduction']);
Route::resource('jobs', 'JobsController');

Route::get('workshops_training/private/{wt_id}',['uses' => 'WTController@manage_private', 'as' => 'admin.workshops_training.private']);
Route::get('workshops_training/public/{wt_id}',['uses' => 'WTController@manage_public', 'as' => 'admin.workshops_training.public']);
Route::get('workshops_training/publish/{wt_id}',['uses' => 'WTController@publish', 'as' => 'admin.workshops_training.publish']);
Route::get('workshops_training/{wt_id}/delete_single', ['uses' => 'WTController@delete_single_image', 'as' => 'admin.workshops_training.delete_single']);
Route::post('wt_introduction', ['uses' => 'WTController@wt_introduction', 'as' => 'admin.workshops_training.wt_introduction']);
Route::resource('workshops_training', 'WTController');

Route::get('memos/{memo_id}/{type}/delete_single', ['uses' => 'MemosController@delete_single_image', 'as' => 'admin.memos.delete_single']);
Route::get('memos/publish/{memo_id}',['uses' => 'MemosController@publish', 'as' => 'admin.memos.publish']);
Route::resource('memos', 'MemosController');

//Route::get('events/private/{event_id}',['uses' => 'EventsController@manage_private', 'as' => 'admin.events.private']);
//Route::get('events/public/{event_id}',['uses' => 'EventsController@manage_public', 'as' => 'admin.events.public']);
//Route::get('events/publish/{event_id}',['uses' => 'EventsController@publish', 'as' => 'admin.events.publish']);
////Route::get('events/{event_id}/delete_single', ['uses' => 'EventController@delete_single_image', 'as' => 'admin.events.delete_single']);
////Route::resource('events', 'EventController');
//Route::post('events/get_uploaded_images','EventsController@get_uploaded_images');
//Route::post('events/update_item','EventsController@update');
//Route::post('events/delete_images','EventsController@delete_images');
//Route::resource('events', 'EventsController');

Route::get('banners/private/{banner_id}',['uses' => 'BannersController@manage_private', 'as' => 'admin.banners.private']);
Route::get('banners/public/{banner_id}',['uses' => 'BannersController@manage_public', 'as' => 'admin.banners.public']);
Route::get('banners/both/{banner_id}',['uses' => 'BannersController@manage_both', 'as' => 'admin.banners.both']);
Route::get('banners/publish/{banner_id}',['uses' => 'BannersController@publish', 'as' => 'admin.banners.publish']);
Route::get('banners/{banner_id}/delete_single', ['uses' => 'BannersController@delete_single_image', 'as' => 'admin.banners.delete_single']);
Route::resource('banners', 'BannersController');

Route::get('offers/both/{offer_id}',['uses' => 'OffersController@manage_both', 'as' => 'admin.offers.both']);
Route::get('offers/private/{offer_id}',['uses' => 'OffersController@manage_private', 'as' => 'admin.offers.private']);
Route::get('offers/public/{offer_id}',['uses' => 'OffersController@manage_public', 'as' => 'admin.offers.public']);
Route::get('offers/publish/{offer_id}',['uses' => 'OffersController@publish', 'as' => 'admin.offers.publish']);
Route::get('offers/{offer_id}/delete_single', ['uses' => 'OffersController@delete_single_image', 'as' => 'admin.offers.delete_single']);
Route::resource('offers', 'OffersController');

Route::get('news/private/{news_id}',['uses' => 'NewsController@manage_private', 'as' => 'admin.news.private']);
Route::get('news/public/{news_id}',['uses' => 'NewsController@manage_public', 'as' => 'admin.news.public']);
Route::get('news/publish/{news_id}',['uses' => 'NewsController@publish', 'as' => 'admin.news.publish']);
Route::get('news/points/{news_id}',['uses' => 'NewsController@points', 'as' => 'admin.news.points']);
Route::get('news/{news_id}/delete_single', ['uses' => 'NewsController@delete_single_image', 'as' => 'admin.news.delete_single']);
Route::resource('news', 'NewsController');

Route::get('wall/{wall_id}/{type}/delete_single', ['uses' => 'WallController@delete_single_image', 'as' => 'admin.wall.delete_single']);
Route::resource('wall', 'WallController');
Route::resource('profile_types', 'ProfileTypesController');
Route::get('polls/publish/{poll_id}',['uses' => 'PollsController@publish', 'as' => 'admin.polls.publish']);
Route::resource('polls', 'PollsController');
Route::resource('polls.options', 'PollOptionsController');

Route::get('surveys/publish/{survey_id}',['uses' => 'SurveysController@publish', 'as' => 'admin.surveys.publish']);
Route::resource('surveys', 'SurveysController');
Route::get('surveys/questions/publish/{question_id}',['uses' => 'SurveyQuestionsController@publish', 'as' => 'admin.survey_questions.publish']);
Route::resource('surveys.questions', 'SurveyQuestionsController');
Route::resource('survey_questions.answers', 'SurveyAnswersController');