<?php

namespace App\Modules\Validator;

use App\Modules\Support\Url;
use App\Modules\Support\Support;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use PHP_IBAN\IBAN;

class RulesCollector
{
    protected $source;
    protected $orPrefix;

    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    public function setOrPrexix($prefix)
    {
        $this->orPrefix = $prefix;

        return $this;
    }

    public function get($value, $rule, $param)
    {
        $rule = Str::replaceLast($this->orPrefix, '', $rule);

        if (! method_exists($this, $rule)) {
            return false;
        }

        return $this->{$rule}($value, $param);
    }

    public function null($field)
    {
        return is_null($field);
    }

    public function notEmpty($value)
    {
        return ! empty($value);
    }

    public function email($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function min($value, $min)
    {
        return strlen($value) >= (int) $min;
    }

    public function max($value, $max)
    {
        return strlen($value) <= (int) $max;
    }

    public function numberMin($value, $min)
    {
        return (float) $value >= (float) $min;
    }

    public function numberMax($value, $max)
    {
        return (float) $value <= (float) $max;
    }

    public function sameAs($field, $sameAs)
    {
        return Arr::get($this->source, $sameAs) == $field;
    }

    public function numberWithLeadingZero($number)
    {
        $number = (string) $number;

        if (strlen($number) == 1) {
            return false;
        }

        return $number[0] == 0;
    }

    public function float($float)
    {
        if (! $this->notEmpty($float)) {
            return false;
        }

        if ($this->numberWithLeadingZero($float)) {
            return false;
        }

        return filter_var($float, FILTER_VALIDATE_FLOAT) !== false;
    }

    public function integer($int)
    {
        if (! $this->notEmpty($int)) {
            return false;
        }

        if ($this->numberWithLeadingZero($int)) {
            return false;
        }

        return $this->trueInteger($int);
    }

    public function trueInteger($int)
    {
        return filter_var($int, FILTER_VALIDATE_INT) !== false;
    }

    public function unsigned($int)
    {
        return (float) $int > 0;
    }

    public function boolean($boolean)
    {
        $booleans = collect([true, false, 0, 1]);

        return $booleans->filter(function ($bool) use ($boolean) {
            return $bool === $boolean;
        })->count() != 0;
    }

    public function isTrue($value)
    {
        return $value === true;
    }

    public function isFalse($value)
    {
        return $value === false;
    }

    public function in($field, $search)
    {
        return Support::contains($field, explode(',', $search));
    }

    public function notIn($field, $search)
    {
        return ! $this->in($field, $search);
    }

    public function singleString($string)
    {
        return count(explode(' ', $string)) === 1;
    }

    public function rgbColor($color)
    {
        return Str::startsWith($color, '#') && strlen($color) === 7;
    }

    public function url($url)
    {
        $fluent = Str::of($url);
        if (! $fluent->startsWith('http://') && ! $fluent->startsWith('https://')) {
            return false;
        }

        return filter_var($url, FILTER_VALIDATE_URL) === $url;
    }

    public function rootDomain($url)
    {
        $matrix = Str::of(Url::parse($url));

        $exploded = $matrix->explode('.');

        if ($exploded->count() <= 1) {
            return false;
        }

        if (Str::of($exploded->last())->trim()->isEmpty()) {
            return false;
        }

        if ((string) $matrix !== $url) {
            return false;
        }

        return true;
    }

    public function alias($alias)
    {
        return count(explode('@', $alias)) == 2;
    }

    public function date($date, $pattern)
    {
        try {
            $carbonDate = Carbon::createFromFormat($pattern, $date);

            return $carbonDate->format($pattern) === $date;
        } catch (Exception $e) {
            return false;
        }
    }

    public function emptyArray($array)
    {
        return is_array($array) && count($array) == 0;
    }

    public function uppercase($string)
    {
        return strtoupper($string) === $string;
    }
}
