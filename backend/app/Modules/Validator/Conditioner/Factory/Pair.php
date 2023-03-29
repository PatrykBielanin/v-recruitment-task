<?php

namespace App\Modules\Validator\Conditioner\Factory;

use App\Modules\Validator\Conditioner\Conditioner;
use App\Modules\Validator\Conditioner\Traits\AccessTrait;

class Pair
{
    protected $conditioner;

    protected $rule;
    protected $field;

    protected $conditions = [];

    use AccessTrait;

    public function __construct(Conditioner $conditioner)
    {
        $this->conditioner = $conditioner;
    }

    public function field($field)
    {
        $this->field = $field;

        return $this;
    }

    public function rule($rule)
    {
        $this->rule = $rule;

        return $this;
    }

    public function whenValue($value, $reverse = false)
    {
        return $this->setCondition($value, $reverse);
    }

    public function whenValueAndPayload($value, callable $callback, $reverse = false)
    {
        return $this->setCondition($value, $reverse, $callback);
    }

    protected function setCondition($value, $reverse, $callback = null)
    {
        $condition = factory(Condition::class)->resolve($this)
            ->value($value)
            ->reverse($reverse)
            ->callback($callback);

        $this->conditions[] = $condition;

        return $condition;
    }
}
