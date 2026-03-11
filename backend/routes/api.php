<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\Admin\ContentController as AdminContentController;


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

//test route
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working',
    ]);
});

Route::get('/phpinfo', function () {
    phpinfo();
    return;
});

//auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//public routes
Route::get('contents', [AdminContentController::class, 'index']);

//protected routes (ต้อง login ก่อน)
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/test', function () {
        return response()->json(['message' => 'Admin test route']);
    });

    // Content Management
    Route::get('contents/sections', [AdminContentController::class, 'getSections']); // ดูรายการ sections
    Route::post('contents/editor-image', [AdminContentController::class, 'uploadEditorImage']);
    Route::delete('contents/{content}/images/{contentImage}', [AdminContentController::class, 'destroyEditorImage']);
    Route::apiResource('contents', AdminContentController::class);
});
