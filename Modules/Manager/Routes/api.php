<?php

use App\Helpers\GeneralHelper;
use Illuminate\Support\Facades\Route;
use Modules\Manager\Http\Controllers\AdminManagerController;

Route::group(['prefix' => 'admin/managers', 'middleware' => GeneralHelper::adminMiddlewares()], function(){
    Route::get('', [AdminManagerController::class, 'index']);
    Route::get('{id}', [AdminManagerController::class, 'show']);
    Route::post('', [AdminManagerController::class, 'store']);
    Route::put('{id}', [AdminManagerController::class, 'update']);
    Route::delete('{id}', [AdminManagerController::class, 'destroy']);
});
