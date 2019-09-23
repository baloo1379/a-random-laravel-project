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

Route::get('/{subpage}', 'SubpageController@show');
Route::post('/', 'SubpageController@store');
Route::patch('/{subpage}', 'SubpageController@update');
Route::delete('/{subpage}', 'SubpageController@destroy');

Route::get('/{subpage}/{post}', 'PostController@show');
Route::post('/{subpage}/news', 'PostController@storeNews');
Route::patch('/{subpage}/{post}', 'PostController@updateNews');
Route::delete('/{subpage}/{post}', 'PostController@destroy');
