<?php

namespace App\Http;

use Illuminate\Support\Arr;
use Slim\Http\ServerRequest;

class Request extends ServerRequest
{
    public function only(...$keys)
    {
        $keys = (array) $keys;

        return array_replace(
            collect(array_flip($keys))->map(function ($item) {
                return null;
            })->toArray(),
            Arr::only(
                array_merge($this->getParsedBody() ?? [], $this->getQueryParams()),
                $keys
            )
        );
    }

    public function onlyValues(...$keys)
    {
        return array_values($this->only(...$keys));
    }

    public function fromValidator($keys = false)
    {
        $method = $keys ? 'onlyValues' : 'only';

        return $this->{$method}(...$this->getAttribute('validationPassedFields'));
    }
}
