<?php

use Modules\Auth\Strategies\EmailVerificationStrategy;
use Modules\Auth\Strategies\OTPVerificationStrategy;

return [
    'verify' => [
        'enabled' => env('VERIFY_ENABLED', true),
        'strategy' => env('VERIFY_STRATEGY', 'email'),
        'strategies' => [
            'email' => [
                'class' => EmailVerificationStrategy::class,
            ],
            'otp' => [
                'class' => OTPVerificationStrategy::class,
            ],
        ],
    ],
];
