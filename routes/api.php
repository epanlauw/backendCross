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

    //type
    Route::post('/add_type','TypeApiController@create')->name('type.create');

    //recipe
    Route::post('/add_recipe','RecipesController@create')->name('recipe.create');
    Route::get('/recipe','RecipesController@index')->name('recipe.all');
    Route::get('/recipe/{id}','RecipesController@show')->name('recipe.detail');
    Route::put('/recipe/{id}','RecipesController@edit')->name('recipe.edit');
    Route::delete('/recipe/{id}','RecipesController@destroy')->name('recipe.delete');

});

Route::group(['middleware' => ['cors', 'json.response']], function() {
    // public routes
    Route::post('/login', 'Auth\ApiAuthController@login')->name('login.api');
    Route::post('/register','Auth\ApiAuthController@register')->name('register.api');
});