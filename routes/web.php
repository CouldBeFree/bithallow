<?php
use App\Answer;
/*
* Pages
*/
Route::get('/', ['as' => 'index', 'uses' => 'PagesController@index']);
Route::get('/register', ['as' => 'register', 'uses' => 'PagesController@register']);
/*
* Auth/Register
*/
Route::group(['prefix' => 'user'], function(){
	Route::post('/signin', ['as' => 'signin', 'uses' => 'AccountController@auth'])->middleware('throttle:2,1');
	Route::post('/signup', ['as' => 'signup', 'uses' => 'AccountController@register']);
	Route::get('/emailverify', ['as' => 'emailverify', 'uses' => 'AccountController@emailVerify']);
	Route::get('/logout', ['as' => 'logout', 'uses' => function(){
		Auth::logout();
		return redirect()->route('index');
	}]);
	/*
	* My account
	*/
	Route::group(['middleware' => 'auth'], function(){
		Route::get('/balance', ['as' => 'balance', 'uses' => 'PagesController@balance']);
		Route::get('/data', ['as' => 'data', 'uses' => 'PagesController@data']);
		Route::post('/data/edit', ['as' => 'editdate', 'uses' => 'AccountController@edit']);
		Route::get('/history', ['as' => 'historyBets', 'uses' => 'PagesController@historyBets']);
		Route::get('/security', ['as' => 'security', 'uses' => 'PagesController@security']);
	});
});
/*
* Actions
*/
Route::get('/action/{id}', ['as' => 'action', 'uses' => 'ActionController@index']);
Route::get('/test_funct/{var}', ['as' => 'test', 'uses' => 'ActionController@test']);
/* Ajax */
Route::group(['prefix' => 'ajax'], function(){
	Route::post('/actioninfo/{id}', ['as' => 'actioninfo', 'uses' => 'ActionController@actionInfo']);
	Route::group(['middleware' => 'auth'], function(){
		Route::post('/addbet', ['as' => 'addbet', 'uses' => 'ActionController@addBet']);
		Route::post('/betsinfo', ['as' => 'betsinfo', 'uses' => 'ActionController@betsInfo']);
		Route::post('/profitinfo', ['as' => 'profitinfo', 'uses' => 'ActionController@profitInfo']);
		Route::post('/balance', ['as' => 'balance.ajax', 'uses' => function(){
			return Answer::generate([
				'status' => 'success',
				'data' => Auth::user()->balance
			]);
		}]);
	});
});

/* Categories */
Route::get('/category/{id}', ['as' => 'category', 'uses' => 'PagesController@categories']);

/*
*
* Admin
*
*/

Route::group(['prefix' => 'admin'], function(){
	Route::get('/', ['as' => 'admin.index', 'uses' => 'AdminController@index'])->middleware('access:admin');
});