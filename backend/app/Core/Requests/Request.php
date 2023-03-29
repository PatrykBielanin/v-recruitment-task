<?php

namespace App\Core\Requests;

use App\Core\Route\Route;
use App\Modules\Support\Support;
use App\Modules\Validator\Conditioner\Conditioner;
use Illuminate\Support\Str;

abstract class Request implements RequestInterface
{
    protected static $fields;

    public function process($request)
    {
        $validator = new \App\Modules\Validator\Validator();
        $validator->setErrorPrefix('validator.');

        $returnFieldsBehavior = function () {
            if (method_exists($this, 'returnFieldsBehavior')) {
                return $this->returnFieldsBehavior();
            }

            return null;
        };

        $validator->addSource(
            array_merge(
                $request->getParsedBody() ?? [],
                $request->getQueryParams() ?? []
            )
        )
            ->setFields($this->validator())
            ->addReturnFieldsBehavior($returnFieldsBehavior())
            ->validate();

        if ($validator->passed()) {
            self::$fields = $validator->fields();

            return false;
        }

        return $validator->errors();
    }

    public static function hasRouteWithValidation($request)
    {
        $route = Route::fromRequest($request)->name();

        if ($route && Str::endsWith($route, '.request')) {
            return $route;
        }

        return false;
    }

    public static function parseRouteWithValidation($request, $container)
    {
        $route = self::hasRouteWithValidation($request);
        $class = Support::classNameFromScope($route, ['app', 'requests'], '*.**');

        $class = $container->get($class);

        if ($errors = $class->process($request)) {
            return $errors;
        }

        return false;
    }

    public static function getValidationFields($request)
    {
        if (! self::hasRouteWithValidation($request)) {
            return [];
        }

        return self::$fields;
    }

    public function getConditioner(): Conditioner
    {
        return new Conditioner();
    }
}
