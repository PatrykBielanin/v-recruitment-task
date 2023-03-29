<?php

namespace App\Modules\File;

use App\Modules\File\Traits\AttributesTrait;
use App\Modules\File\Resource;
use App\Modules\Support\Support;
use Exception;
use Illuminate\Support\Str;
use Spatie\Watcher\Watch;

class File
{
    protected $file;
    protected $origin;

    protected $selfPath = 'app/Modules/File';

    use AttributesTrait;

    public static function factory(): File
    {
        return new self();
    }

    public static function temporary(): Temporary
    {
        return new Temporary();
    }

    public function __call(string $method, array $arguments)
    {
        $method = (string) Str::of($method)->camel()->ucFirst()->prepend('get')->append('Attribute');

        if (method_exists($this, $method)) {
            return $this->{$method}(...$arguments);
        }

        return null;
    }

    public function relative(string $file): File
    {
        $this->file = $file;

        return $this;
    }

    public function from(?string $file = null): File
    {
        $this->relative(
            (string) Str::of(__DIR__)->replaceFirst($this->selfPath, '')->finish('/').$file
        );

        return $this;
    }

    public function create(string|array $data): File
    {
        $data = Support::jsonEncodable($data) ?: $data;

        FileSystem::create($this->file, $data);

        return $this;
    }

    public function delete(): File
    {
        if (FileSystem::isDirectory($this->file)) {
            FileSystem::deleteDirectory($this->file);

            return $this;
        }

        FileSystem::delete($this->file);

        return $this;
    }

    public function createDirectoryTree(int $permissions = 0777): File
    {
        FileSystem::createDir($this->file, $permissions);

        return $this;
    }

    public function directory(): File
    {
        $this->origin = $this->file;
        $this->relative(
            Str::of($this->file)->replaceLast($this->name(), '')->rtrim(DIRECTORY_SEPARATOR)
        );

        return $this;
    }

    public function origin(): File
    {
        return $this->relative($this->origin);
    }

    public function copyTo(string $destination): File
    {
        FileSystem::copy($this->file, $destination);

        return $this;
    }

    public function enshureUser(string $user = 'www-data'): File
    {
        FileSystem::chown($this->file, $user);

        return $this;
    }
}
