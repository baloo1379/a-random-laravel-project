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
    $subpages = App\Subpage::all();
    return $subpages;
});

Route::post('/{subpage}/n', 'PostController@storeNews');
Route::patch('/{subpage}/n/{post}', 'PostController@updateNews');
Route::get('/{subpage}/n/{post}', 'PostController@show');
Route::delete('/{subpage}/n/{post}', 'PostController@destroy');
Route::post('/{subpage}/n/{post}/gallery', 'GalleryController@store');

Route::post('/{subpage}/b', 'PostController@storeBook');
Route::patch('/{subpage}/b/{post}', 'PostController@updateBook');
Route::get('/{subpage}/b/{post}', 'PostController@show');
Route::delete('/{subpage}/b/{post}', 'PostController@destroy');

Route::get('/{subpage}', 'SubpageController@show');
Route::post('/', 'SubpageController@store');
Route::patch('/{subpage}', 'SubpageController@update');
Route::delete('/{subpage}', 'SubpageController@destroy');
