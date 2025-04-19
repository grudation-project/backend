<?php

namespace Modules\FcmNotification\Helpers;

class FcmNotificationHelper
{
    public static function generateAccessToken()
    {
        return cache()->remember('FIREBASE_ACCESS_TOKEN', 3600, function () {
            $client = new \Google_Client;
            $client->setAuthConfig(self::getFirebaseConfigPath());
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

            $token = $client->fetchAccessTokenWithAssertion();

            return $token['access_token'];
        });
    }

    public static function getFirebaseConfig()
    {
        $configFile = file_get_contents(self::getFirebaseConfigPath());

        return json_decode($configFile, true);
    }

    public static function getFirebaseConfigPath(): string
    {
        return base_path(env('FIREBASE_CREDENTIALS', 'Modules/FcmNotification/firebase-config.json'));
    }

    public static function getProjectId()
    {
        return cache()->rememberForever('FIREBASE_PROJECT_ID', function () {
            return self::getFirebaseConfig()['project_id'];
        });
    }
}
