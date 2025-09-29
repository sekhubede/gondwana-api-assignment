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
class BookingControllerFailureTest extends TestCase
{
    use HttpTestHelpers;

    protected function setUp(): void
    {
        $this->setUpHttp();
    }

    public function testReturns502OnRequestException(): void
    {
        $mockTransformer = $this->createMock(PayloadTransformer::class);
        $mockTransformer->method('transform')->willReturn(['dummy' => true]);

        $mockFormatter = $this->createMock(ResponseFormatter::class);

        $mockHttpClient = $this->createMock(\GuzzleHttp\Client::class);
        $mockHttpClient->method('post')->willThrowException(new \GuzzleHttp\Exception\RequestException(
            "Simulated API error",
            new \GuzzleHttp\Psr7\Request('POST', 'test')
        ));

        $controller = new BookingController($mockTransformer, $mockFormatter, $mockHttpClient);

        $result = $controller->calculateRates($this->makeRequest(), $this->makeResponse());

        $this->assertSame(502, $result->getStatusCode());
        $this->assertStringContainsString('Failed to fetch rates', (string)$result->getBody());
    }

    public function testReturns500OnUnexpectedException(): void
    {
        $mockTransformer = $this->createMock(PayloadTransformer::class);
        $mockTransformer->method('transform')->willReturn(['dummy' => true]);

        $mockFormatter = $this->createMock(ResponseFormatter::class);

        $mockHttpClient = $this->createMock(\GuzzleHttp\Client::class);
        $mockHttpClient->method('post')->willThrowException(new \RuntimeException("Unexpected crash"));

        $controller = new BookingController($mockTransformer, $mockFormatter, $mockHttpClient);

        $result = $controller->calculateRates($this->makeRequest(), $this->makeResponse());

        $this->assertSame(500, $result->getStatusCode());
        $this->assertStringContainsString('Unexpected server error', (string)$result->getBody());
    }
}