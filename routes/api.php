<?php

 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\requestApiController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
    
});
Route::post('/register', [ApiController::class, 'register']);
Route::post('/uploadImages', [ApiController::class, 'uploadImages']);

Route::post('/submitRequestRecord', [requestApiController::class, 'enterRequestRecord']);
Route::post('/uploadRequirements', [requestApiController::class, 'uploadRequirements']);
Route::post('/fetchRequest', [requestApiController::class, 'fetchRequestSet']);
Route::post('/updateRequest',[requestApiController::class, 'updateRequestRecord']);



