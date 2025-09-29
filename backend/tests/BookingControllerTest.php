<?php
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use App\Controllers\BookingController;
use App\Services\PayloadTransformer;
use GuzzleHttp\Psr7\ServerRequest;

class BookingControllerTest extends TestCase
{
    public function testReturns400OnInvalidPayload()
    {
        $mockTransformer = $this->createMock(PayloadTransformer::class);
        $mockTransformer->method('transform')
            ->willThrowException(new InvalidArgumentException(("Missing required field: Arrival")));

            $controller = new BookingController($mockTransformer);

            $request = (new ServerRequestFactory())->createServerRequest('POST', '/rates');
            $response = (new ResponseFactory())->createResponse();

            $result = $controller->calculateRates($request, $response);

            $this->assertEquals(400, $result->getStatusCode());
            $body = (string) $result->getBody();
            $this->assertStringContainsString('Invalid input', $body);
    }

    public function testResturns200OnValidPayload()
    {
        $mockTransformer = $this->createMock(PayloadTransformer::class);
        $mockTransformer->method('transform')
            ->willReturn([
                'Unit Type ID'  => -2147483637,
                'Arrival'       => '2025-10-01',
                'Departure'     => '2025-10-05',
                'Guests'        => [
                    ['Age Group' => 'Adult']
                ]
                ]);

         $controller = new class($mockTransformer) extends BookingController {
            public function calculateRates($request, $response): \Psr\Http\Message\ResponseInterface
            {
                $response->getBody()->write(json_encode([
                    'success'   => true,
                    'data'      => ['mocked' => true]
                ]));
                return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
            }
         };

         $request = (new ServerRequestFactory())->createServerRequest('POST', '/rates');
         $response = (new ResponseFactory())->createResponse();

         $result = $controller->calculateRates($request, $response);

         $this->assertEquals(200, $result->getStatusCode());
         $body = (string) $result->getBody();
         $this->assertStringContainsString('"success":true', $body);
    }
}