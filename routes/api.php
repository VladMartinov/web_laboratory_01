<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ExelController;
use App\Http\Controllers\RecordController;

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

Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);
Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('file', [ExelController::class, 'loadFile'])->middleware('auth:sanctum');

Route::post('/records', [RecordController::class, 'createRecord'])->middleware('auth:sanctum');
Route::get('/records', [RecordController::class, 'getRecords'])->middleware('auth:sanctum');
Route::put('/records', [RecordController::class, 'updateRecord'])->middleware('auth:sanctum');
Route::delete('/records/{classTypeRecordId}', [RecordController::class, 'deleteRecord'])->middleware('auth:sanctum');
