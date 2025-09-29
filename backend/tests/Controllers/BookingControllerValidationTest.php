<?php
namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\BookingController;
use App\Services\PayloadTransformer;
use App\Services\ResponseFormatter;
use Tests\Helpers\HttpTestHelpers;

/**
 * @covers \App\Controllers\BookingController
 */
class BookingControllerValidationTest extends TestCase
{
    use HttpTestHelpers;

    protected function setUp(): void
    {
        $this->setUpHttp();
    }

    public function testReturns400OnInvalidPayload(): void
    {
        $mockTransformer = $this->createMock(PayloadTransformer::class);
        $mockTransformer->method('transform')
            ->willThrowException(new \InvalidArgumentException("Missing required field: Arrival"));

        $mockFormatter = $this->createMock(ResponseFormatter::class);
        $mockHttpClient = $this->createMock(\GuzzleHttp\Client::class);

        $controller = new BookingController($mockTransformer, $mockFormatter, $mockHttpClient);

        $result = $controller->calculateRates($this->makeRequest(), $this->makeResponse());

        $this->assertSame(400, $result->getStatusCode());
        $this->assertStringContainsString('Invalid input', (string) $result->getBody());
        $this->assertStringContainsString('Missing required field: Arrival', (string) $result->getBody());
    }
}