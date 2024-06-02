<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminPanel\AdminController;
use App\Http\Controllers\AdminPanel\collectionController;
use App\Http\Middleware\RoleMiddleWare;
use App\Http\Controllers\ResidentPanel\ResidentController;
use App\Http\Controllers\AdminPanel\documentController;
use App\Http\Controllers\AdminPanel\requestRecordController;
use App\Http\Controllers\AdminPanel\requirementController;
use App\Http\Controllers\base_residentController;
use App\Http\Controllers\fileController;

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
    Route::get('/admin/residents/view_resident/verify',[ResidentController::class,'updateRegistration'])->name('admin.verify_resident');
   
    Route::get('/admin/documents',[documentController::class,'index'])->name('admin.list_documents');
    Route::get('/admin/documents/view_document',[documentController::class,'check'])->name('admin.view_document');
    Route::get('/admin/documents/view_document/modify',[documentController::class,'update'])->name('admin.modify_document');
    Route::get('/admin/documents/create',[documentController::class,'create'])->name('admin.create_document');
    Route::get('/admin/documents/create/store',[documentController::class,'store'])->name('admin.store_document');
   
    Route::get('/admin/requirements',[requirementController::class,'index'])->name('admin.list_requirements');
    Route::get('/admin/requirements/view_requirement',[requirementController::class,'investigate'])->name('admin.view_requirement');
    Route::get('/admin/requirements/view_requirement/modify',[requirementController::class,'update'])->name('admin.modify_requirement');
    Route::get('/admin/requirementss/create',[requirementController::class,'create'])->name('admin.create_requirement');
    Route::get('/admin/requirementss/create/store',[requirementController::class,'store'])->name('admin.store_requirement');
   

    Route::get('/admin/requests',[requestRecordController::class,'index'])->name('admin.list_requests');
    Route::get('/admin/requests/view_requests',[requestRecordController::class,'check'])->name('admin.view_request');
    Route::get('/admin/requests/view_requests/confirm',[requestRecordController::class,'update'])->name('admin.modify_request');
   
    Route::get('/admin/collections',[collectionController::class,'index'])->name('admin.list_collections');
    Route::get('/admin/requests/view_requests',[requestRecordController::class,'check'])->name('admin.view_request');
    Route::get('/admin/requests/view_requests/confirm',[requestRecordController::class,'update'])->name('admin.modify_request');
    
   
    


    Route::get('/resident/home',[base_residentController::class,'index'])->name('resident.home');

    
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'role:A'])->name('dashboard');
require __DIR__.'/auth.php';



Route::get('private/{category}/{categoryCode}/{fileName}',[fileController::class,'get'])->middleware('auth')->name('files.get') ;
