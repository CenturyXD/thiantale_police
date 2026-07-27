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
use App\Http\Controllers\Api\Admin\MainOContentController;
use App\Http\Controllers\Api\Admin\SectionController;
use App\Http\Controllers\Api\Admin\SlotController;
use App\Http\Controllers\Api\Admin\FacebookController;
use App\Http\Controllers\Api\Admin\UserManagementController;

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
Route::apiResource('main-o-contents', MainOContentController::class);
Route::apiResource('sub-o-contents', SubOContentController::class);
Route::apiResource('sections', SectionController::class);
Route::apiResource('slots', SlotController::class);

Route::post('search/subindis', [SubindiController::class, 'getByIndi']);
Route::post('search/topics', [TopicController::class, 'getByTopicid']);
Route::post('search/os', [OsController::class, 'getByOsid']);
Route::post('search/o-contents', [OContentController::class, 'getByOsid']);
Route::post('search/sub-o-contents', [SubOContentController::class, 'getByOContentid']);

Route::apiResource('admin/indis', IndiController::class);
Route::apiResource('admin/subindis', SubindiController::class);
Route::apiResource('admin/topics', TopicController::class);
Route::apiResource('admin/os', OsController::class);
Route::apiResource('admin/o-contents', OContentController::class);
Route::apiResource('admin/main-o-contents', MainOContentController::class);
Route::apiResource('admin/sub-o-contents', SubOContentController::class);
Route::apiResource('admin/sections', SectionController::class);
Route::apiResource('admin/slots', SlotController::class);
//search by id
Route::post('admin/search/subindis', [SubindiController::class, 'getByIndi']);
Route::post('admin/search/topics', [TopicController::class, 'getByTopicid']);
Route::post('admin/search/os', [OsController::class, 'getByOsid']);
Route::post('admin/search/o-contents', [OContentController::class, 'getByOContentid']);
Route::post('admin/search/main-o-contents', [MainOContentController::class, 'getByOContentid']);
Route::post('admin/search/sub-o-contents', [SubOContentController::class, 'getBySubOContentid']);

// Route::get('admin/contents/sections', [AdminContentController::class, 'getSections']); // ดูรายการ sections
// Route::post('admin/contents/editor-image', [AdminContentController::class, 'uploadEditorImage']);
// Route::delete('admin/contents/{content}/images/{contentImage}', [AdminContentController::class, 'destroyEditorImage']);
// Route::apiResource('admin/contents', AdminContentController::class);

Route::apiResource('facebook', FacebookController::class);

//protected routes (ต้อง login ก่อน)
Route::prefix('admin')->middleware(['auth:sanctum', 'active'])->group(function () {
    Route::get('/test', function () {
        return response()->json(['message' => 'Admin test route']);
    });

    // Content Management
    Route::get('contents/sections', [AdminContentController::class, 'getSections']); // ดูรายการ sections
    Route::post('contents/editor-image', [AdminContentController::class, 'uploadEditorImage']);
    Route::delete('contents/{content}/images/{contentImage}', [AdminContentController::class, 'destroyEditorImage']);
    Route::apiResource('contents', AdminContentController::class);

    // User Management
    Route::apiResource('users', UserManagementController::class);

    // Facebook Management
    

    // Route::apiResource('indis', IndiController::class);
    // Route::apiResource('subindis', SubindiController::class);
    // Route::apiResource('topics', TopicController::class);
    // Route::apiResource('os', OsController::class);
    // Route::apiResource('o-contents', OContentController::class);
    // Route::apiResource('main-o-contents', MainOContentController::class);
    // Route::apiResource('sub-o-contents', SubOContentController::class);
    // Route::apiResource('sections', SectionController::class);
    // Route::apiResource('slots', SlotController::class);
    // //search by id
    // Route::post('search/subindis', [SubindiController::class, 'getByIndi']);
    // Route::post('search/topics', [TopicController::class, 'getByTopicid']);
    // Route::post('search/os', [OsController::class, 'getByOsid']);
    // Route::post('search/o-contents', [OContentController::class, 'getByOContentid']);
    // Route::post('search/main-o-contents', [MainOContentController::class, 'getByOContentid']);
    // Route::post('search/sub-o-contents', [SubOContentController::class, 'getBySubOContentid']);


    // Content Management
    // Route::get('contents/sections', [AdminContentController::class, 'getSections']); // ดูรายการ sections
    // Route::post('contents/editor-image', [AdminContentController::class, 'uploadEditorImage']);
    // Route::delete('contents/{content}/images/{contentImage}', [AdminContentController::class, 'destroyEditorImage']);
    // Route::apiResource('contents', AdminContentController::class);
});
