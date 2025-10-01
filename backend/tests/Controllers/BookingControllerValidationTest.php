<?php
namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use App\Controllers\BookingController;
use App\Services\PayloadTransformer;
use App\Services\ResponseFormatter;
use GuzzleHttp\Client;

class BookingControllerValidationTest extends TestCase
{
    private $app;

    protected function setUp(): void
    {
        $app = AppFactory::create();
        $transformer = new PayloadTransformer();
        $formatter   = new ResponseFormatter();
        $httpClient  = new Client(['base_uri' => 'http://example.com']);
        $controller  = new BookingController($transformer, $formatter, $httpClient);

        $app->post('/rates', [$controller, 'calculateRates']);
        $this->app = $app;
    }

    private function createRequest(array $data)
    {
        $requestFactory = new ServerRequestFactory();
        return $requestFactory->createServerRequest('POST', '/rates')
            ->withParsedBody($data);
    }

    public function testReturns400OnInvalidPayload(): void
    {
        $request = $this->createRequest([]);
        $response = $this->app->handle($request);
        $decoded = json_decode((string) $response->getBody(), true);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertFalse($decoded['success']);
        $this->assertSame('Arrival and departure dates are required (dd/mm/yyyy).', $decoded['message']);
    }

    public function testReturns400OnArrivalInThePast(): void
    {
        $yesterday = (new \DateTimeImmutable('-1 day'))->format('d/m/Y');
        $future    = (new \DateTimeImmutable('+5 days'))->format('d/m/Y');

        $request = $this->createRequest([
            'Arrival'   => $yesterday,
            'Departure' => $future,
            'Ages'      => [25]
        ]);
        $response = $this->app->handle($request);
        $decoded  = json_decode((string) $response->getBody(), true);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertFalse($decoded['success']);
        $this->assertSame('Arrival date cannot be in the past.', $decoded['message']);
    }

    public function testReturns400OnDepartureBeforeArrival(): void
    {
        $inTwoDays = (new \DateTimeImmutable('+2 days'))->format('d/m/Y');
        $inOneDay  = (new \DateTimeImmutable('+1 day'))->format('d/m/Y');

        $request = $this->createRequest([
            'Arrival'   => $inTwoDays,
            'Departure' => $inOneDay,
            'Ages'      => [25]
        ]);
        $response = $this->app->handle($request);
        $decoded  = json_decode((string) $response->getBody(), true);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertFalse($decoded['success']);
        $this->assertSame('Departure date must be after arrival date.', $decoded['message']);
    }

    public function testReturns400OnDepartureEqualToArrival(): void
    {
        $sameDay = (new \DateTimeImmutable('+3 days'))->format('d/m/Y');

        $request = $this->createRequest([
            'Arrival'   => $sameDay,
            'Departure' => $sameDay,
            'Ages'      => [25]
        ]);
        $response = $this->app->handle($request);
        $decoded  = json_decode((string) $response->getBody(), true);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertFalse($decoded['success']);
        $this->assertSame('Departure date must be after arrival date.', $decoded['message']);
    }
}