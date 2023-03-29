<?php

namespace App\Modules\Support;

use Exception;
use Illuminate\Support\Collection;

class ConditionalResolver
{
	protected mixed $default;
    protected bool $multiple = false;
	protected Collection $resolvers;

    public function __construct(mixed $default)
    {
		$this->default = $default;
		$this->resolvers = collect();
    }

    public static function toValue(mixed $value, mixed $params = null, bool $getParamsIfNotCallable = false): mixed
    {
        return is_callable($value) ? $value($params) : ($getParamsIfNotCallable ? $params : $value);
    }

    public static function default(mixed $default): self
    {
        return new self($default);
    }

    public function mutliple(bool $mode): self
    {
        $this->multiple = $mode;

        return $this;
    }

    public function add(mixed $value, bool $condition): self
    {
        if ($condition) {
            $this->resolvers->push(self::toValue($value));
        }

        return $this;
    }

    public function resolve(?callable $callback = null): mixed
    {
        return self::toValue($callback, $this->current(), true);
    }

    protected function current(): mixed
    {
        if (! $this->resolvers->count()) {
            return self::toValue($this->default);
        }

        if ($this->multiple) {
            return $this->resolvers->first();
        }

        if ($this->resolvers->count() === 1) {
            return $this->resolvers->first();
        }

        throw new Exception('Only one option must be provided');
    }
}
