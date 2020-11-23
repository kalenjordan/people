<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/newsletter-subscribe', 'ApiController@newsletterSubscribe');

Route::get('/geocode', 'ApiController@geocode');
Route::get('/geocode-delete', 'ApiController@geocodeDelete');

Route::get('/public-tags', 'ApiController@publicTags');
Route::get('/people/{slug}/public-tag', 'ApiController@personPublicTag');

Route::get('/private-tags', 'ApiController@privateTags');
Route::get('/people/{slug}/private-tag', 'ApiController@personPrivateTag');
