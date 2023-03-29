<?php

namespace App\Modules\Validator\Conditioner\Factory;

use App\Modules\Validator\Conditioner\Conditioner;
use App\Modules\Validator\Conditioner\Traits\AccessTrait;

class Condition
{
    protected $pair;

    protected $value;
    protected $reverse;
    protected $callback;

    protected $conditioner;

    use AccessTrait;

    public function __construct(Pair $pair)
    {
        $this->pair = $pair;
    }

    public function value($value)
    {
        $this->value = $value;

        return $this;
    }

    public function reverse($reverse)
    {
        $this->reverse = $reverse;

        return $this;
    }

    public function callback($callback)
    {
        if (is_callable($callback)) {
            $this->callback = $callback;
        }

        return $this;
    }

    public function then(callable $callback)
    {
        $this->conditioner = new Conditioner();

        $callback($this->conditioner);

        return $this->getPair();
    }

    public function compare($validatorValue, $validatorField, $validatorPayload)
    {
        return factory(Compare::class)->resolve($this)
            ->value($validatorValue)
            ->field($validatorField)
            ->payload($validatorPayload);
    }
}
