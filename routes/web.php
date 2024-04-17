<?php

use App\Http\Controllers\adminController;
use App\Http\Controllers\androidRegisterAPI;
use App\Http\Controllers\loginController;
use App\Http\Controllers\RequestRecordController;
use App\Http\Controllers\residentController;
use App\Http\Controllers\signupController;
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

Route::get('/', function () {
    return view('index');
});






Route::get('/signup/1_enterName', [signupController::class, 'stepOne'])->name('signup.1_enterName');
Route::get('/signup/2_enterAddress', [signupController::class, 'stepTwo'])->name('signup.2_enterAddress');
Route::get('/signup/3_enterLogIn', [signupController::class, 'stepThree'])->name('signup.3_enterLogIn');
Route::get('/signup/4_finishSignUp', [signupController::class, 'stepFour'])->name('signup.4_finishSignUp');
Route::get('/signup/5_confirmAccount', [signupController::class, 'stepFive'])->name('signup.5_confirmAccount');
Route::resource('signup', signupController::class);

Route::resource('login', loginController::class);

Route::resource('administrator', adminController::class);
Route::resource('residents', residentController::class);

Route::resource('requests', RequestRecordController::class);
