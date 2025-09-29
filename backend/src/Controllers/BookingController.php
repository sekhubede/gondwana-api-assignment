<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\PayloadTransformer;
use App\Services\ResponseFormatter;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;

class BookingController
{
    private PayloadTransformer $transformer;
    private ResponseFormatter $formatter;
    private Client $httpClient;

    public function __construct(PayloadTransformer $transformer, ResponseFormatter $formatter)
    {
        $this->transformer = $transformer;
        $this->formatter = $formatter;
        $this->httpClient = new Client([
            'base_uri' => 'https://dev.gondwana-collection.com/Web-Store/Rates/',
            'timeout' => 10.0
        ]);
    }

    public function calculateRates(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody() ?? [];
            $normalized = $this->transformer->transform($data);

            $apiResponse = $this->httpClient->post('Rates.php', [
                'json' => $normalized
            ]);

            $body = $apiResponse->getBody()->getContents();
            $decoded = json_decode($body, true);

            $clean = $this->formatter->format($decoded);

            $payload = [
                'status'  => 200,
                'content' => [
                    'success' => true,
                    'data'    => $clean
                ]
            ];
        } catch (InvalidArgumentException $e) {
            $payload = [
                'status'  => 400,
                'content' => [
                    'success' => false,
                    'error'   => 'Invalid input',
                    'message' => $e->getMessage()
                ]
            ];
        } catch (RequestException $e) {
            $payload = [
                'status'  => 502,
                'content' => [
                    'success' => false,
                    'error'   => 'Failed to fetch rates',
                    'message' => $e->getMessage()
                ]
            ];
        } catch (\Throwable $e) {
            $payload = [
                'status'  => 500,
                'content' => [
                    'success' => false,
                    'error'   => 'Unexpected server error',
                    'message' => $e->getMessage()
                ]
            ];
        }

        $response->getBody()->write(json_encode($payload['content'], JSON_PRETTY_PRINT));
        return $response
            ->withStatus($payload['status'])
            ->withHeader('Content-Type', 'application/json');
    }

    protected function errorResponse(Response $response, int $status, string $error, string $message): Response
    {
        $response->getBody()->write(json_encode([
            'success' => false,
            'error' => $error,
            'message' => $message
        ], JSON_PRETTY_PRINT));

        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    }
}