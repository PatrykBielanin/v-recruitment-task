<?php

namespace App\Modules\Validator\Conditioner\Factory;

use Illuminate\Support\Str;

class Parser
{
    public static function isArray($field, $value)
    {
        return Str::contains($field, '*') && is_array($value);
    }

    public static function replaceField($field, $index, $isArray, $lastField)
    {
        if (! $isArray && $lastField === null) {
            return $field;
        }

        return Str::replaceFirst('*', $lastField ?? $index, $field);
    }

    public static function fields($field, $value, $condition)
    {
        if (self::isArray($field, $value)) {
            return self::passedKeys($value, $condition);
        }

        return [0];
    }

    public static function passedKeys($values, $condition)
    {
        return collect($values)->filter(function ($value) use ($condition) {
            return $condition->getReverse() ? $value !== $condition->getValue() : $value == $condition->getValue();
        })->keys();
    }
}
