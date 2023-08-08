<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return redirect()->route('admin');
});

Route::get('/admin', [AdminController::class, 'index'])->middleware('auth')->name('admin');

// Signup
Route::get('/signup', [UserController::class, 'showSignupForm'])->name('signup.form');
Route::post('/signup', [UserController::class, 'signup'])->name('signup.submit');

// Login
Route::get('/login', [UserController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('/login', [UserController::class, 'login'])->name('login.submit');
Route::get('/logout', [UserController::class, 'logout'])->name('logout');

// Admin Profile Update
Route::get('/admin/profile/update', [AdminController::class, 'showUpdateProfileForm'])->name('admin.profile.form');
Route::post('/admin/profile/update', [AdminController::class, 'updateProfile'])->name('admin.update');

// Change Password
Route::get('/change-password', [UserController::class, 'showChangePasswordForm'])->name('password.form');
Route::post('/change-password', [UserController::class, 'changePassword'])->name('password.change');
