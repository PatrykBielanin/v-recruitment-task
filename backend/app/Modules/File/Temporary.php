<?php

namespace App\Modules\File;

use Illuminate\Support\Str;

final class Temporary extends File
{
    protected $preserveParent = false;
    protected $temporaryDirectoryPath = 'tmp';
    protected $baseTemporaryPath = 'resources/storage/tmp/';

    public function file(?string $fileName = null): Temporary
    {
        return $this->inParent(
            $this->tree($this->path() ? $this->name() : $this->directory()->name(), $fileName)
        );
    }

    public function directory(?string $directoryName = null, bool $parent = false): Temporary
    {
        if ($parent) {
            return parent::directory();
        }

        if ($directoryName) {
            $this->preserveParent = true;
        }

        $this->inParent($this->tree($directoryName));

        $this->createDirectoryTree();

        return $this;
    }

    public function delete(): Temporary
    {
        while ($this->name() !== $this->temporaryDirectoryPath) {
            $this->directory(parent: true);
        }

        $this->origin()->listRecursive()->each(function (File $file) {
            $file->delete();

            if (! $file->directory()->list()->count()) {
                $file->delete();
            }
        });

        if ($this->preserveParent) {
            return $this;
        }

        parent::delete();
        $this->directory(parent: true);

        if ($this->list()->count() == 0) {
            parent::delete();
        }

        return $this;
    }

    public function withParent(): Temporary
    {
        $this->preserveParent = false;

        return $this;
    }

    protected function inParent(string $path): Temporary
    {
        return $this->from($this->baseTemporaryPath.$path);
    }

    protected function tree(...$arguments): string
    {
        return collect([...$arguments])->map(function ($argument) {
            if ($argument === null) {
                return Str::random(8);
            }

            return $argument;
        })->implode(DIRECTORY_SEPARATOR);
    }
}
