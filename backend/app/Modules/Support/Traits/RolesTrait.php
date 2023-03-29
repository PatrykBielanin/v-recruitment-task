<?php

namespace App\Modules\Support\Traits;

use Illuminate\Support\Collection;

trait RolesTrait
{
    public static function clientRoles(): Collection
    {
        return collect(['freelancer', 'brand', 'agency']);
    }

    public static function employeeRoles(): Collection
    {
        return collect(['lb', 'cw_art', 'cw']);
    }

    public static function adminRoles(): Collection
    {
        return collect(['admin']);
    }

    public static function publisherRoles(): Collection
    {
        return collect(['publisher']);
    }

    public static function allRoles(bool $allClientRoles = false): Collection
    {
        return collect([
            ...self::adminRoles(),
            ...self::employeeRoles(),
            ...self::publisherRoles(),
            self::clientRoles()->last(),
        ]);
    }
}
