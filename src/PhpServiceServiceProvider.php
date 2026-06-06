<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices;

use AndyDefer\PhpServices\Contracts\ModelTransformableInterface;
use AndyDefer\PhpServices\Services\ModelTransformableService;
use Illuminate\Support\ServiceProvider;

final class PhpServiceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            ModelTransformableInterface::class,
            ModelTransformableService::class
        );
    }

    public function boot(): void
    {
        // Configuration pour l'intégration Laravel
    }
}
