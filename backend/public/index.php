<?php

use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;
use App\Controllers\BookingController;
use App\Services\PayloadTransformer;
use App\Services\ResponseFormatter;
use GuzzleHttp\Client;

require_once __DIR__ . '/../../vendor/autoload.php';

$app = AppFactory::create();

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withHeader('Access-Control-Max-Age', '3600');
});

$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});

$app->addBodyParsingMiddleware();

$errorMiddleware = new ErrorMiddleware(
    $app->getCallableResolver(),
    $app->getResponseFactory(),
    false,
    true,
    true
);
$app->add($errorMiddleware);

$app->get('/', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'status'  => 'ok',
        'message' => 'API is running'
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

$transformer = new PayloadTransformer();
$formatter   = new ResponseFormatter();
$httpClient  = new Client([
    'base_uri' => 'https://dev.gondwana-collection.com/Web-Store/Rates/',
    'timeout'  => 10.0,
]);

$bookingController = new BookingController($transformer, $formatter, $httpClient);

$app->get('/rates', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'status'  => 'ok',
        'message' => 'Rates endpoint is alive — use POST to calculate rates'
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/rates', [$bookingController, 'calculateRates']);

$app->run();