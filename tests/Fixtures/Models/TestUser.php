<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Tests\Fixtures\Models;

use AndyDefer\PhpServices\Tests\Fixtures\Enums\TestUserRole;
use AndyDefer\PhpServices\Tests\Fixtures\Enums\TestUserStatus;
use Illuminate\Database\Eloquent\Model;

class TestUser extends Model
{
    protected $table = 'test_users';

    protected $fillable = [
        'name',
        'email',
        'status',
        'role',
        'age',
        'metadata',
    ];

    protected $casts = [
        'status' => TestUserStatus::class,
        'role' => TestUserRole::class,
        'metadata' => 'array',
    ];

    public function getFullNameAttribute(): string
    {
        return $this->name.' ('.$this->email.')';
    }
}
