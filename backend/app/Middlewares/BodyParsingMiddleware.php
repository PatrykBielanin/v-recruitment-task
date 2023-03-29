<?php

namespace App\Middlewares;

use App\Modules\File\File;
use App\Modules\Support\Support;

class BodyParsingMiddleware
{
    public function __invoke($request, $handler)
    {
        $contentType = $request->getHeaderLine('Content-Type');

        if (strstr($contentType, 'application/json')) {
            $contents = File::factory()->relative('php://input')
                ->contents(true, true, true);

            if (Support::jsonEncodable($contents)) {
                $request = $request->withParsedBody(
                    $this->cleanBody($contents)
                );
            }
        }

        $request = $request->withQueryParams(
            $this->cleanQueryParams($request)
        );

        return $handler->handle($request);
    }

    protected function cleanQueryParams($request)
    {
        return collect($request->getQueryParams())->map(function ($param) {
            if (Support::contains($param, ['true', 'false'])) {
                return filter_var($param, FILTER_VALIDATE_BOOLEAN);
            }

            return Support::trimStringOrNullWhenEmpty($param);
        })->toArray();
    }

    protected function cleanBody($contents)
    {
        return Support::mapRecursive($contents, function ($item) {
            if (! is_string($item)) {
                return $item;
            }

            return Support::trimStringOrNullWhenEmpty($item);
        });
    }
}
