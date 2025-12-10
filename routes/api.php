<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\UserController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Service Routes
Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{id}', [ServiceController::class, 'show'])->name('services.show');
Route::put('/services/{id}', [ServiceController::class, 'update'])->name('services.update');
Route::delete('/services/{id}', [ServiceController::class, 'destroy'])->name('services.destroy');

// User Routes
Route::get('/users', [UserController::class, 'index'])->name('users');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

// Staff Routes
Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
Route::get('/staff/{id}', [StaffController::class, 'show'])->name('staff.show');
Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->name('staff.destroy');
Route::post('/staff/{id}', [StaffController::class, 'update'])->name('staff.update');

//Booking Routes
Route::get('/bookings',[BookingController::class,'index'])->name('bookings.index');
Route::post('/bookings',[BookingController::class,'store'])->name('bookings.store');
Route::get('/bookings/{id}',[BookingController::class,'show'])->name('bookings.show');
Route::post('/bookings/{id}',[BookingController::class,'update'])->name('bookings.update');
Route::delete('/bookings/{id}',[BookingController::class,'destroy'])->name('bookings.destroy');
Route::get('/bookings/{id}/confirm',[BookingController::class,'confirm'])->name('bookings.confirm');
Route::get('/bookings/{id}/cancel',[BookingController::class,'cancel'])->name('bookings.cancel');
