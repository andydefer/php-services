<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Responses;

use AndyDefer\PhpClient\Abstracts\Response;
use AndyDefer\PhpClient\Utils\EmptyStruct;
use AndyDefer\PhpServices\Structures\IpGeoFailureStruct;
use AndyDefer\PhpServices\Structures\IpGeoSuccessStruct;

/**
 * Response for IP geolocation.
 *
 * Parses the ip-api.com response and provides structured access to the data.
 */
final class IpGeoResponse extends Response
{
    /**
     * Checks if the geolocation request was successful.
     *
     * @return bool True if successful, false otherwise
     */
    public function isSuccess(): bool
    {
        $data = $this->getBody()->format();

        return isset($data['status']) && $data['status'] === 'success';
    }

    /**
     * Checks if the geolocation request failed.
     *
     * @return bool True if failed, false otherwise
     */
    public function isFailure(): bool
    {
        return ! $this->isSuccess();
    }

    /**
     * Gets the success data if the request was successful.
     *
     * @return IpGeoSuccessStruct|null The success data or null if not successful
     */
    public function getSuccessData(): ?IpGeoSuccessStruct
    {
        if (! $this->isSuccess()) {
            return null;
        }

        $data = $this->getBody()->format();

        return IpGeoSuccessStruct::from([
            'country' => $data['country'] ?? '',
            'countryCode' => $data['countryCode'] ?? '',
            'region' => $data['region'] ?? '',
            'regionName' => $data['regionName'] ?? '',
            'city' => $data['city'] ?? '',
            'zip' => $data['zip'] ?? '',
            'lat' => (float) ($data['lat'] ?? 0.0),
            'lon' => (float) ($data['lon'] ?? 0.0),
            'timezone' => $data['timezone'] ?? '',
            'isp' => $data['isp'] ?? '',
            'org' => $data['org'] ?? '',
            'as' => $data['as'] ?? '',
            'query' => $data['query'] ?? '',
        ]);
    }

    /**
     * Gets the failure data if the request failed.
     *
     * @return IpGeoFailureStruct|null The failure data or null if successful
     */
    public function getFailureData(): ?IpGeoFailureStruct
    {
        if ($this->isSuccess()) {
            return null;
        }

        $data = $this->getBody()->format();

        return IpGeoFailureStruct::from([
            'message' => $data['message'] ?? 'Unknown error',
            'query' => $data['query'] ?? null,
        ]);
    }

    /**
     * Gets the error message if the request failed.
     *
     * @return string|null The error message or null if successful
     */
    public function getErrorMessage(): ?string
    {
        if ($this->isSuccess()) {
            return null;
        }

        $data = $this->getBody()->format();

        return $data['message'] ?? 'Unknown error';
    }

    /**
     * Gets the queried IP address.
     *
     * @return string|null The queried IP address or null if not available
     */
    public function getQuery(): ?string
    {
        $data = $this->getBody()->format();

        return $data['query'] ?? null;
    }

    public static function getStructClass(): string
    {
        return EmptyStruct::class;
    }
}
