<?php

use App\Modules\File\File;
use App\Http\RequestResponseArg;
use Dotenv\Dotenv;
use Dotenv\Loader;
use Dotenv\Environment\DotenvFactory;
use Dotenv\Exception\InvalidPathException;
use Slim\App;
use Noodlehaus\Config;

require_once __DIR__.'/../vendor/autoload.php';

try {
    factory(Dotenv::class)->resolve(new Loader([File::factory()->from()->path() . '.env'], new DotenvFactory(), false))->load();
} catch (InvalidPathException $e) {
    exit;
}

$containerBuilder = new DI\ContainerBuilder();
$containerBuilder->addDefinitions(File::factory()->from('bootstrap/definitions.php')->path());

$container = $containerBuilder->build();

$app = $container->get(App::class);
$config = $container->get(Config::class);

$routeCollector = $app->getRouteCollector();
$routeCollector->setDefaultInvocationStrategy(new RequestResponseArg());

require_once __DIR__.'/errors.php';
require_once __DIR__.'/../routes/api.php';
