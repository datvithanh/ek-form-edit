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

Route::get('/', 'WebController@index');

Route::get('/post/{postId}/edit', 'WebController@editPost');

Route::get('/post/{postId}/raw', 'WebController@rawHistory');

Route::put('/api/post/{postId}', 'WebController@editPostApi');

Route::get('/item/l5/{id}', 'WebController@l5');

Route::get('/api/test', 'WebController@test');

Route::put('/api/item/l5/{id}', 'WebController@itemL5');