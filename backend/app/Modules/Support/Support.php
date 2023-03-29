<?php

namespace App\Modules\Support;

use App\Modules\Support\Traits\ArraysTrait;
use App\Modules\Support\Traits\PriceTrait;
use App\Modules\Support\Traits\RolesTrait;
use App\Modules\Support\Traits\StringsTrait;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Support\Str;

class Support
{
    use PriceTrait;
    use RolesTrait;
    use ArraysTrait;
    use StringsTrait;

    protected static int $vatAmount;

    public static function setVatAmount(int $vatAmount): void
    {
        static::$vatAmount = $vatAmount;
    }

    public static function jsonDump(mixed $data): void
    {
        echo '<pre>';
        echo self::jsonEncodable($data, true);
        exit();
    }

    public static function queryLog(): void
    {
        self::jsonDump(
            Manager::getQueryLog()
        );
    }

    public static function constant(string $class, string $constant): mixed
    {
        $constant = $class.Str::of($constant)->start('::')->upper();

        if (! defined($constant)) {
            return null;
        }

        return constant($constant);
    }
}
