<?php

use App\Helpers\GeneralHelper;
use Illuminate\Support\Facades\Route;
use Modules\Chat\Http\Controllers\ConversationController;
use Modules\Chat\Http\Controllers\ConversationMessageController;
use Modules\Chat\Http\Controllers\ConversationMessagePinController;
use Modules\Chat\Http\Controllers\MessageReactionController;
use Modules\Chat\Http\Middleware\AllowedToChatMiddleware;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::group([
    'prefix' => 'conversations',
    'middleware' => array_merge(GeneralHelper::getDefaultLoggedUserMiddlewares(), [AllowedToChatMiddleware::class])],
    function () {
        Route::get('for', [ConversationController::class, 'getByUser']);
        Route::get('get_other_user', [ConversationController::class, 'otherUser']);
        Route::get('', [ConversationController::class, 'index']);
        Route::post('', [ConversationController::class, 'store']);
        Route::post('{conversationId}/pin', [ConversationController::class, 'pin']);
        Route::get('{conversationId}/pinned', [ConversationMessagePinController::class, 'index']);
        Route::patch('{conversationId}/seen', [ConversationController::class, 'markAsSeen']);
        Route::delete('{conversationId}', [ConversationController::class, 'destroy']);
        Route::get('unseen_count', [ConversationController::class, 'unseenCount']);

        Route::group(['prefix' => '{conversationId}/messages'], function () {
            Route::post('{id}/pin', [ConversationMessagePinController::class, 'pin']);
            Route::post('{id}/forward', [ConversationMessageController::class, 'forward']);
            Route::patch('{id}/change_reaction', MessageReactionController::class);
            Route::get('', [ConversationMessageController::class, 'index']);
            Route::post('', [ConversationMessageController::class, 'store']);
            Route::delete('{id}', [ConversationMessageController::class, 'destroy']);
        });
    }
);
