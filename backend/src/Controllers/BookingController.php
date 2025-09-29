<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\PayloadTransformer;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class BookingController
{
    private PayloadTransformer $transformer;
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
        $data = $request->getParsedBody();
        $normalized = $this->transformer->transform($data);

        try {

            $apiResponse = $this->httpClient->post('Rates.php', [
                'json' => $normalized
            ]);

            $body = $apiResponse->getBody()->getContents();
            $decoded = json_decode($body, true);

            $response->getBody()->write(json_encode($decoded, JSON_PRETTY_PRINT));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (RequestException $e) {
            
            $error = [
                'error' => 'Failed to fetch rates',
                'message' => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error, JSON_PRETTY_PRINT));
            return $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json');
        }

    }
}