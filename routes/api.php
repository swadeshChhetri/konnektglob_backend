<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\SellerController;

Route::post('register', [AuthController::class, "register"]);
Route::post('login', [AuthController::class, "login"]);
Route::get('users/count', [UserController::class, 'getUserCount']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'getProductWithSeller']);
Route::get('/seller/inquiries', [InquiryController::class, 'index']);


Route::group([
  "middleware" => ["auth:sanctum"]
], function () {
  // Route::apiResource('users', UserController::class);
  Route::get("profile", [AuthController::class, "profile"]);
  Route::get("logout", [AuthController::class, "logout"]);

  // Product Creation, Update & Delete (Only for authenticated users)
  Route::post('/products', [ProductController::class, 'store']);
  Route::put('/products/{product}', [ProductController::class, 'update']);
  Route::delete('/products/{product}', [ProductController::class, 'destroy']);
  Route::get('/user-dashboard', [UserController::class, 'getUserDashboardData']);
  Route::get('/user-myProducts/{product}', [ProductController::class, 'productDetails']);
  Route::get('/user-myProducts', [ProductController::class, 'myProducts']);
  Route::post('/sellers', [SellerController::class, 'store']);
  Route::post('/inquiries', [InquiryController::class, 'store']);
  Route::get('/showinquiries', [InquiryController::class, 'showInquiries']);
  Route::get("seller/profile", [SellerController::class, "show"]);
});
