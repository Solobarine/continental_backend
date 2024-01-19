<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CardController;
use App\Http\Controllers\API\DepositController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\TransferController;
use App\Http\Controllers\API\UserController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', [AuthController::class, 'register']);

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    // Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::get('user', 'user');
});

Route::controller(UserController::class)->group(function () {
    Route::patch('/settings/update-profile', 'update');
    Route::patch('/settings/update-password', 'update_password');
    Route::patch('/settings/update-email', 'update_email');
    Route::patch('/user/update-profile-image', 'update_profile_picture');
    Route::post('/receiver/account-number', 'receiver');
    Route::get('/user/recents', 'recents');
});

Route::controller(CardController::class)->group(function () {
    Route::get('/account/cards', 'index');
    Route::post('/account/cards/', 'create');
    Route::delete('/account/cards/{id}', 'destroy');
});

Route::controller(DepositController::class)->group(function () {
    Route::get('/account/deposits', 'index');
    Route::get('/account/deposits/recents', 'recents');
    Route::post('/account/deposits', 'create');
});

Route::controller(TransferController::class)->group(function () {
    Route::get('/account/transfers', 'index');
    Route::get('/account/transfers/recents', 'recents');
    Route::post('/account/transfers', 'create');
});

Route::controller(MessageController::class)->group(function () {
    Route::get('/account/messages', 'index');
    Route::patch('/account/messages/{id}', 'update');
});

