<?php

namespace App\Modules\Support;

use Illuminate\Support\Str;

class Url
{
    public static function buildQuery(?string $url, array $params): ?string
    {
        $url = Str::of($url)->rtrim('?');

        if (count($params)) {
            $url .= '?'.http_build_query($params);
        }

        return $url;
    }

    public static function buildFromString(?string $string, bool $https = false): ?string
    {
        if (! $string) {
            return null;
        }

        $string = Str::of($string);

        if (! $string->startsWith('https://') && ! $string->startsWith('http://')) {
            $string = $string->prepend($https ? 'https://' : 'http://');
        }

        return (string) $string;
    }

    public static function parse(?string $url): ?string
    {
        $url = Str::of($url)
            ->replaceFirst('www.', '')
            ->replaceFirst('http://', '')
            ->replaceFirst('https://', '')
            ->rtrim('/');

        return (string) $url;
    }

    public static function root(?string $url): ?string
    {
        if (Str::contains($url, '/')) {
            return self::root((string) Str::beforeLast(self::parse($url), '/'));
        }

        return $url;
    }

    public static function enshureHost(string $url, string $host): string
    {
        return Str::start($url, $host);
    }
}
