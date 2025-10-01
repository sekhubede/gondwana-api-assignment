<?php
namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\BookingController;
use App\Services\PayloadTransformer;
use App\Services\ResponseFormatter;
use GuzzleHttp\Client;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

class BookingControllerSuccessTest extends TestCase
{
    public function testReturns200OnSuccessfulApiCall(): void
    {
        $mockHttp = $this->createMock(Client::class);
        $mockHttp->method('post')->willReturn(
            new GuzzleResponse(200, [], json_encode([
                'Location ID' => 1,
                'Total Charge' => 1000,
                'Extras Charge' => 0,
                'Booking Group ID' => 'Test',
                'Legs' => []
            ]))
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
        $this->assertSame(200, $result->getStatusCode());
    }
}