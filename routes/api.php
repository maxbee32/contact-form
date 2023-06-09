<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware'=>'api',
              'prefix'=>'admin'
],function($router){

    Route::post("admin-signup", "App\Http\Controllers\AdminController@adminSignUp");
    Route::post("admin-login","App\Http\Controllers\AdminController@adminLogin");
    Route::post("admin-getForms", "App\Http\Controllers\AdminController@getForms");
    Route::post("admin-sendApproveDecision/{id}", "App\Http\Controllers\AdminController@sendApproveDecision")->name('admin.sendApproveDecision');
    Route::post("admin-sendRejectDecision/{id}", "App\Http\Controllers\AdminController@sendRejectDecision")->name('admin.sendRejectDecision');
    Route::post("admin-createEmail", "App\Http\Controllers\AdminController@createEmail");
    Route::post("admin-bulkEmail", "App\Http\Controllers\AdminController@bulkEmail");
});


Route::group(['middleware'=>'api',
              'prefix'=>'user'
],function($router){
    Route::post("contact-form", "App\Http\Controllers\UserController@contactForm");
});
