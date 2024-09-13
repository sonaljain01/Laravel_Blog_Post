<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Blog\BlogController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post("register", [ApiController::class, "register"]);
Route::post("login", [ApiController::class, "login"]);

Route::group([
    "middleware" => ["auth:api"]
], function(){

    Route::get("profile", [ApiController::class, "profile"]);
    Route::get("logout", [ApiController::class, "logout"]);
});

Route::group([
    "middleware" => ["auth:api"]
], function () {
    Route::get("/", [BlogController::class, "display"]);
    Route::post("/create", [BlogController::class, "store"]);
    Route::delete("/delete", [BlogController::class, "delete"]);
    Route::put("/update", [BlogController::class, "update"]);
});