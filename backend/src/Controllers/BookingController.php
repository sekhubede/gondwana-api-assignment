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

    public function __construct(PayloadTransformer $transformer)
    {
        $this->transformer = $transformer;
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
                'success'   => true,
                'data'      => $clean
            ], JSON_PRETTY_PRINT));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (InvalidArgumentException $e) {

            $error = [
                'success'   => false,
                'error'     => 'Invalid input',
                'message'   => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error, JSON_PRETTY_PRINT));
            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');

        } catch (RequestException $e) {
            
            $error = [
                'success'   => false,
                'error'     => 'Failed to fetch rates',
                'message'   => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error, JSON_PRETTY_PRINT));
            return $response
                ->withStatus(502)
                ->withHeader('Content-Type', 'application/json');

        } catch (\Throwable $e) {

            $error = [
                'success'   => false,
                'error'     => 'Unexpected server error',
                'message'   => $e->getMessage()()
            ];
            $response->getBody()->write(json_encode($error, JSON_PRETTY_PRINT));
            return $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    private function errorResponse(Response $response, int $status, string $error, string $message): Response
    {
        $response->getBody()->write(json_encode([
            'success'   => false,
            'error'     => $error,
            'message'   => $message
        ], JSON_PRETTY_PRINT));

        return $response->withStatus($status)->withHeader('Content-Type', 'applicaitn/json');
    }
}