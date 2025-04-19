<?php

use App\Helpers\GeneralHelper;
use Illuminate\Support\Facades\Route;
use Modules\FcmNotification\Http\Controllers\NotificationController;

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

Route::group(['middleware' => [GeneralHelper::authMiddleware()]], function () {
    Route::patch('fcm_token', [NotificationController::class, 'updateToken']);

    Route::get('unread_notifications_count', [NotificationController::class, 'unreadNotificationsCount']);
    Route::post('notify', [NotificationController::class, 'notifyUsers'])->middleware(GeneralHelper::adminMiddlewares());
    Route::get('', [NotificationController::class, 'index']);
    Route::patch('', [NotificationController::class, 'markAllAsRead']);
    Route::delete('', [NotificationController::class, 'destroyAll']);
    Route::patch('{notification}', [NotificationController::class, 'markOneAsRead']);
    Route::delete('{notification}', [NotificationController::class, 'destroyOne']);
});

//if(! app()->isProduction()) {
    Route::post('generate_firebase_token', function () {
        $client = new Google_Client();

        $client->setAuthConfig(base_path(env('FIREBASE_CREDENTIALS')));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->setApplicationName("FAKE_NAME");
        $client->fetchAccessTokenWithAssertion();

        return $client->getAccessToken();
    });
//}
