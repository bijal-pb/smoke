<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\FlavourCategoryController;
use App\Http\Controllers\Admin\FlavourController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\NotificationController;



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

Route::get('/token/{id}', [HomeController::class, 'accessToken'])->name('authtoken');

Route::get('/', function () {
    return redirect("/admin");
});


Auth::routes();

Route::get('/home', function () {
    return redirect("/admin");
});

Route::get('/forgot/password', [UserController::class, 'forgot_password'])->name('admin.forgot');
Route::post('/forgot/password/mail', [UserController::class, 'password_mail']);
Route::post('admin/login', [UserController::class, 'admin_login'])->name('admin.login');;
Route::post('admin/app/setting', [UserController::class, 'setting_update'])->name('admin.setting.update');

Route::name('admin.')->namespace('Admin')->group(function () {
    Route::group(['prefix' => 'admin', 'middleware' => ['admin.check']], function () {
        Route::get('/', [AdminController::class, 'index'])->name('home');
       
        // category routes
        Route::get('/category', [CategoryController::class, 'index'])->name('category');
        Route::get('/category/get', [CategoryController::class, 'getCategory'])->name('category.get');
        Route::post('/category/store', [CategoryController::class, 'store'])->name('category.store');
        Route::post('/category/delete', [CategoryController::class, 'delete'])->name('category.delete');
        Route::get('/category/list', [CategoryController::class, 'categories'])->name('category.list');

        // Flavour Category route
        Route::get('/flavour/category', [FlavourCategoryController::class, 'index'])->name('flavourcategory');
        Route::get('/flavour/category/get', [FlavourCategoryController::class, 'getFlavourCategory'])->name('flavour.category.get');
        Route::post('/flavour/category/store', [FlavourCategoryController::class, 'store'])->name('flavour.category.store');
        Route::post('/flavour/category/delete', [FlavourCategoryController::class, 'delete'])->name('flavour.category.delete');
        Route::get('flavour/category/list', [FlavourCategoryController::class, 'flavourcategories'])->name('flavour.category.list');
        
        // Flavours routes
        Route::get('/flavour', [FlavourController::class, 'index'])->name('flavour');
        Route::get('/flavour/get', [FlavourController::class, 'getFlavour'])->name('flavour.get');
        Route::post('/flavour/store', [FlavourController::class, 'store'])->name('flavour.store');
        Route::post('/flavour/delete', [FlavourController::class, 'delete'])->name('flavour.delete');
        Route::get('/flavour/list', [FlavourController::class, 'flavour'])->name('flavour.list');
        
       // users  route
       Route::get('/profile', [UserController::class, 'profile'])->name('profile');
       Route::get('/password', [UserController::class, 'password'])->name('password');
       Route::post('/password/change', [UserController::class, 'change_password'])->name('password.update');
       Route::post('/profile/update', [UserController::class, 'update_profile'])->name('profile.update');
       Route::get('/users', [UserController::class, 'index'])->name('user');
       Route::get('/users/list', [UserController::class, 'users'])->name('users.list');
       Route::get('/get/user', [UserController::class, 'getUser'])->name('user.get');
       Route::get('/user/status/change', [UserController::class, 'changeStatus'])->name('user.status.change');
       Route::post('/user/store', [UserController::class, 'store'])->name('user.store');


        // app setting
        Route::get('setting', [UserController::class, 'app_setting'])->name('setting');
        Route::post('setting/update', [UserController::class, 'setting_update']);

        //post route
        Route::get('/post', [PostController::class, 'index'])->name('post');
        Route::get('/post/get', [PostController::class, 'getPost'])->name('post.get');;
        Route::post('/post/store', [PostController::class, 'store'])->name('post.store');
        Route::get('/post/list', [PostController::class, 'post'])->name('post.list');

        //notification
        Route::get('notification', [NotificationController::class, 'app_notification'])->name('notification');
        Route::post('notification/send', [ NotificationController::class ,'send_notification'])->name('notification.send');

      
    });
});

Route::get('logout', [LoginController::class, 'logout'])->name('logout');
