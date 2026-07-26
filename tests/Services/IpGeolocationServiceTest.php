<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Tests\Services;

use AndyDefer\PhpServices\Responses\IpGeoResponse;
use AndyDefer\PhpServices\Services\IpGeolocationService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class IpGeolocationServiceTest extends TestCase
{
    private MockIpGeolocationClient $mockClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockClient = new MockIpGeolocationClient;
    }

    public function test_locate_returns_success_data(): void
    {
        // Arrange: Set up a mock success response
        $ip = '8.8.8.8';
        $this->mockClient->addSuccessResponse($ip, [
            'country' => 'United States',
            'countryCode' => 'US',
            'region' => 'CA',
            'regionName' => 'California',
            'city' => 'Mountain View',
            'zip' => '94043',
            'lat' => 37.422,
            'lon' => -122.084,
            'timezone' => 'America/Los_Angeles',
            'isp' => 'Google LLC',
            'org' => 'Google LLC',
            'as' => 'AS15169 Google LLC',
        ]);

        // Act: Locate the IP address
        $result = $this->mockClient->locate($ip);

        // Assert: The result should contain the mock data
        $this->assertEquals('United States', $result->country);
        $this->assertEquals('US', $result->countryCode);
        $this->assertEquals('CA', $result->region);
        $this->assertEquals('California', $result->regionName);
        $this->assertEquals('Mountain View', $result->city);
        $this->assertEquals('94043', $result->zip);
        $this->assertEquals(37.422, $result->lat);
        $this->assertEquals(-122.084, $result->lon);
        $this->assertEquals('America/Los_Angeles', $result->timezone);
        $this->assertEquals('Google LLC', $result->isp);
        $this->assertEquals('Google LLC', $result->org);
        $this->assertEquals('AS15169 Google LLC', $result->as);
        $this->assertEquals($ip, $result->query);
    }

    public function test_locate_throws_exception_on_failure(): void
    {
        // Arrange: Set up a mock failure response
        $ip = '169.159.220.210ddd';
        $this->mockClient->addFailureResponse('invalid query', $ip);

        // Assert: An exception should be thrown
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('IP geolocation failed: invalid query');

        // Act: Locate the IP address
        $this->mockClient->locate($ip);
    }

    public function test_locate_throws_exception_on_http_error(): void
    {
        // Arrange: Set up a mock HTTP error response
        $ip = '8.8.8.8';
        $this->mockClient->addErrorResponse(500, 'Internal Server Error');

        // Assert: An exception should be thrown
        $this->expectException(RuntimeException::class);

        // Act: Locate the IP address
        $this->mockClient->locate($ip);
    }

    public function test_locate_throws_exception_for_invalid_ip(): void
    {
        // Arrange: Use an invalid IP address
        $ip = 'invalid-ip';

        // Assert: An exception should be thrown
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid IP address');

        // Act: Locate the invalid IP address
        $this->mockClient->locate($ip);
    }

    public function test_locate_throws_exception_for_empty_ip(): void
    {
        // Arrange: Use an empty IP address
        $ip = '';

        // Assert: An exception should be thrown
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('IP address cannot be empty');

        // Act: Locate the empty IP address
        $this->mockClient->locate($ip);
    }

    public function test_locate_raw_returns_success_response(): void
    {
        // Arrange: Set up a mock success response
        $ip = '8.8.8.8';
        $this->mockClient->addSuccessResponse($ip, [
            'country' => 'United States',
            'countryCode' => 'US',
            'city' => 'Mountain View',
        ]);

        // Act: Locate the IP address
        $response = $this->mockClient->locateRaw($ip);

        // Assert: The response should be successful
        $this->assertInstanceOf(IpGeoResponse::class, $response);
        $this->assertTrue($response->isSuccess());
        $this->assertNotNull($response->getSuccessData());
        $this->assertNull($response->getFailureData());
        $this->assertNull($response->getErrorMessage());
        $this->assertEquals($ip, $response->getQuery());
    }

    public function test_locate_raw_returns_failure_response(): void
    {
        // Arrange: Set up a mock failure response
        $ip = '169.159.220.210ddd';
        $this->mockClient->addFailureResponse('invalid query', $ip);

        // Act: Locate the IP address
        $response = $this->mockClient->locateRaw($ip);

        // Assert: The response should be a failure
        $this->assertInstanceOf(IpGeoResponse::class, $response);
        $this->assertTrue($response->isFailure());
        $this->assertNull($response->getSuccessData());
        $this->assertNotNull($response->getFailureData());
        $this->assertEquals('invalid query', $response->getErrorMessage());
        $this->assertEquals($ip, $response->getQuery());
    }

    public function test_response_success_data_returns_correct_structure(): void
    {
        // Arrange: Set up a mock success response
        $ip = '8.8.8.8';
        $this->mockClient->addSuccessResponse($ip, [
            'country' => 'United States',
            'countryCode' => 'US',
            'region' => 'CA',
            'regionName' => 'California',
            'city' => 'Mountain View',
            'zip' => '94043',
            'lat' => 37.422,
            'lon' => -122.084,
            'timezone' => 'America/Los_Angeles',
            'isp' => 'Google LLC',
            'org' => 'Google LLC',
            'as' => 'AS15169 Google LLC',
        ]);

        // Act: Locate the IP address
        $response = $this->mockClient->locateRaw($ip);

        // Assert: The success data structure is correct
        $data = $response->getSuccessData();
        $this->assertNotNull($data);
        $this->assertObjectHasProperty('country', $data);
        $this->assertObjectHasProperty('countryCode', $data);
        $this->assertObjectHasProperty('region', $data);
        $this->assertObjectHasProperty('regionName', $data);
        $this->assertObjectHasProperty('city', $data);
        $this->assertObjectHasProperty('zip', $data);
        $this->assertObjectHasProperty('lat', $data);
        $this->assertObjectHasProperty('lon', $data);
        $this->assertObjectHasProperty('timezone', $data);
        $this->assertObjectHasProperty('isp', $data);
        $this->assertObjectHasProperty('org', $data);
        $this->assertObjectHasProperty('as', $data);
        $this->assertObjectHasProperty('query', $data);
    }

    public function test_response_failure_data_returns_correct_structure(): void
    {
        // Arrange: Set up a mock failure response
        $ip = '169.159.220.210ddd';
        $this->mockClient->addFailureResponse('invalid query', $ip);

        // Act: Locate the IP address
        $response = $this->mockClient->locateRaw($ip);

        // Assert: The failure data structure is correct
        $data = $response->getFailureData();
        $this->assertNotNull($data);
        $this->assertObjectHasProperty('message', $data);
        $this->assertObjectHasProperty('query', $data);
        $this->assertEquals('invalid query', $data->message);
        $this->assertEquals($ip, $data->query);
    }

    public function test_service_works_with_real_client(): void
    {
        // Arrange: Use the real service
        $service = new IpGeolocationService;
        $ip = '8.8.8.8';

        // Act: Locate the IP address
        $result = $service->locate($ip);

        // Assert: The result should contain valid data
        $this->assertNotEmpty($result->country);
        $this->assertNotEmpty($result->countryCode);
        $this->assertNotEmpty($result->city);
        $this->assertNotEquals(0.0, $result->lat);
        $this->assertNotEquals(0.0, $result->lon);
        $this->assertEquals($ip, $result->query);
    }
}
