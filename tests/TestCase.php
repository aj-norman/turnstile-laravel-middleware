<?php

namespace Ajnorman\CfTurnstileLaravelMiddleware\Tests;

use Ajnorman\CfTurnstileLaravelMiddleware\CloudflareTurnstileProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            CloudflareTurnstileProvider::class,
        ];
    }
}
