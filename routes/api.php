<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->group(function () {
    // our routes to be protected will go in here

    //user
    Route::get('/detail_profile','Auth\ApiAuthController@getDetails')->name('detail.api');
    Route::get('/logout', 'Auth\ApiAuthController@logout')->name('logout.api');
    Route::put('/edit_profile','Auth\ApiAuthController@editUsers')->name('editprofile.api');

    Route::post('/add_type','TypeApiController@create')->name('type.create');
    
});

Route::group(['middleware' => ['cors', 'json.response']], function() {
    // public routes
    Route::post('/login', 'Auth\ApiAuthController@login')->name('login.api');
    Route::post('/register','Auth\ApiAuthController@register')->name('register.api');
});