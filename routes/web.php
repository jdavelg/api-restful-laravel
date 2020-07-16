<?php

use App\Http\Middleware\ApiAuthMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Cors;
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


Route::resource('api/user', 'UserController')->middleware(Cors::class);  
Route::put('api/user', 'UserController@updating')->middleware(Cors::class);
Route::post('api/user/register', 'UserController@register')->middleware(Cors::class);


Route::post('api/user/login', 'UserController@login')->name('user.login')->middleware(Cors::class);

Route::post('api/user/upload',  'UserController@upload')->middleware(ApiAuthMiddleware::class, Cors::class);
Route::get('api/user/avatar/{filename}',  'UserController@getImage')->middleware(Cors::class);
Route::get('api/user/detail/{id}',  'UserController@detail')->middleware(Cors::class);

Route::resource('api/category', 'CategoryController')->middleware(Cors::class);
Route::resource('api/post', 'PostController')->middleware(Cors::class);
Route::post('api/post/upload', 'PostController@upload')->middleware(Cors::class);
Route::get('api/post/image/{filename}', 'PostController@getImage')->middleware(Cors::class);
Route::get('api/post/category/{id}', 'PostController@getPostsByCategory')->middleware(Cors::class);
Route::get('api/post/user/{id}', 'PostController@getPostsByUser')->middleware(Cors::class);
