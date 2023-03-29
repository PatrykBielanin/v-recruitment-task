<?php

namespace App\Core\Resources;

use App\Core\Resources\Contracts\ResourceInterface;
use JsonSerializable;

abstract class Resource implements ResourceInterface, JsonSerializable
{
    protected $response;

    public function handle(mixed $response): ResourceInterface
    {
        $this->response = $response;

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return $this->data();
    }

    public function __get($property): mixed
    {
        return $this->response;
    }

    public function toArray(): mixed
    {
        return $this->data();
    }
}
