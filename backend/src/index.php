<?php
use Slim\Factory\AppFactory;
use App\Controllers\BookingController;
use App\Services\PayloadTransformer;

require_once __DIR__ . '/../../vendor/autoload.php';

$app = AppFactory::create();

// Add middleware to parse JSON request bodies
$app->addBodyParsingMiddleware();

// Simple test route
$app->get('/', function ($_request, $response) {
    $response->getBody()->write("Hello, World!");
    return $response;
});

$transformer = new PayloadTransformer();
$bookingController = new BookingController($transformer);

// New route for POST /rates
$app->post('/rates', [$bookingController, 'calculateRates']);

$app->run();