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

Route::get('/home', 'HomeController@index')->name('home');

Route::post('/upload', 'cloudController@upload');
Route::post('/folderCreate', 'cloudController@folderCreate');
Route::post('/getList', 'cloudController@getList');
Route::post('/goUp', 'cloudController@goUp');
Route::post('/getPath', 'cloudController@getPath');
Route::post('/getSelectedFilesRecursiveStat', 'cloudController@getSelectedFilesRecursiveStat');
Route::post('/pasteFiles', 'cloudController@pasteFiles');
