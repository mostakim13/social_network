<?php

use App\Http\Controllers\FollowPersonCOntroller;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserApiController;
use App\Models\FollowPerson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->group(function () {
    Route::put('/follow/person/{personId}', [UserApiController::class, 'followPerson']);
    Route::post('/page/create', [PageController::class, 'create']);
    Route::put('/follow/page/{pageId}', [UserApiController::class, 'followPage']);
    Route::post('/person/attach-post', [UserApiController::class, 'attachpost']);
    Route::post('/page/{pageId}/attach-post', [UserApiController::class, 'attachpagepost']);
    Route::get('/person/feed', [UserApiController::class, 'personfeed']);
});


Route::post('/auth/register', [UserApiController::class, 'register']);

Route::post('/auth/login', [UserApiController::class, 'login']);
