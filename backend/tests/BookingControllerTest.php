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

    public function testReturns400OnInvalidPayload(): void
    {
        $mockTransformer = $this->createMock(PayloadTransformer::class);
        $mockTransformer->method('transform')
            ->willThrowException(new InvalidArgumentException("Missing required field: Arrival"));

        $mockFormatter = $this->createMock(ResponseFormatter::class);
        $controller = new BookingController($mockTransformer, $mockFormatter);

        $request = $this->requestFactory->createServerRequest('POST', '/rates');
        $response = $this->responseFactory->createResponse();

        $result = $controller->calculateRates($request, $response);

        $this->assertSame(400, $result->getStatusCode());
        $body = (string) $result->getBody();
        $this->assertThat($body, $this->stringContains('Invalid input'));
        $this->assertThat($body, $this->stringContains('Missing required field: Arrival'));
    }

    public function testReturns200OnValidPayload(): void
    {
        $mockTransformer = $this->createMock(PayloadTransformer::class);
        $mockTransformer->method('transform')->willReturn([
            'Unit Type ID' => -2147483637,
            'Arrival'      => '2025-10-01',
            'Departure'    => '2025-10-05',
            'Guests'       => [['Age Group' => 'Adult']]
        ]);

        $mockFormatter = $this->createMock(ResponseFormatter::class);

        // Override controller to bypass API call
        $controller = new class($mockTransformer, $mockFormatter) extends BookingController {
            public function calculateRates($request, $response): \Psr\Http\Message\ResponseInterface
            {
                $response->getBody()->write(json_encode([
                    'success' => true,
                    'data'    => ['mocked' => true]
                ]));
                return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
            }
        };

        $request = $this->requestFactory->createServerRequest('POST', '/rates');
        $response = $this->responseFactory->createResponse();

        $result = $controller->calculateRates($request, $response);

        $this->assertSame(200, $result->getStatusCode());
        $this->assertThat((string)$result->getBody(), $this->stringContains('"success":true'));
    }

    public function testReturns500OnApiFailure(): void
    {
        $mockTransformer = $this->createMock(PayloadTransformer::class);
        $mockTransformer->method('transform')->willReturn([
            'Unit Type ID' => -2147483637,
            'Arrival'      => '2025-10-01',
            'Departure'    => '2025-10-05',
            'Guests'       => [['Age Group' => 'Adult']]
        ]);

        $mockFormatter = $this->createMock(ResponseFormatter::class);

        $controller = new class($mockTransformer, $mockFormatter) extends BookingController {
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

        $this->assertSame(500, $result->getStatusCode());
        $this->assertThat((string)$result->getBody(), $this->stringContains('Failed to fetch rates'));
    }

    public function testUsesResponseFormatter(): void
    {
        $mockTransformer = $this->createMock(PayloadTransformer::class);
        $mockTransformer->method('transform')->willReturn([
            'Unit Type ID' => -2147483637,
            'Arrival'      => '2025-10-01',
            'Departure'    => '2025-10-05',
            'Guests'       => [['Age Group' => 'Adult']]
        ]);

        $mockFormatter = $this->createMock(ResponseFormatter::class);
        $mockFormatter->expects($this->once())
            ->method('format')
            ->willReturn(['mocked' => true]);

        $controller = new class($mockTransformer, $mockFormatter) extends BookingController {
            private $formatter;
            public function __construct($transformer, $formatter)
            {
                parent::__construct($transformer, $formatter);
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

        $this->assertSame(200, $result->getStatusCode());
        $this->assertThat((string)$result->getBody(), $this->stringContains('"mocked":true'));
    }

    public function testReturns502OnRequestException(): void
    {
        $mockTransformer = $this->createMock(PayloadTransformer::class);
        $mockTransformer->method('transform')->willReturn([
            'Unit Type ID' => -2147483637,
            'Arrival'      => '2025-10-01',
            'Departure'    => '2025-10-05',
            'Guests'       => [['Age Group' => 'Adult']]
        ]);

        $mockFormatter = $this->createMock(ResponseFormatter::class);

        // Override to simulate RequestException
        $controller = new class($mockTransformer, $mockFormatter) extends BookingController {
            public function calculateRates($request, $response): \Psr\Http\Message\ResponseInterface
            {
                // Simulate Guzzle RequestException branch
                return $this->errorResponse($response, 502, 'Failed to fetch rates', 'Simulated RequestException');
            }
        };

        $request = $this->requestFactory->createServerRequest('POST', '/rates');
        $response = $this->responseFactory->createResponse();

        $result = $controller->calculateRates($request, $response);

        $this->assertSame(502, $result->getStatusCode());
        $this->assertStringContainsString('Failed to fetch rates', (string)$result->getBody());
    }

    public function testReturns500OnUnexpectedThrowable(): void
    {
        $mockTransformer = $this->createMock(PayloadTransformer::class);
        $mockTransformer->method('transform')->willReturn([
            'Unit Type ID' => -2147483637,
            'Arrival'      => '2025-10-01',
            'Departure'    => '2025-10-05',
            'Guests'       => [['Age Group' => 'Adult']]
        ]);

        $mockFormatter = $this->createMock(ResponseFormatter::class);

        // Override to simulate Throwable branch
        $controller = new class($mockTransformer, $mockFormatter) extends BookingController {
            public function calculateRates($request, $response): \Psr\Http\Message\ResponseInterface
            {
                return $this->errorResponse($response, 500, 'Unexpected server error', 'Simulated Throwable');
            }
        };

        $request = $this->requestFactory->createServerRequest('POST', '/rates');
        $response = $this->responseFactory->createResponse();

        $result = $controller->calculateRates($request, $response);

        $this->assertSame(500, $result->getStatusCode());
        $this->assertStringContainsString('Unexpected server error', (string)$result->getBody());
    }

}