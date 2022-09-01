<?php

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\FlavourController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\NotificationController;
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


Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::post('username/check', [UserController::class, 'check_username']);
Route::post('forgot/password', [UserController::class,'forgot_password']);
Route::get('countries', [UserController::class, 'get_countries']);
Route::get('privacy-term', function(){
    return view('privacy');
});
Route::group(['middleware' => ['auth:api']], function () {
    // profile
    Route::get('profile', [UserController::class, 'me']);
    Route::post('user/profile', [UserController::class, 'user_profile']);
    Route::post('profile/edit', [UserController::class, 'edit_profile']);
    Route::post('change/password', [UserController::class, 'change_password']);

    // notification enable disable
    Route::post('notification/enable', [UserController::class, 'notification_enable']);

    // flavour
    Route::get('flavour/categories', [FlavourController::class, 'get_flavour_categories']);
    Route::get('flavours', [FlavourController::class, 'get_flavours']);

    // Posts
    Route::post('post/add', [PostController::class, 'create_post']);
    Route::post('post/review', [PostController::class, 'post_review']);
    Route::post('post/like', [PostController::class, 'post_like']);
    Route::get('post', [PostController::class, 'get_post']);

    // Followers
    Route::post('following', [UserController::class, 'following']);
    Route::get('following/list', [UserController::class, 'get_following']);
    Route::get('follower/list', [UserController::class, 'get_followers']);
    // my collection
    Route::get('collections', [PostController::class, 'get_collections']);

    // home 
    Route::get('home', [HomeController::class, 'home']);
    Route::get('recommendations', [HomeController::class, 'recommendations']);
   
    // search posts
    Route::get('posts/search', [HomeController::class, 'get_posts']);

    // Notifications
    Route::get('notfication/list', [NotificationController::class, 'get_notification']);

    //logout
    Route::get('logout', [UserController::class, 'logout']);
});
