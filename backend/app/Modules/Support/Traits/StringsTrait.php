<?php

namespace App\Modules\Support\Traits;

use Illuminate\Support\Str;

trait StringsTrait
{
    public static function redisKey(...$arguments): string
    {
        return implode('.', $arguments);
    }

    public static function classNameFromScope(string $scope, array $namespace, string $pattern): string
    {
        $scope = explode('.', $scope);
        $splittedPattern = str_split($pattern);
        $splittedPatternKeys = array_keys($splittedPattern, '*');

        $scope = collect($splittedPattern)
            ->map(function ($letter, $key) use ($scope, $splittedPatternKeys) {
                if (self::contains($key, $splittedPatternKeys)) {
                    $part = $scope[array_search($key, $splittedPatternKeys)] ?? null;

                    if (! $part) {
                        return '';
                    }

                    return Str::ucfirst(Str::camel($part));
                }

                return '\\';
            })
            ->join('');

        $namespace = collect($namespace)
            ->map(function ($n) {
                return (string) Str::of($n)->camel()->ucfirst();
            })
            ->join('\\');

        return $namespace.'\\'.Str::of($scope)->rtrim('\\');
    }

    public static function trimStringOrNullWhenEmpty(?string $string): ?string
    {
        $string = Str::of($string)->replace(chr(0), '')
            ->trim();

        if ($string->isEmpty()) {
            return null;
        }

        return (string) $string;
    }

    public static function wrapString(string $string, string $left = '"', string $right = '"'): string
    {
        return Str::of($string)->start($left)->finish($right);
    }
}
