<?php

namespace App\Http;

use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Response as BaseResponse;

class NewResponse
{
    public function __construct()
    {
        $response = new Response(new BaseResponse(), new StreamFactory());

        $this->response = $response;
    }

    public function __call($method, $arguments)
    {
        return $this->response->{$method}(...$arguments);
    }
}
