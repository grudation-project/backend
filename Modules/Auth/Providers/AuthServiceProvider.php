<?php

namespace Modules\Auth\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Modules\Auth\Console\ClearExpiredToken;
use Modules\Auth\Helpers\VerifyConfigHelper;
use Modules\Auth\Strategies\Verifiable;
use Modules\Otp\Contracts\OtpContract;
use SocialiteProviders\Apple\Provider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AuthServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Auth';

    protected string $moduleNameLower = 'auth';

    protected string $configFileName = 'auth';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->commands([
            ClearExpiredToken::class,
        ]);

        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        if (VerifyConfigHelper::enabled()) {

            $this->registerVerifyUser();
        }

        $this->app->register(RouteServiceProvider::class);

        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('apple', Provider::class);
        });
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([
            module_path(
                $this->moduleName,
                "Config/$this->configFileName.php"
            ) => config_path($this->configFileName.'.php'),
        ], 'config');

        $this->mergeConfigFrom(
            module_path(
                $this->moduleName,
                "Config/$this->configFileName.php"
            ), $this->configFileName
        );
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', $this->moduleNameLower.'-module-views']);

        $this->loadViewsFrom(
            array_merge($this->getPublishableViewPaths(), [$sourcePath]),
            $this->moduleNameLower
        );
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (Config::get('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->moduleNameLower)) {
                $paths[] = $path.'/modules/'.$this->moduleNameLower;
            }
        }

        return $paths;
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        // Register Other Providers
        $this->app->register(ScheduleServiceProvider::class);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [

        ];
    }

    private function registerVerifyUser(): void
    {
        $this->app->singleton(
            Verifiable::class,
            fn () => $this->getVerificationStrategy(),
        );
    }

    private function getVerificationStrategy()
    {
        $defaultStrategy = VerifyConfigHelper::defaultStrategy();
        $class = VerifyConfigHelper::getStrategyClass($defaultStrategy);

        return match ($defaultStrategy) {
            'otp' => new $class(app(OtpContract::class)),
            default => new $class,
        };
    }
}
