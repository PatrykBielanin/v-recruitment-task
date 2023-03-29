<?php

namespace App\Core\Route;

use App\Modules\Support\Url;
use Exception;
use Slim\App;
use Slim\Http\ServerRequest;
use Slim\Interfaces\RouteInterface;
use Slim\Routing\RouteCollector;
use  Slim\Routing\RouteContext;
use Slim\Routing\RouteParser;

class Route
{
    protected $context;
    protected $routeCollector;

    public function __construct(RouteContext|RouteParser $context, ?RouteCollector $routeCollector = null)
    {
        $this->context = $context;
        $this->routeCollector = $routeCollector;
    }

    public static function fromRequest(ServerRequest $request): Route
    {
        return new self(
            RouteContext::fromRequest($request)
        );
    }

    public static function fromApp(App $app): Route
    {
        return new self(
            $app->getRouteCollector()->getRouteParser(),
            $app->getRouteCollector()
        );
    }

    public function get(): RouteInterface|RouteParser
    {
        if (! $this->isContextFromRequest()) {
            return $this->context;
        }

        return $this->context->getRoute();
    }

    public function routeCollector(): RouteCollector
    {
        if ($this->isContextFromRequest()) {
            throw new Exception('Cannot reach route collector outside app factory');
        }

        return $this->routeCollector;
    }

    public function name(): ?string
    {
        if (! $this->isContextFromRequest()) {
            throw new Exception('Cannot reach route name from app factory');
        }

        if (empty($route = $this->get())) {
            return null;
        }

        return $route->getName();
    }

    public function pathFor(string $name, array $params = [], array $queryParams = [], ?string $host = null): string
    {
        $factory = $this->isContextFromRequest() ? $this->context->getRouteParser()
      : $this->context;

        $url = $factory->urlFor($name, $params, $queryParams);

        if ($host) {
            return Url::enshureHost($url, $host);
        }

        return $url;
    }

    protected function isContextFromRequest(): bool
    {
        return $this->context instanceof RouteContext;
    }
}
