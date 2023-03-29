<?php

namespace App\Modules\Validator\Conditioner\Factory;

class Compare
{
    protected $condition;

    protected $field;
    protected $value;
    protected $payload;

    public function __construct(Condition $condition)
    {
        $this->condition = $condition;
    }

    public function value($value)
    {
        $this->value = $value;

        return $this;
    }

    public function field($field)
    {
        $this->field = $field;

        return $this;
    }

    public function payload($payload)
    {
        $this->payload = $payload;

        return $this;
    }

    public function passed()
    {
        $passed = $this->passedValue();

        if ($this->isArray()) {
            $passed = Parser::passedKeys($this->value, $this->condition)->count();
        }

        if ($callback = $this->condition->getCallback()) {
            return $passed && $callback($this->payload);
        }

        return $passed;
    }

    protected function isArray()
    {
        return Parser::isArray($this->field, $this->value);
    }

    protected function passedValue($reverse = false)
    {
        if ($this->condition->getReverse() && ! $reverse) {
            return ! $this->passedValue(true);
        }

        return $this->value == $this->condition->getValue();
    }
}
