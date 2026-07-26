<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Structures;

use AndyDefer\PhpClient\Abstracts\Struct;

/**
 * Structure for a successful IP geolocation response.
 *
 * Contains all geographic and network information about an IP address.
 */
final class IpGeoSuccessStruct extends Struct
{
    public function __construct(
        public readonly string $country,
        public readonly string $countryCode,
        public readonly string $region,
        public readonly string $regionName,
        public readonly string $city,
        public readonly string $zip,
        public readonly float $lat,
        public readonly float $lon,
        public readonly string $timezone,
        public readonly string $isp,
        public readonly string $org,
        public readonly string $as,
        public readonly string $query,
    ) {}
}
