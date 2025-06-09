<?php

use App\Helpers\GeneralHelper;
use Illuminate\Support\Facades\Route;
use Modules\Service\Http\Controllers\AdminSectionController;
use Modules\Service\Http\Controllers\AdminServiceController;

Route::group(['prefix' => 'admin/services', 'middleware' => GeneralHelper::adminMiddlewares()], function () {
    Route::get('', [AdminServiceController::class, 'index']);
    Route::get('{id}', [AdminServiceController::class, 'show']);
    Route::post('', [AdminServiceController::class, 'store']);
    Route::put('{id}', [AdminServiceController::class, 'update']);
    Route::delete('{id}', [AdminServiceController::class, 'destroy']);

    Route::apiResource('{serviceId}/sections', AdminSectionController::class);
});
