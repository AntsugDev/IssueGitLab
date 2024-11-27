<?php

use App\Http\Api\Auth\AuthController;
use App\Http\Api\Auth\GitLabController;
use App\Http\Api\Auth\KeycloakController;
use App\Http\Api\GitLab\Controllers\Board;
use App\Http\Api\GitLab\Controllers\DuplicateController;
use App\Http\Api\GitLab\Controllers\IssueController;
use App\Http\Api\GitLab\Controllers\LabelController;
use App\Http\Api\GitLab\Controllers\ProjectController;
use App\Http\Api\Jobs\FailedController;
use App\Http\Api\Menu\MenuController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

Route::prefix('auth')->group(function (){
    Route::post('',[AuthController::class,'auth']);
    Route::get('redirect',[KeycloakController::class,'index']);
    Route::get('user',[KeycloakController::class,'store']);
});


Route::middleware(['auth:api',\App\Http\Middleware\ValidateSso::class])->group(function (){

    Route::prefix('at')->group(function () {
        Route::get('refresh',[AuthController::class,'refresh']);
        Route::get('logout',[AuthController::class,'logout']);
    });
    Route::prefix('gitlab')->group(function (){
        Route::get('/{accessToken}',[GitLabController::class,'store']);
    });

    Route::prefix('data')->group(function () {
        Route::get('project', [ProjectController::class, 'index']);
        Route::get('project/search/{search}', [ProjectController::class, 'show']);
        Route::get('project/{id}', [ProjectController::class, 'check']);
        Route::post('duplicate', [DuplicateController::class, 'store']);
    });
    Route::resource('labels', LabelController::class)->only(['index']);
    Route::resource('boards', Board::class)->only(['index']);
    Route::post('issue', [IssueController::class,'store']);

    Route::resource('failed_jobs',FailedController::class)->only(['index','destroy','show']);



});


Route::any('/{any}', function () {
    return new JsonResponse(array("data"=>array("errors"=>"Route not found or method unsupported")), 404);
})->where('any', '.*');
