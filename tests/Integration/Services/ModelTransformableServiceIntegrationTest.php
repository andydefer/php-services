<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Tests\Integration\Services;

use AndyDefer\DomainStructures\Utils\StrictDataObject;
use AndyDefer\PhpServices\Services\ModelTransformableService;
use AndyDefer\PhpServices\Tests\Fixtures\Data\TestUserData;
use AndyDefer\PhpServices\Tests\Fixtures\Enums\TestUserRole;
use AndyDefer\PhpServices\Tests\Fixtures\Enums\TestUserStatus;
use AndyDefer\PhpServices\Tests\Fixtures\Models\TestUser;
use AndyDefer\PhpServices\Tests\IntegrationTestCase;

final class ModelTransformableServiceIntegrationTest extends IntegrationTestCase
{
    private ModelTransformableService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ModelTransformableService;
    }

    public function test_to_data_converts_database_model_correctly(): void
    {
        // Arrange
        $user = TestUser::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'status' => TestUserStatus::ACTIVE->value,
            'role' => TestUserRole::USER->value,
            'age' => 25,
            'metadata' => ['premium' => true, 'score' => 100],
        ]);

        // Act
        $result = $this->service->toData($user, TestUserData::class);

        // Assert
        $this->assertSame($user->id, $result->id);
        $this->assertSame('Jane Doe', $result->name);
        $this->assertSame('jane@example.com', $result->email);
        $this->assertSame(TestUserStatus::ACTIVE, $result->status);
        $this->assertSame(TestUserRole::USER, $result->role);
        $this->assertSame(25, $result->age);
        $this->assertInstanceOf(StrictDataObject::class, $result->metadata);

        // ✅ Bon : la valeur de 'premium' est true (booléen)
        $this->assertTrue($result->metadata->premium);
        $this->assertSame(100, $result->metadata->score);
    }

    public function test_to_data_collection_converts_multiple_models(): void
    {
        // Arrange - ne pas encoder manuellement
        TestUser::create([
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'status' => TestUserStatus::ACTIVE->value,
            'role' => TestUserRole::USER->value,
            'metadata' => [],  // ← Tableau vide
        ]);

        TestUser::create([
            'name' => 'User 2',
            'email' => 'user2@example.com',
            'status' => TestUserStatus::INACTIVE->value,
            'role' => TestUserRole::GUEST->value,
            'metadata' => [],  // ← Tableau vide
        ]);

        $users = TestUser::all();

        // Act
        $result = $this->service->toDataCollection($users, TestUserData::class);

        // Assert
        $this->assertCount(2, $result);
        $this->assertInstanceOf(TestUserData::class, $result[0]);
        $this->assertInstanceOf(TestUserData::class, $result[1]);
    }

    public function test_to_data_handles_null_metadata(): void
    {
        // Arrange
        $user = TestUser::create([
            'name' => 'Null User',
            'email' => 'null@example.com',
            'status' => TestUserStatus::ACTIVE->value,
            'role' => TestUserRole::USER->value,
            'age' => null,
            'metadata' => null,
        ]);

        // Act
        $result = $this->service->toData($user, TestUserData::class);

        // Assert
        $this->assertSame($user->id, $result->id);
        $this->assertSame('Null User', $result->name);
        $this->assertNull($result->age);
        $this->assertNull($result->metadata);
    }
}
