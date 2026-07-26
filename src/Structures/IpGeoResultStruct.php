<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Structures;

use AndyDefer\PhpClient\Abstracts\Struct;

/**
 * Structure for IP geolocation result.
 *
 * Contains either success data or failure information.
 */
final class IpGeoResultStruct extends Struct
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $country = null,
        public readonly ?string $countryCode = null,
        public readonly ?string $region = null,
        public readonly ?string $regionName = null,
        public readonly ?string $city = null,
        public readonly ?string $zip = null,
        public readonly ?float $lat = null,
        public readonly ?float $lon = null,
        public readonly ?string $timezone = null,
        public readonly ?string $isp = null,
        public readonly ?string $org = null,
        public readonly ?string $as = null,
        public readonly ?string $query = null,
        public readonly ?string $message = null,
    ) {}

    /**
     * Creates a success result.
     */
    public static function success(
        string $country,
        string $countryCode,
        string $region,
        string $regionName,
        string $city,
        string $zip,
        float $lat,
        float $lon,
        string $timezone,
        string $isp,
        string $org,
        string $as,
        string $query,
    ): self {
        return new self(
            success: true,
            country: $country,
            countryCode: $countryCode,
            region: $region,
            regionName: $regionName,
            city: $city,
            zip: $zip,
            lat: $lat,
            lon: $lon,
            timezone: $timezone,
            isp: $isp,
            org: $org,
            as: $as,
            query: $query,
        );
    }

    /**
     * Creates a failure result.
     */
    public static function failure(string $message, ?string $query = null): self
    {
        return new self(
            success: false,
            message: $message,
            query: $query,
        );
    }
}
