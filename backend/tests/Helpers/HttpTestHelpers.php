<?php
namespace Tests\Helpers;

use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

trait HttpTestHelpers
{
    protected ServerRequestFactory $requestFactory;
    protected ResponseFactory $responseFactory;

    protected function setUpHttp(): void
    {
        $this->requestFactory = new ServerRequestFactory();
        $this->responseFactory = new ResponseFactory();
    }

    protected function makeRequest(string $method = 'POST', string $uri = '/rates')
    {
        return $this->requestFactory->createServerRequest($method, $uri);
    }

    protected function makeResponse()
    {
        return $this->responseFactory->createResponse();
    }
}
