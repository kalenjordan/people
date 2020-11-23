<?php

Route::get('/', 'IndexController@welcome');

Route::get('/auth', 'AuthController@auth');
Route::get('/logout', 'AuthController@logout');
Route::get('/auth/callback', 'AuthController@handleGoogleCallback');

Route::get('/account/settings', 'AccountController@settings');
Route::post('/account/settings', 'AccountController@settingsPost');

Route::get('/account/people', 'AccountController@personList');
Route::get('/account/people/new', 'AccountController@personNew');
Route::post('/account/people/new', 'AccountController@personNewPost');
Route::get('/account/people/new-from-linkedin', 'AccountController@personNewFromLinkedIn');
Route::get('/account/people/{id}', 'AccountController@personEdit');
Route::post('/account/people/{id}', 'AccountController@personEditPost');
Route::get('/account/people/{id}/delete', 'AccountController@personDelete');


Route::get('/blog/{slug}', 'IndexController@blog');
Route::get('/s/{slug}', 'IndexController@savedSearch');
Route::get('/{slug}', 'IndexController@person');
