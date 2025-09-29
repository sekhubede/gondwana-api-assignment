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

                $response->getBody()->write(json_encode([
                    'success' => true,
                    'data' => $clean
                ], JSON_PRETTY_PRINT));

                return $response->withHeader('Content-Type', 'application/json');

            } catch (InvalidArgumentException $e) {
                return $this->errorResponse(
                    $response,
                    400,
                    'Invalid input',
                    $e->getMessage()
                );

            } catch (RequestException $e) {
                return $this->errorResponse(
                    $response,
                    502,
                    'Failed to fetch rates',
                    $e->getMessage()
                );

            } catch (\Throwable $e) {
                return $this->errorResponse(
                    $response,
                    500,
                    'Unexpected server error',
                    $e->getMessage()
                );
            }
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