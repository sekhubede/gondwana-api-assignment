<?php
use Slim\Factory\AppFactory;

require __DIR__ . '/../../vendor/autoload.php';

$app = AppFactory::create();

// Simple test route
$app->get('/', function ($request, $response) {
    $response->getBody()->write("Hello, world!");
    return $response;
});

$app->run();