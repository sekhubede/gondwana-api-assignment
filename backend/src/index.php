<?php
use Slim\Factory\AppFactory;
use Slim\Exception\HttpNotFoundException;
use Slim\Middleware\ErrorMiddleware;
use App\Controllers\BookingController;
use App\Services\PayloadTransformer;
use App\Services\ResponseFormatter;
use GuzzleHttp\Client;

require_once __DIR__ . '/../../vendor/autoload.php';

$app = AppFactory::create();

// Middleware
$app->addBodyParsingMiddleware();

// Error handling middleware
$displayErrorDetails = false;   // set to false in production
$logErrors = true;
$logErrorDetails = true;
$errorMiddleware = new ErrorMiddleware(
    $app->getCallableResolver(),
    $app->getResponseFactory(),
    $displayErrorDetails,
    $logErrors,
    $logErrorDetails
);
$app->add($errorMiddleware);

// Health check route
$app->get('/', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'status'  => 'ok',
        'message' => 'API is running'
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Dependencies
$transformer = new PayloadTransformer();
$formatter   = new ResponseFormatter();
$httpClient  = new Client([
    'base_uri' => 'https://dev.gondwana-collection.com/Web-Store/Rates/',
    'timeout'  => 10.0,
]);

// Controllers
$bookingController = new BookingController($transformer, $formatter, $httpClient);

// Routes
$app->post('/rates', [$bookingController, 'calculateRates']);

$app->run();