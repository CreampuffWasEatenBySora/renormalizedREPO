<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminPanel\AdminController;
use App\Http\Middleware\RoleMiddleWare;
use App\Http\Controllers\ResidentPanel\ResidentController;
use App\Http\Controllers\base_residentController;

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

Route::get('/', function () {
    return view('welcome');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/admin/home',[AdminController::class,'index'])->name('admin.home');
    Route::get('/admin/residents',[ResidentController::class,'index'])->name('admin.list_residents');
    Route::get('/admin/residents/view_resident',[ResidentController::class,'investigate'])->name('admin.view_resident');
    Route::get('/resident/home',[base_residentController::class,'index'])->name('resident.home');

    
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'role:A'])->name('dashboard');
require __DIR__.'/auth.php';

 