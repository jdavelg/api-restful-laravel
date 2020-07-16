<?php

use App\Http\Middleware\ApiAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});


Route::resource('api/user', 'UserController');  

Route::post('api/user/register', 'UserController@register');


Route::post('api/user/login', 'UserController@login')->name('user.login');

Route::post('api/user/upload',  'UserController@upload')->middleware(ApiAuthMiddleware::class);
Route::get('api/user/avatar/{filename}',  'UserController@getImage');
Route::get('api/user/detail/{id}',  'UserController@detail');

Route::resource('api/category', 'CategoryController');
Route::resource('api/post', 'PostController');
Route::post('api/post/upload', 'PostController@upload');
Route::get('api/post/image/{filename}', 'PostController@getImage');
Route::get('api/post/category/{id}', 'PostController@getPostsByCategory');
Route::get('api/post/user/{id}', 'PostController@getPostsByUser');
