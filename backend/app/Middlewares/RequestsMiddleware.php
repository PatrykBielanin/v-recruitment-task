<?php

namespace App\Middlewares;

use App\Core\Requests\Request;
use App\Core\Route\Route;
use App\Http\NewResponse;
use App\Http\Response;
use DI\Container;
use Illuminate\Support\Collection;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class RequestsMiddleware
{
    protected $contianer;

    protected const API_ROUTE_PATTERN = '/api';

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $handler)
    {
        if (Request::hasRouteWithValidation($request)) {
            if ($errors = Request::parseRouteWithValidation($request, $this->container)) {
                return $this->respondWithError($errors, $request);
            }

            $request = $request->withAttribute('validationPassedFields', Request::getValidationFields($request));
        }

        return $handler->handle($request);
    }

    protected function respondWithError(Collection $errors, ServerRequestInterface $request): Response
    {
        if (! $this->isApiRequest($request)) {
            throw new HttpNotFoundException($request);
        }

        return factory(NewResponse::class)->withJson($errors, 400);
    }

    protected function isApiRequest(ServerRequestInterface $request): bool
    {
        return collect(Route::fromRequest($request)->get()->getGroups())
            ->filter(function ($group) {
                return $group->getPattern() === self::API_ROUTE_PATTERN;
            })->count() !== 0;
    }
}
