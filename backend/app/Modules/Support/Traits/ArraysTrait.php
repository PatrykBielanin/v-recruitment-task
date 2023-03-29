<?php

namespace App\Modules\Support\Traits;

use ArrayAccess;
use Illuminate\Support\Str;

trait ArraysTrait
{
    public static function nestedArray(ArrayAccess $elements, int $parentId = 0, string $field = 'parent_id'): array
    {
        $branch = [];

        foreach ($elements as $element) {
            if ($element->$field == $parentId) {
                $element->childrens = self::nestedArray($elements, $element->id, $field) ?? [];

                $branch[] = $element;
            }
        }

        return $branch;
    }

    public static function mapRecursive(array $array, callable $callback, bool $withKeys = false, ?string $oldKey = null): array
    {
        $mapFunction = $withKeys ? 'mapWithKeys' : 'map';

        $collection = collect($array)
            ->$mapFunction(function ($item, $key) use ($callback, $withKeys) {
                if (is_array($item)) {
                    return self::mapRecursive($item, $callback, $withKeys, $withKeys ? $key : null);
                }

                return $callback($item, $key);
            })
            ->toArray();

        if ($oldKey) {
            return [$oldKey => $collection];
        }

        return $collection;
    }

    public static function stringOrDecodedJson(?string $string, bool $asArray = false): mixed
    {
        $encoded = json_decode($string, $asArray);

        if (json_last_error() == JSON_ERROR_NONE) {
            return $encoded;
        }

        return $string;
    }

    public static function jsonEncodable(mixed $value, bool $force = false): bool|string
    {
        if ($force || (is_object($value) || is_array($value))) {
            return json_encode($value);
        }

        return false;
    }

    public static function toMongoKeys(array $values, string $replaceWith = '_'): array
    {
        return self::mapRecursive($values, function ($value, $key) use ($replaceWith) {
            return [(string) Str::of($key)->replace('.', $replaceWith) => $value];
        }, true);
    }

	public static function contains(mixed $key, array $array): bool
	{
		return in_array($key, $array);
	}
}
