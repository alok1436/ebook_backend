<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
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

//Authenticaed routes
Route::middleware(['auth:sanctum'])->group(function () {
    //Order routes
    Route::controller(OrderController::class)->group(function () {
        Route::get('/orders', 'get');
        Route::post('/create_order', 'create');
        Route::post('/cancel_order', 'cancelOrder');
    });
});

//product routes
Route::controller(ProductController::class)->group(function () {
    Route::get('/products',  'get');
    Route::post('/product/create', 'create');
});

//unauthenticaed routes
Route::post('/login', [AuthController::class, 'loginUser'])->name('login');
Route::post('/register', [AuthController::class, 'createUser']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password/{incryptedId}', [AuthController::class, 'resetPassword'])->name('reset-password');
Route::post('otp-varification/{incryptedId}', [AuthController::class, 'otpVarification'])->name('otp-varification');
Route::post('/email-verify', [AuthController::class, 'emailVarification']);
