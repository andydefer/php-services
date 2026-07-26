<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Tests\Services;

use AndyDefer\PhpClient\Clients\ClientService;
use AndyDefer\PhpServices\Services\IpGeolocationService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Assert;

final class MockIpGeolocationClient extends IpGeolocationService
{
    private MockHandler $mockHandler;

    public function __construct()
    {
        $this->mockHandler = new MockHandler;
        $handlerStack = HandlerStack::create($this->mockHandler);
        $guzzleClient = new Client(['handler' => $handlerStack]);
        $clientService = new ClientService($guzzleClient);

        parent::__construct($clientService);
    }

    public function addSuccessResponse(string $ip, array $data): void
    {
        $responseData = array_merge([
            'status' => 'success',
            'query' => $ip,
        ], $data);

        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], json_encode($responseData))
        );
    }

    public function addFailureResponse(string $message, ?string $ip = null): void
    {
        $responseData = [
            'status' => 'fail',
            'message' => $message,
        ];

        if ($ip !== null) {
            $responseData['query'] = $ip;
        }

        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/json'], json_encode($responseData))
        );
    }

    public function addErrorResponse(int $statusCode, string $errorMessage): void
    {
        $this->mockHandler->append(
            new Response($statusCode, [], $errorMessage)
        );
    }

    public function getMockHandler(): MockHandler
    {
        return $this->mockHandler;
    }

    public function assertRequestCount(int $expectedCount): void
    {
        $requests = $this->mockHandler->getLastRequest();
        if ($requests === null) {
            $count = 0;
        } else {
            $count = 1;
        }
        Assert::assertEquals($expectedCount, $count);
    }

    public function assertRequestUri(string $expectedUri): void
    {
        $request = $this->mockHandler->getLastRequest();
        if ($request === null) {
            Assert::fail('No request was made');
        }
        Assert::assertEquals($expectedUri, (string) $request->getUri());
    }
}
