<?php

namespace App\Http;

use App\Modules\File\File;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response as BaseResponse;

class Response extends BaseResponse
{
    public function withJson($data, ?int $status = null, int $options = 0, int $depth = 512): ResponseInterface
    {
        $status = $status ?? 200;

        return parent::withJson([
            'data' => $data,
            'state' => $status,
            'meta' => [],
        ], $status, $options, $depth);
    }

    public function withJsonValidatorMessage($data, $status, $keyMessage = 'message'): ResponseInterface
    {
        return $this->withJson(
            [$keyMessage => $data],
            $status
        );
    }

    public function preview(File $file): ResponseInterface
    {
        return $this
            ->withHeader('Content-Type', $file->mimeType())
            ->write($file->contents());
    }
}
