<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Tests\Fixtures\Data;

use AndyDefer\DomainStructures\Abstracts\AbstractData;
use AndyDefer\DomainStructures\Utils\StrictDataObject;
use AndyDefer\PhpServices\Tests\Fixtures\Enums\TestUserRole;
use AndyDefer\PhpServices\Tests\Fixtures\Enums\TestUserStatus;

final class TestUserData extends AbstractData
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly TestUserStatus $status,
        public readonly TestUserRole $role,
        public readonly ?int $age,
        public readonly ?StrictDataObject $metadata,
        public readonly string $created_at,
        public readonly string $updated_at,
    ) {}
}
