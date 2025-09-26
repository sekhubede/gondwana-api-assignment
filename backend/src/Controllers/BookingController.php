<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\PayloadTransformer;

class BookingController
{
    private PayloadTransformer $transformer;

    public function __construct(PayloadTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function calculateRates(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        // Transform input into normalized payload
        $normalized = $this->transformer->transform($data);

        // For now: just return transformed payload as JSON
        $response->getBody()->write(json_encode($normalized, JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    }
}