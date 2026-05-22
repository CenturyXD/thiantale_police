<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\Admin\ContentController as AdminContentController;
use App\Http\Controllers\Api\Admin\IndiController;
use App\Http\Controllers\Api\Admin\SubindiController;
use App\Http\Controllers\Api\Admin\TopicController;
use App\Http\Controllers\Api\Admin\OsController;
use App\Http\Controllers\Api\Admin\OContentController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\Admin\SubOContentController;


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
Route::get('public/indis', [IndiController::class, 'index']); // ดูรายการ indi แบบ public
Route::get('public/indis/{indi}', [IndiController::class, 'show']); // ดูรายละเอียด indi แบบ public

// Route::get('contents', [AdminContentController::class, 'index']);
Route::apiResource('contents', AdminContentController::class);

Route::apiResource('indis', IndiController::class);
Route::apiResource('subindis', SubindiController::class);
Route::apiResource('topics', TopicController::class);
Route::apiResource('os', OsController::class);
Route::apiResource('o-contents', OContentController::class);
Route::apiResource('sub-o-contents', SubOContentController::class);



//protected routes (ต้อง login ก่อน)
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/test', function () {
        return response()->json(['message' => 'Admin test route']);
    });

    Route::apiResource('indis', IndiController::class);
    Route::apiResource('subindis', SubindiController::class);
    Route::apiResource('topics', TopicController::class);
    Route::apiResource('os', OsController::class);
    Route::apiResource('o-contents', OContentController::class);
    Route::apiResource('sub-o-contents', SubOContentController::class);

    // Content Management
    Route::get('contents/sections', [AdminContentController::class, 'getSections']); // ดูรายการ sections
    Route::post('contents/editor-image', [AdminContentController::class, 'uploadEditorImage']);
    Route::delete('contents/{content}/images/{contentImage}', [AdminContentController::class, 'destroyEditorImage']);
    Route::apiResource('contents', AdminContentController::class);
});
