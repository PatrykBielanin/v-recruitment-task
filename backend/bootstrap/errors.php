<?php

use App\Middlewares\BodyParsingMiddleware;

$app->add(new BodyParsingMiddleware());
$app->addRoutingMiddleware();

$app->add(
    new Zeuxisoo\Whoops\Slim\WhoopsMiddleware([
        'enable' => $config->get('debug'),
    ])
);

if (! $config->get('debug')) {
    $app->addErrorMiddleware(
        false,
        false,
        false
    );
}
