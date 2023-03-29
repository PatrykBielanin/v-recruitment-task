<?php

use Slim\App as Slim;
use Slim\Factory\AppFactory;
use Noodlehaus\Config;
use App\Modules\File\File;
use App\Core\Url\Url;
use App\Core\Route\Route;
use App\Core\Views\MixExtension;
use Slim\Views\Twig;
use App\Modules\Support\ConditionalResolver;

return [
	Slim::class => function ($container) {
		AppFactory::setContainer($container);

		return AppFactory::create();
	},

	Route::class => function ($container) {
		return Route::fromApp(
			$container->get(Slim::class)
		);
	},

	Config::class => function () {
		return new Config(
		  File::factory()->from(getenv('APP_CONFIG'))->path()
		);
	},

	Url::class => function ($container) {
		$config = $container->get(Config::class);

		return factory(Url::class)->setFallbackBasePath($config->get('baseUrl'));
	},
];
