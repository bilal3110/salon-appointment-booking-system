<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TestimonialController;
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
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::post('/services', [ServiceController::class, 'store'])->name('services.store')->middleware('auth');
Route::get('/services/{id}', [ServiceController::class, 'show'])->name('services.show')->middleware('auth');
Route::put('/services/{id}', [ServiceController::class, 'update'])->name('services.update')->middleware('auth');
Route::delete('/services/{id}', [ServiceController::class, 'destroy'])->name('services.destroy')->middleware('auth');

// User Routes
Route::get('/users', [UserController::class, 'index'])->name('users')->middleware('auth');
Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('auth');
Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show')->middleware('auth');
Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update')->middleware('auth');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('auth');

// Staff Routes
Route::post('/staff', [StaffController::class, 'store'])->name('staff.store')->middleware('auth');
Route::get('/staff', [StaffController::class, 'index'])->name('staff.index')->middleware('auth');
Route::get('/staff/{id}', [StaffController::class, 'show'])->name('staff.show')->middleware('auth');
Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->name('staff.destroy')->middleware('auth');
Route::post('/staff/{id}', [StaffController::class, 'update'])->name('staff.update')->middleware('auth');

//Booking Routes
Route::get('/bookings',[BookingController::class,'index'])->name('bookings.index')->middleware('auth');
Route::post('/bookings',[BookingController::class,'store'])->name('bookings.store');
Route::get('/bookings/{id}',[BookingController::class,'show'])->name('bookings.show')->middleware('auth');
Route::post('/bookings/{id}',[BookingController::class,'update'])->name('bookings.update')->middleware('auth');
Route::delete('/bookings/{id}',[BookingController::class,'destroy'])->name('bookings.destroy')->middleware('auth');
Route::get('/bookings/{id}/confirm',[BookingController::class,'confirm'])->name('bookings.confirm')->middleware('auth');
Route::get('/bookings/{id}/cancel',[BookingController::class,'cancel'])->name('bookings.cancel')->middleware('auth');
Route::get('/staff-by-service/{service}', [BookingController::class, 'getStaffByService']);

// Testimonial Routes
Route::get('/testimonials', [TestimonialController::class, 'index'])->name('testimonials.index');
Route::post('/testimonials', [TestimonialController::class, 'store'])->name('testimonials.store');
Route::delete('/testimonials/{id}', [TestimonialController::class, 'delete'])->name('testimonials.delete')->middleware('auth');

