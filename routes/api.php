<?php

 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\AuthenticationControllerAPI;
use App\Http\Controllers\Api\documentControllerAPI;
use App\Http\Controllers\Api\requestApiController;
use App\Http\Controllers\Api\requestControllerAPI;
use App\Http\Controllers\Api\CollectionControllerAPI;
use App\Http\Controllers\Api\notificationControllerAPI;

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
Route::post('/login', [AuthenticationControllerAPI::class, 'login']);
Route::post('/findPhoneMatch', [AuthenticationControllerAPI::class, 'findPhoneMatch']);
Route::post('/findEmailMatch', [AuthenticationControllerAPI::class, 'findEmailMatch']);
Route::post('/register', [ApiController::class, 'register']);
Route::post('/uploadImages', [ApiController::class, 'uploadImages']);

Route::post('/submitRequestRecord', [requestApiController::class, 'enterRequestRecord']);
Route::post('/uploadRequirements', [requestApiController::class, 'uploadRequirements']);


Route::post('/fetchDocuments', [documentControllerAPI::class, 'fetch']);
Route::post('/fetchNotifications', [notificationControllerAPI::class, 'fetch']);
Route::post('/updateNotifications', [notificationControllerAPI::class, 'update']);


Route::post('/fetchRequests', [requestControllerAPI::class, 'fetch']);
Route::post('/storeRequest', [requestControllerAPI::class, 'store']);
Route::post('/cancelRequest', [requestControllerAPI::class, 'cancel']);
Route::post('/updateRequest',[requestApiController::class, 'updateRequestRecord']);
Route::post('/getRequirementURLs', [requestControllerAPI::class, 'fetchFileURLs']);

Route::post('/fetchCollections', [CollectionControllerAPI::class, 'fetch']);
Route::post('/storeCollection', [CollectionControllerAPI::class, 'store']);
Route::post('/confirmCollection', [CollectionControllerAPI::class, 'confirm']);
Route::post('/cancelCollection', [CollectionControllerAPI::class, 'cancel']);



