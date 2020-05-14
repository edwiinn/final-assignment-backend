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

Route::get('/documents', 'DocumentController@getAllDocumentsName');
Route::get('/documents/{filename}', 'DocumentController@getDocument');
Route::post('/documents', 'DocumentController@saveDocument');

Route::get('/signed-documents/{filename}', 'DocumentController@getSignedDocument');
Route::get('/signed-documents', 'DocumentController@getSignedDocuments');
Route::post('/signed-documents', 'DocumentController@saveSignedDocument');

Route::get('/user/public/recent', 'UserController@getRecentPublicKey');
Route::post('/user/public', 'UserController@savePublicKey');

Route::post('/certificate/csr/sign', 'CertificationController@signCsrRequest');

Route::get('/auth/gl/callback', 'AuthController@googleCallback');