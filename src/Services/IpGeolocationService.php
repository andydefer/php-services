<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Services;

use AndyDefer\PhpClient\Clients\ClientService;
use AndyDefer\PhpClient\Enums\ContentType;
use AndyDefer\PhpServices\Requests\IpGeoRequest;
use AndyDefer\PhpServices\Responses\IpGeoResponse;
use AndyDefer\PhpServices\Structures\IpGeoSuccessStruct;
use InvalidArgumentException;

/**
 * Service for IP geolocation using ip-api.com.
 *
 * This service provides methods to retrieve geolocation data from an IP address
 * using the free ip-api.com API. It handles both IPv4 and IPv6 addresses and
 * returns structured data about the location, ISP, and timezone.
 *
 * @link https://ip-api.com/docs/api:json Official ip-api.com documentation
 */
class IpGeolocationService
{
    private ClientService $client;

    /**
     * Constructor.
     *
     * @param  ClientService|null  $client  HTTP client instance (optional)
     */
    public function __construct(?ClientService $client = null)
    {
        $this->client = $client ?? new ClientService;
    }

    /**
     * Retrieves geolocation data for a given IP address.
     *
     * @param  string  $ip  The IP address to locate (IPv4 or IPv6)
     * @return IpGeoSuccessStruct The geolocation data
     *
     * @throws InvalidArgumentException When the IP address is empty or invalid
     * @throws \RuntimeException When the geolocation request fails
     */
    public function locate(string $ip): IpGeoSuccessStruct
    {
        if (empty($ip)) {
            throw new InvalidArgumentException('IP address cannot be empty.');
        }

        if (! $this->isValidIp($ip)) {
            throw new InvalidArgumentException(sprintf('Invalid IP address: %s', $ip));
        }

        $request = new IpGeoRequest($ip);

        $request->getHeaders()
            ->setAccept(ContentType::JSON);

        $request->getOptions()
            ->setTimeout(20)
            ->setConnectTimeout(5)
            ->setHttpErrors(false);

        /** @var IpGeoResponse $response */
        $response = $this->client->get(
            $request->getUrl()->getValue(),
            $request,
            IpGeoResponse::class
        );

        if ($response->isFailure()) {
            $errorMessage = $response->getErrorMessage() ?? 'Unknown error';
            throw new \RuntimeException(sprintf('IP geolocation failed: %s', $errorMessage));
        }

        $successData = $response->getSuccessData();

        if ($successData === null) {
            throw new \RuntimeException('IP geolocation returned invalid data');
        }

        return $successData;
    }

    /**
     * Retrieves geolocation data for a given IP address and returns the raw response.
     *
     * This method is useful when you need to handle both success and failure cases
     * without exceptions.
     *
     * @param  string  $ip  The IP address to locate (IPv4 or IPv6)
     * @return IpGeoResponse The raw response
     *
     * @throws InvalidArgumentException When the IP address is empty or invalid
     */
    public function locateRaw(string $ip): IpGeoResponse
    {
        if (empty($ip)) {
            throw new InvalidArgumentException('IP address cannot be empty.');
        }

        if (! $this->isValidIp($ip)) {
            throw new InvalidArgumentException(sprintf('Invalid IP address: %s', $ip));
        }

        $request = new IpGeoRequest($ip);

        $request->getHeaders()
            ->setAccept(ContentType::JSON);

        $request->getOptions()
            ->setTimeout(20)
            ->setConnectTimeout(5)
            ->setHttpErrors(false);

        /** @var IpGeoResponse $response */
        $response = $this->client->get(
            $request->getUrl()->getValue(),
            $request,
            IpGeoResponse::class
        );

        return $response;
    }

    /**
     * Validates an IP address.
     *
     * Supports both IPv4 and IPv6 addresses.
     *
     * @param  string  $ip  The IP address to validate
     * @return bool True if the IP address is valid, false otherwise
     */
    private function isValidIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }
}
