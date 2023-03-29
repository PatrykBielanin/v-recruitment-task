<?php

namespace App\Core\Factory;

class Factory
{
    protected $class;

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public function __call(string $method, array $arguments): mixed
    {
        return $this->instance()->$method(...$arguments);
    }

    public function __get(string $property): mixed
    {
        return $this->instance()->$property;
    }

    public static function make(string $class): Factory
    {
        return new self($class);
    }

    public function resolve(mixed ...$arguments): object
    {
        return $this->instance($arguments);
    }

    protected function instance(array $arguments = []): object
    {
        $className = $this->class;

        return new $className(...$arguments);
    }
}
