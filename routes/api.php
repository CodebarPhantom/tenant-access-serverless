<?php

use App\Http\Controllers\API\v1\User\UserController;
use App\Http\Controllers\API\v1\UrlRedirect\UrlRedirectController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::prefix("/v1")->as("v1.")->group(function() {

    Route::prefix("/karawang")->as("karawang.")->group(function() {
        Route::get('/user',[UserController::class,'listUserKarawang']);
        Route::delete('/user/{id}',[UserController::class,'removeUserKarawang']);
    });

    Route::prefix("/subang")->as("subang.")->group(function() {
        Route::get('/user',[UserController::class,'listUserSubang']);
        Route::delete('/user/{id}',[UserController::class,'removeUserSubang']);
    });

    Route::prefix("/url-redirect")->as("url-redirect.")->group(function() {
        Route::get("/",[UrlRedirectController::class,'index']);
        Route::get("/{id}",[UrlRedirectController::class,'show']);
        Route::post("/store",[UrlRedirectController::class,'store']);
        Route::patch("/{id}",[UrlRedirectController::class,'update']);
    });




});


