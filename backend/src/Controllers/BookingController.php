<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\PayloadTransformer;
use App\Services\ResponseFormatter;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;
use DateTime;

class BookingController
{
    private PayloadTransformer $transformer;
    private ResponseFormatter $formatter;
    private Client $httpClient;

    public function __construct(
        PayloadTransformer $transformer,
        ResponseFormatter $formatter,
        ?Client $httpClient = null
    ) {
        $this->transformer = $transformer;
        $this->formatter   = $formatter;
        $this->httpClient  = $httpClient ?? new Client([
            'base_uri' => 'https://dev.gondwana-collection.com/Web-Store/Rates/',
            'timeout'  => 10.0
        ]);
    }

    public function calculateRates(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody() ?? [];

            if (!isset($data['Arrival']) || !isset($data['Departure'])) {
                throw new InvalidArgumentException('Arrival and departure dates are required (dd/mm/yyyy).');
            }

            DateTime::getLastErrors();
            $arrival = DateTime::createFromFormat('!d/m/Y', $data['Arrival']);
            
            DateTime::getLastErrors();
            $departure = DateTime::createFromFormat('!d/m/Y', $data['Departure']);

            if ($arrival === false || $departure === false) {
                throw new InvalidArgumentException('Arrival and departure dates are required (dd/mm/yyyy).');
            }

            $today = new DateTime('today');

            if ($arrival < $today) {
                throw new InvalidArgumentException('Arrival date cannot be in the past.');
            }

            if ($departure <= $arrival) {
                throw new InvalidArgumentException('Departure date must be after arrival date.');
            }

            $normalized  = $this->transformer->transform($data);
            $apiResponse = $this->httpClient->post('Rates.php', ['json' => $normalized]);
            $body        = $apiResponse->getBody()->getContents();
            $decoded     = json_decode($body, true);
            $clean       = $this->formatter->format($decoded);

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
                    'message' => $e->getMessage(),
                    'data'    => null
                ]
            ];
        } catch (RequestException $e) {
            $payload = [
                'status'  => 502,
                'content' => [
                    'success' => false,
                    'message' => 'Failed to fetch rates',
                    'data'    => null
                ]
            ];
        } catch (\Throwable $e) {
            $payload = [
                'status'  => 500,
                'content' => [
                    'success' => false,
                    'message' => 'Unexpected server error',
                    'data'    => null
                ]
            ];
        }

        $response->getBody()->write(
            json_encode($payload['content'], JSON_PRETTY_PRINT)
        );

        return $response
            ->withStatus($payload['status'])
            ->withHeader('Content-Type', 'application/json');
    }
}