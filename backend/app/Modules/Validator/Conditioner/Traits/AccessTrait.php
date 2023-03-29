<?php

namespace App\Modules\Validator\Conditioner\Traits;

use Illuminate\Support\Str;

trait AccessTrait
{
    public function __call($method, $arguments)
    {
        $method = Str::of($method);

        if ($method->startsWith('get') && property_exists($this, $property = $method->replaceFirst('get', '')->lower())) {
            return $this->{$property};
        }

        return null;
    }
}
