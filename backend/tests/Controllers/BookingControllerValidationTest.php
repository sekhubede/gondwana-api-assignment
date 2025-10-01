<?php
namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use Slim\Factory\AppFactory;
use App\Controllers\BookingController;
use App\Services\PayloadTransformer;
use App\Services\ResponseFormatter;
use GuzzleHttp\Client;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

class BookingControllerValidationTest extends TestCase
{
    private $controller;

    protected function setUp(): void
    {
        $this->controller = new BookingController(
            new PayloadTransformer(),
            new ResponseFormatter(),
            new Client(['http_errors' => false]) // dummy client
        );
    }

    public function testReturns400WhenDepartureMissing(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('POST', '/rates');
        $request = $request->withParsedBody([
            "Arrival" => "01/10/2025",
            "Ages"    => [30]
        ]);

        $response = (new ResponseFactory())->createResponse();
        $result = $this->controller->calculateRates($request, $response);

        $this->assertSame(400, $result->getStatusCode());
    }

    public function testReturns400WhenArrivalInPast(): void
    {
        $yesterday = (new \DateTime('yesterday'))->format('d/m/Y');

        $request = (new ServerRequestFactory())->createServerRequest('POST', '/rates');
        $request = $request->withParsedBody([
            "Arrival"   => $yesterday,
            "Departure" => "05/10/2025",
            "Ages"      => [30]
        ]);

        $response = (new ResponseFactory())->createResponse();
        $result = $this->controller->calculateRates($request, $response);

        $this->assertSame(400, $result->getStatusCode());
    }

    public function testReturns400WhenDepartureBeforeArrival(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('POST', '/rates');
        $request = $request->withParsedBody([
            "Arrival"   => "10/10/2025",
            "Departure" => "05/10/2025",
            "Ages"      => [30]
        ]);

        $response = (new ResponseFactory())->createResponse();
        $result = $this->controller->calculateRates($request, $response);

        $this->assertSame(400, $result->getStatusCode());
    }
}