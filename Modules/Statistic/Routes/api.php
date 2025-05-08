<?php

use App\Helpers\GeneralHelper;
use Illuminate\Support\Facades\Route;
use Modules\Statistic\Http\Controllers\StatisticController;

Route::get('admin/statistics', [StatisticController::class, 'admin'])->middleware(GeneralHelper::adminMiddlewares());
Route::get('users/statistics', [StatisticController::class, 'user'])->middleware(GeneralHelper::userMiddlewares());
Route::get('managers/statistics', [StatisticController::class, 'manager'])->middleware(GeneralHelper::managerMiddlewares());
Route::get('technicians/statistics', [StatisticController::class, 'technician'])->middleware(GeneralHelper::technicianMiddlewares());
