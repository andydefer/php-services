<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Structures;

use AndyDefer\PhpClient\Abstracts\Struct;

/**
 * Structure for a failed IP geolocation response.
 */
final class IpGeoFailureStruct extends Struct
{
    public function __construct(
        public readonly string $message,
        public readonly ?string $query = null,
    ) {}
}
