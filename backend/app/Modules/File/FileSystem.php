<?php

namespace App\Modules\File;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FileSystem
{
    public static function open(string $path, bool $external = false): bool|string
    {
        if (! self::exists($path) && ! $external) {
            return false;
        }

        return file_get_contents($path);
    }

    public static function create(string $path, string $data): bool
    {
        return file_put_contents($path, $data);
    }

    public static function createDir(string $path, int $permissions = 0777): bool
    {
        if (self::isDirectory($path)) {
            return true;
        }

        $umask = umask(0);
        $created = mkdir($path, $permissions, true);
        umask($umask);

        return $created;
    }

    public static function delete(string $path): bool
    {
        if (! self::exists($path)) {
            return false;
        }

        unlink($path);

        return true;
    }

    public static function deleteDirectory(string $path): bool
    {
        if (! self::isDirectory($path)) {
            return false;
        }

        rmdir($path);

        return true;
    }

    public static function exists(string $path): bool
    {
        return file_exists($path);
    }

    public static function extension(string $path): string
    {
        return Str::of(pathinfo($path, PATHINFO_EXTENSION))
            ->before('?')
            ->lower();
    }

    public static function list(string $path): Collection
    {
        return collect(glob(Str::finish($path, '/').'{,.}*', GLOB_BRACE))
            ->filter(fn (string $path) => ! Str::endsWith($path, '.'));
    }

    public static function isDirectory(string $path): bool
    {
        return is_dir($path);
    }

    public static function fileName(string $path): string
    {
        return basename($path);
    }

    public static function copy(string $from, string $to): bool
    {
        return copy($from, $to);
    }

    public static function size(string $path): int
    {
        return filesize($path);
    }

    public static function time(string $path): Carbon
    {
        return Carbon::parse(filemtime($path));
    }

    public static function mimeType(string $path): string|bool
    {
        return mime_content_type($path);
    }

    public static function chown(string $path, string $user): bool
    {
        return chown($path, $user);
    }
}
