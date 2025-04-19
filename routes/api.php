<?php

use App\Helpers\GeneralHelper;
use Illuminate\Support\Facades\Route;
use Modules\Service\Http\Controllers\SelectMenuController;

Route::group(['prefix' => 'api/select_menu'], function(){
    Route::get('services', [SelectMenuController::class, 'services']);
    Route::get('technicians', [SelectMenuController::class, 'technicians'])->middleware(GeneralHelper::managerMiddlewares());
});
