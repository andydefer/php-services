<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Requests;

use AndyDefer\PhpClient\Abstracts\Request;
use AndyDefer\PhpClient\Abstracts\Struct;
use AndyDefer\PhpClient\Enums\ContentType;
use AndyDefer\PhpClient\Enums\HttpMethod;
use AndyDefer\PhpClient\ValueObjects\RequestBodyVO;
use AndyDefer\PhpClient\ValueObjects\UrlVO;

/**
 * Request for IP geolocation.
 *
 * Queries the ip-api.com API to retrieve geolocation data for a given IP address.
 */
final class IpGeoRequest extends Request
{
    private const API_URL = 'http://ip-api.com/json';

    private string $ip;

    public function __construct(string $ip)
    {
        $this->ip = $ip;
        parent::__construct();
    }

    protected function setMethod(): HttpMethod
    {
        return HttpMethod::GET;
    }

    protected function setUrl(): UrlVO
    {
        return new UrlVO(sprintf('%s/%s', self::API_URL, $this->ip));
    }

    protected function setBody(): RequestBodyVO
    {
        return new RequestBodyVO(
            new class extends Struct {},
            ContentType::JSON
        );
    }
}
