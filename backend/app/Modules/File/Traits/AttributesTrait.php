<?php

namespace App\Modules\File\Traits;

use App\Modules\File\FileSystem;
use App\Modules\Support\Support;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait AttributesTrait
{
    public function getExtensionAttribute(): string
    {
        return FileSystem::extension($this->file);
    }

    public function getPathAttribute(): ?string
    {
        return $this->file;
    }

    public function getNameAttribute(?string $extension = null): string
    {
        $name = FileSystem::fileName($this->file);

        if ($extension) {
            return Str::of($name)->replaceLast($this->extension(), $extension);
        }

        return $name;
    }

    public function getExistsAttribute(): bool
    {
        return FileSystem::exists($this->file);
    }

    public function getContentsAttribute(bool $expectJson = false, bool $jsonToArray = false, bool $external = false): bool|object|string|array
    {
        $contents = FileSystem::open($this->file, $external);

        if ($contents === false) {
            return $contents;
        }

        if ($expectJson) {
            return Support::stringOrDecodedJson($contents, $jsonToArray);
        }

        return $contents;
    }

    public function getListAttribute(?string $extension = null): Collection
    {
        if (! FileSystem::isDirectory($this->file)) {
            return collect();
        }

        $list = FileSystem::list($this->file)->map(fn ($file) => self::factory()->relative($file));

        if ($extension) {
            return $list->filter(fn ($file) => $file->extension() == Str::lower($extension));
        }

        return $list;
    }

    public function getListRecursiveAttribute(?Collection $list = null, ?Collection $prepared = null): Collection
    {
        $prepared = $prepared ?? collect();
        $list = $list ?? $this->list();

        foreach ($list as $file) {
            if (FileSystem::isDirectory($file->path())) {
                $prepared = $this->listRecursive($file->list(), $prepared);

                continue;
            }

            $prepared[] = $file;
        }

        return collect($prepared)->sortByDesc(function (self $file) {
            return count(explode(DIRECTORY_SEPARATOR, $file->path()));
        });
    }

    public function getSizeAttribute(bool $humanReadable = false): float|string
    {
        if (! $this->exists()) {
            return 0;
        }

        $toHumanReadable = function ($sizeInBytes) {
            $sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
            $size = floor(log($sizeInBytes, 1024));

            return round($sizeInBytes / pow(1024, $size), 2).$sizes[$size];
        };

        $collection = FileSystem::isDirectory($this->file) ? $this->listRecursive() : collect([$this]);

        $totalSizeInBytes = $collection->map(fn ($file) => FileSystem::size($file->path()))->sum();

        if ($humanReadable) {
            return $toHumanReadable($totalSizeInBytes);
        }

        return $totalSizeInBytes;
    }

    public function getTimeAttribute(): Carbon
    {
        return FileSystem::time($this->file);
    }

    public function getMimeTypeAttribute(): string|bool
    {
        return FileSystem::mimeType($this->file);
    }

    public function getBase64Attribute(): string
    {
        return base64_encode($this->contents());
    }
}
