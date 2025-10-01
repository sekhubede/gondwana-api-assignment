<?php
namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\BookingController;
use App\Services\PayloadTransformer;
use App\Services\ResponseFormatter;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

class BookingControllerFailureTest extends TestCase
{
    public function testReturns400OnInvalidPayload(): void
    {
        $controller = new BookingController(
            new PayloadTransformer(),
            new ResponseFormatter(),
            $this->createMock(Client::class)
        );

        $request = (new ServerRequestFactory())->createServerRequest('POST', '/rates')
            ->withParsedBody([
                "Arrival" => "2025-10-01", // wrong format
                "Departure" => "05/10/2025",
                "Ages" => [25]
            ]);
        $response = (new ResponseFactory())->createResponse();

        $result = $controller->calculateRates($request, $response);
        $this->assertSame(400, $result->getStatusCode());
    }

    public function testReturns502OnRequestException(): void
    {
        $mockHttp = $this->createMock(Client::class);
        $mockHttp->method('post')->willThrowException(
            new RequestException("Upstream error", $this->createMock(RequestInterface::class))
        );

        $controller = new BookingController(
            new PayloadTransformer(),
            new ResponseFormatter(),
            $mockHttp
        );

        $request = (new ServerRequestFactory())->createServerRequest('POST', '/rates')
            ->withParsedBody([
                "Arrival" => "01/10/2025",
                "Departure" => "05/10/2025",
                "Ages" => [25]
            ]);
        $response = (new ResponseFactory())->createResponse();

        $result = $controller->calculateRates($request, $response);
        $this->assertSame(502, $result->getStatusCode());
    }

    public function testReturns500OnUnexpectedException(): void
    {
        $mockHttp = $this->createMock(Client::class);
        $mockHttp->method('post')->willThrowException(new \RuntimeException("Unexpected"));

        $controller = new BookingController(
            new PayloadTransformer(),
            new ResponseFormatter(),
            $mockHttp
        );

        $request = (new ServerRequestFactory())->createServerRequest('POST', '/rates')
            ->withParsedBody([
                "Arrival" => "01/10/2025",
                "Departure" => "05/10/2025",
                "Ages" => [25]
            ]);
        $response = (new ResponseFactory())->createResponse();

        $result = $controller->calculateRates($request, $response);
        $this->assertSame(500, $result->getStatusCode());
    }
}