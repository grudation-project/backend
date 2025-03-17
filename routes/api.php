<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Service\Http\Controllers\SelectMenuController;

Route::group(['prefix' => 'api/select_menu'], function(){
    Route::get('services', [SelectMenuController::class, 'services']);
});
