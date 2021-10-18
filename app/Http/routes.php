<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/



Route::auth();

Route::group(['prefix' => 'cms'], function () {
	Route::get('/', function () {
		// check if the user is authenticated.
		if(auth()->check()) {
			// When an authenticated user visit the home page, he/she will be redirected to the admin page (dashboard).
			//return view('pages.notification.push');

			return redirect('admin');
		}
		// When an un-authenticated user visit the home page, he/she will be redirected to the login page.
		return redirect('login');
	});

});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::post('/flick', ['uses' => 'Api\PaymentController@flick_update']);
Route::any('/feedback', ['uses' => 'Api\AboutUsController@feedback', 'as' => 'feedback']);

// APPLE LOGIN
Route::get('apple', 'Api\AppleController@index');
Route::post('apple/redirect', 'Api\AppleController@apple_redirect');
Route::get('apple/completed', ['uses' => 'Api\AppleController@completed', 'as' => 'apple.completed']);


Route::get('/', function () {
		return view('welcome');
    // check if the user is authenticated.
    if(auth()->check()) {
        // When an authenticated user visit the home page, he/she will be redirected to the admin page (dashboard).
        //return view('pages.notification.push');

        return redirect('admin');
    }
    // When an un-authenticated user visit the home page, he/she will be redirected to the login page.
    return redirect('login');
});
Route::group(['middleware' => ['web']], function () {

    Route::get('payment/{user_id?}/{order_id?}', ['uses' => 'Admin\PaymentController@show', 'as' => 'admin.payment.show']);

});




Route::get('docs', function(){
	return View::make('docs...index');
});


