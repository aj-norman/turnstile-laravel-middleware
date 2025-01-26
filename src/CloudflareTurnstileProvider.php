<?php

namespace Ajnorman\CfTurnstileLaravelMiddleware;

use Illuminate\Support\ServiceProvider;

class CloudflareTurnstileProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/cf-turnstile.php' => config_path('cf-turnstile.php')
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/../config/cf-turnstile.php', 'cf-turnstile'
        );
    }
}
