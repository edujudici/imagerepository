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

Auth::routes();

Route::group(['middleware' => 'auth'], function ()
{
	
	Route::get('/home', 'HomeController@index');


    Route::post('/save',  ['as' => 'company.save',  'uses' => 'HomeController@save']);
    Route::post('/delete',  ['as' => 'company.delete',  'uses' => 'HomeController@delete']);
    
    Route::get ('/image-list/{id}',  ['as' => 'image.list',  'uses' => 'ImageController@index']);
    Route::post('/image-save',  ['as' => 'image.save',  'uses' => 'ImageController@save']);
    Route::post('/image-delete',  ['as' => 'image.delete',  'uses' => 'ImageController@delete']);

    Route::get('/image-link/{id}', ['as' => 'image.link', 'uses' => 'ImageController@link']);

});


