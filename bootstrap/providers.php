<?php

use App\Providers\BroadcastServiceProvider;

return [
    \App\Providers\AppServiceProvider::class,
    \App\Providers\RouteServiceProvider::class,
    BroadcastServiceProvider::class,
];
