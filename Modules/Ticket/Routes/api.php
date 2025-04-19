<?php

use App\Helpers\GeneralHelper;
use Illuminate\Support\Facades\Route;
use Modules\Ticket\Http\Controllers\AdminTicketController;
use Modules\Ticket\Http\Controllers\ManagerTicketController;
use Modules\Ticket\Http\Controllers\TechnicianTicketController;
use Modules\Ticket\Http\Controllers\UserTicketController;

Route::group(['prefix' => 'users/tickets', 'middleware' => GeneralHelper::userMiddlewares()], function () {
    Route::get('', [UserTicketController::class, 'index']);
    Route::get('{id}', [UserTicketController::class, 'show']);
    Route::post('', [UserTicketController::class, 'store']);
    Route::put('{id}', [UserTicketController::class, 'update']);
});

Route::group(['prefix' => 'managers/tickets', 'middleware' => GeneralHelper::managerMiddlewares()], function () {
    Route::get('', [ManagerTicketController::class, 'index']);
    Route::get('{id}', [ManagerTicketController::class, 'show']);
    Route::post('{id}/assign', [ManagerTicketController::class, 'assign']);
    Route::post('{id}/finish', [ManagerTicketController::class, 'resolve']);
});

Route::group(['prefix' => 'technicians/tickets', 'middleware' => GeneralHelper::technicianMiddlewares()], function () {
    Route::get('', [TechnicianTicketController::class, 'index']);
    Route::get('{id}', [TechnicianTicketController::class, 'show']);
    Route::post('{id}/finish', [TechnicianTicketController::class, 'resolve']);
});

Route::group(['prefix' => 'admin/tickets', 'middleware' => GeneralHelper::adminMiddlewares()], function(){
   Route::get('', [AdminTicketController::class, 'index']);
   Route::get('{id}', [AdminTicketController::class, 'show']);
});
