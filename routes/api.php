<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Blog\BlogController;

Route::get("/",function(){
    return response()->json([
        "status" => "up",
        "message"=> "Welcome to Blog API",
        "time" => now()
    ]);
});


Route::group(["prefix" => "/auth"], function () {
    Route::post("register", [ApiController::class, "register"]);
    Route::post("login", [ApiController::class, "login"]);
});

Route::group(["prefix" => "/user"], function () {
    Route::get("profile", [ApiController::class, "profile"]);
    Route::get("logout", [ApiController::class, "logout"]);
})->middleware("auth:api");


Route::get("/blog", [BlogController::class, "display"]);
Route::group(["prefix" => "/blog"], function () {
    Route::post("/create", [BlogController::class, "store"]);
    Route::put("/update", [BlogController::class, "update"]);
    Route::delete("/delete", [BlogController::class, "destroy"]);
})->middleware("auth:api");