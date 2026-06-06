<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Tests\Integration;

use AndyDefer\PhpServices\Contracts\ModelTransformableInterface;
use AndyDefer\PhpServices\Services\ModelTransformableService;
use AndyDefer\PhpServices\Tests\IntegrationTestCase;

final class PhpServiceServiceProviderTest extends IntegrationTestCase
{
    public function test_service_provider_registers_model_transformable_interface(): void
    {
        // Act
        $instance = app(ModelTransformableInterface::class);

        // Assert
        $this->assertInstanceOf(ModelTransformableService::class, $instance);
    }

    public function test_service_provider_registers_singleton_instance(): void
    {
        // Act
        $instance1 = app(ModelTransformableInterface::class);
        $instance2 = app(ModelTransformableInterface::class);

        // Assert
        $this->assertSame($instance1, $instance2);
    }
}
