<?php

namespace Ajnorman\CfTurnstileLaravelMiddleware;

use Ajnorman\CfTurnstileLaravelMiddleware\View\TurnstileComponent;
use Illuminate\Support\Facades\Blade;
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

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cf-turnstile');

        Blade::component('turnstile', TurnstileComponent::class);
    }
}
