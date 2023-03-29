<?php

namespace App\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\InvocationStrategyInterface;
use Slim\Psr7\Factory\StreamFactory;

class RequestResponseArg implements InvocationStrategyInterface
{
    public function __invoke(
        callable $callable,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $routeArguments
    ): ResponseInterface {
        return $callable(new Request($request), new Response($response, new StreamFactory()), (object) $routeArguments);
    }
}
