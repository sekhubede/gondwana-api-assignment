<?php
namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\BookingController;
use App\Services\PayloadTransformer;
use App\Services\ResponseFormatter;
use GuzzleHttp\Client;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

class BookingControllerValidationTest extends TestCase
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
                "Departure" => "05/10/2025",
                "Ages" => [25]
            ]);
        $response = (new ResponseFactory())->createResponse();

        $result = $controller->calculateRates($request, $response);
        $this->assertSame(400, $result->getStatusCode());

        $data = json_decode((string)$result->getBody(), true);
        $this->assertFalse($data['success']);

        // Match new error message
        $this->assertStringContainsString(
            "Arrival and departure dates are required",
            $data['message']
        );
    }
}