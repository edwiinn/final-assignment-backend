<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/document', 'DocumentController@getAllDocumentsName');
Route::get('/document/{filename}', 'DocumentController@getDocument');
Route::post('/document', 'DocumentController@saveDocument');

Route::get('/user/public/recent', 'UserController@getRecentPublicKey');
Route::post('/user/public', 'UserController@savePublicKey');