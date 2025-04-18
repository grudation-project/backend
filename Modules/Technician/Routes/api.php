<?php

use App\Helpers\GeneralHelper;
use Illuminate\Support\Facades\Route;
use Modules\Technician\Http\Controllers\ManagerTechnicianController;

Route::group(['prefix' => 'managers', 'middleware' => GeneralHelper::managerMiddlewares()], function(){
    Route::apiResource('technicians', ManagerTechnicianController::class);
});
