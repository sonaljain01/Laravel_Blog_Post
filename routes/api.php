<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Blog\BlogController;
use App\Http\Controllers\AdminController;

Route::get("/",function(){
    return response()->json([
        "status" => "up",
        "message"=> "Welcome to Blog API",
        "time" => now()
    ]);
});


Route::group(["prefix" => "auth"], function () {
    Route::post("register", [ApiController::class, "register"]);
    Route::post("login", [ApiController::class, "login"]);
});

Route::group(["prefix" => "user"], function () {
    Route::get("profile", [ApiController::class, "profile"]);
    Route::get("logout", [ApiController::class, "logout"]);
})->middleware("auth:api");


Route::group(["prefix" => "blog"], function () {
    Route::get("/", [BlogController::class, "display"]);
    Route::post("create", [BlogController::class, "store"]);
    Route::put("update", [BlogController::class, "update"]);
    Route::delete("delete", [BlogController::class, "destroy"]);
})->middleware("auth:api");

Route::group(["prefix" => "admin/blog"], function () {
    Route::get("/", [AdminController::class, "display"]);
    Route::post("create", [AdminController::class, "store"]);
    Route::put("update", [AdminController::class, "update"]);
    Route::delete("delete/{id}", [AdminController::class, "destroy"]);
})->middleware("auth:api");