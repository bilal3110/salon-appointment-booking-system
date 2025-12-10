<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Frontend Routes
Route::get('/',[FrontendController::class,'index'])->name('frontend.index');
Route::get('/booking',[FrontendController::class,'booking'])->name('frontend.booking');

// Dashboard Route
Route::get('/admin-panel',[DashboardController::class,'index'])->name('dashboard.index');
Route::get('/admin-panel/services',[DashboardController::class,'servicePage'])->name('dashboard.services');
Route::get('/admin-panel/services/create',[DashboardController::class,'createServicePage'])->name('dashboard.services.create');
Route::get('/admin-panel/services/{id}/edit',[DashboardController::class,'editServicePage'])->name('dashboard.services.edit');
Route::get('/admin-panel/users',[DashboardController::class,'addUserPage'])->name('dashboard.users');
Route::get('/admin-panel/users/create',[DashboardController::class,'createUserPage'])->name('dashboard.users.create');
Route::get('/admin-panel/users/{id}/edit',[DashboardController::class,'editUserPage'])->name('dashboard.users.edit');
Route::get('/admin-panel/staff',[DashboardController::class,'addStaffPage'])->name('dashboard.staff');
Route::get('/admin-panel/staff/{id}/view',[DashboardController::class,'viewStaffPage'])->name('dashboard.staff.view');
Route::get('/admin-panel/staff/create',[DashboardController::class,'createStaffPage'])->name('dashboard.staff.create');
