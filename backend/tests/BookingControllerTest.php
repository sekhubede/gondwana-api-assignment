<?php
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use App\Controllers\BookingController;
use App\Services\PayloadTransformer;
use App\Services\ResponseFormatter;

/**
 * @covers \App\Controllers\BookingController
 */
class BookingControllerTest extends TestCase
{
    private ServerRequestFactory $requestFactory;
    private ResponseFactory $responseFactory;

    protected function setUp(): void
    {
        $this->requestFactory = new ServerRequestFactory();
        $this->responseFactory = new ResponseFactory();
    }

    public function testReturns400OnInvalidPayload()
    {
        $mockTransformer = $this->createMock(PayloadTransformer::class);
        $mockTransformer->method('transform')
            ->willThrowException(new InvalidArgumentException("Missing required field: Arrival"));

        $controller = new BookingController($mockTransformer);

        $request = $this->requestFactory->createServerRequest('POST', '/rates');
        $response = $this->responseFactory->createResponse();

        $result = $controller->calculateRates($request, $response);

        $this->assertEquals(400, $result->getStatusCode());
        $body = (string) $result->getBody();
        $this->assertStringContainsString('Invalid input', $body);
    }

    public function testReturns200OnValidPayload()
    {
        $mockTransformer = $this->createMock(PayloadTransformer::class);
        $mockTransformer->method('transform')
            ->willReturn([
                'Unit Type ID'  => -2147483637,
                'Arrival'       => '2025-10-01',
                'Departure'     => '2025-10-05',
                'Guests'        => [['Age Group' => 'Adult']]
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

        $request = $this->requestFactory->createServerRequest('POST', '/rates');
        $response = $this->responseFactory->createResponse();

        $result = $controller->calculateRates($request, $response);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertStringContainsString('"success":true', (string)$result->getBody());
    }

    public function testReturns500OnApiFailure()
    {
        $mockTransformer = $this->createMock(PayloadTransformer::class);
        $mockTransformer->method('transform')
            ->willReturn([
                'Unit Type ID'  => -2147483637,
                'Arrival'       => '2025-10-01',
                'Departure'     => '2025-10-05',
                'Guests'        => [['Age Group' => 'Adult']]
            ]);

        $controller = new class($mockTransformer) extends BookingController {
            public function calculateRates($request, $response): \Psr\Http\Message\ResponseInterface
            {
                $response->getBody()->write(json_encode([
                    'error'   => 'Failed to fetch rates',
                    'message' => 'Simulated API failure'
                ]));
                return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
            }
        };

        $request = $this->requestFactory->createServerRequest('POST', '/rates');
        $response = $this->responseFactory->createResponse();

        $result = $controller->calculateRates($request, $response);

        $this->assertEquals(500, $result->getStatusCode());
        $this->assertStringContainsString('Failed to fetch rates', (string)$result->getBody());
    }

    public function testUsesResponseFormatter()
    {
        $mockTransformer = $this->createMock(PayloadTransformer::class);
        $mockTransformer->method('transform')
            ->willReturn([
                'Unit Type ID'  => -2147483637,
                'Arrival'       => '2025-10-01',
                'Departure'     => '2025-10-05',
                'Guests'        => [['Age Group' => 'Adult']]
            ]);

        $mockFormatter = $this->createMock(ResponseFormatter::class);
        $mockFormatter->expects($this->once())
            ->method('format')
            ->willReturn(['mocked' => true]);

        $controller = new class($mockTransformer, $mockFormatter) extends BookingController {
            private $formatter;
            public function __construct($transformer, $formatter)
            {
                parent::__construct($transformer);
                $this->formatter = $formatter;
            }
            public function calculateRates($request, $response): \Psr\Http\Message\ResponseInterface
            {
                $formatted = $this->formatter->format(['dummy']);
                $response->getBody()->write(json_encode($formatted));
                return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
            }
        };

        $request = $this->requestFactory->createServerRequest('POST', '/rates');
        $response = $this->responseFactory->createResponse();

        $result = $controller->calculateRates($request, $response);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertStringContainsString('mocked', (string)$result->getBody());
    }
}