<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DocumentController;
use App\Http\Controllers\API\CollaborationController;
use App\Http\Controllers\API\InvitationController;
use App\Http\Controllers\API\DocumentSearchController;



// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

    Route::post('register',[AuthController::class,'register']);
    Route::post('login',[AuthController::class,'login']);

Route::middleware(['auth:sanctum', 'throttle:sensitive-api'])->group(function () {
    Route::post('logout',[AuthController::class,'logout']);
    Route::apiResource('documents', DocumentController::class);

    Route::get('/documents/{document}/versions', [DocumentController::class, 'versions']);
    Route::post('/documents/{document}/revert/{version}', [DocumentController::class, 'revert']);
    // Shared documents
    Route::get('/shared-documents', [DocumentController::class, 'shared']);


    // invitation routes
    Route::get('/invitations', [InvitationController::class, 'index']);
    Route::post('/documents/{document}/invite', [InvitationController::class, 'store']);
    Route::post('/invitations/{invitation}/accept', [InvitationController::class, 'accept']);
    Route::post('/invitations/{invitation}/decline', [InvitationController::class, 'decline']);
    Route::post('documents/{document}/revoke/{user}',[InvitationController::class,'revoke']);

    Route::get('/search', DocumentSearchController::class);

});


