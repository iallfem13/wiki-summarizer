<?php

use App\Enums\DocumentStatus;
use App\Http\Controllers\DocumentController;
use App\Http\Middleware\VerifyInternalSecret;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// webhook result from python
Route::post('/result', [DocumentController::class, 'result'])
    ->middleware(VerifyInternalSecret::class);

// send document information to the backend 
Route::post('/submit', [DocumentController::class, 'store']);

// get the list of known document statuses
Route::get('/document-statuses', function () {
    return array_column(DocumentStatus::cases(), 'value');
});

// get list of documents
Route::get('/documents', [DocumentController::class, 'documents']);
