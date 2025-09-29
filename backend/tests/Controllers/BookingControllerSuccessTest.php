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
class BookingControllerSuccessTest extends TestCase
{
    use HttpTestHelpers;

    protected function setUp(): void
    {
        $this->setUpHttp();
    }

    public function testReturns200OnSuccessfulApiCall(): void
    {
        $mockTransformer = $this->createMock(PayloadTransformer::class);
        $mockTransformer->method('transform')->willReturn(['dummy' => true]);

        $mockFormatter = $this->createMock(ResponseFormatter::class);
        $mockFormatter->method('format')->willReturn(['mocked' => true]);

        $mockResponse = new \GuzzleHttp\Psr7\Response(
            200,
            [],
            json_encode(['api' => 'response'])
        );

        $mockHttpClient = $this->createMock(\GuzzleHttp\Client::class);
        $mockHttpClient->method('post')->willReturn($mockResponse);

        $controller = new BookingController($mockTransformer, $mockFormatter, $mockHttpClient);

        $result = $controller->calculateRates($this->makeRequest(), $this->makeResponse());

        $this->assertSame(200, $result->getStatusCode());
        $this->assertStringContainsString('"success": true', (string)$result->getBody());
    }
}