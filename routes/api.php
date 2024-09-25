<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Blog\BlogController;
use App\Http\Controllers\Categorycontroller;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\CommentController;

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


Route::get("/category", [Categorycontroller::class, "index"]);
Route::group(["prefix" => "/category"], function () {
    Route::post("/create", [Categorycontroller::class, "store"]);
    Route::put("/update/{id}", [Categorycontroller::class, "update"]);
    Route::delete("/delete/{id}", [Categorycontroller::class, "destroy"]);
})->middleware("auth:api");

Route::group(["prefix" => "/role"], function () {
    Route::delete("/blogs/{id}", [BlogController::class, "destroy"]); 
})->middleware("auth:api");

Route::group(["prefix"=> "comment"], function () {
    Route::post("/create", [CommentController::class, "storeComment"]);
    Route::get("/{blog}/comments", [CommentController::class, "displayComments"]);
})->middleware("auth:api");