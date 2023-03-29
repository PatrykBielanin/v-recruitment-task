<?php

use App\Core\Factory\Factory;

if (! function_exists('factory')) {
    function factory(string $class)
    {
        return Factory::make($class);
    }
}
