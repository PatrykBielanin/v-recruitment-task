<?php

use App\Controllers\Calculator\CalculatorController;

$app->group('/api', function($group) {
	$group->options('/{routes:.+}', function ($request, $response) {
		return $response;
	});

	$group->group('/calculator', function($group) {
		$group->post('/price', CalculatorController::class . ':calculate')
			->setName('calculator.calculate.request');
		$group->post('/installment', CalculatorController::class . ':installment')
			->setName('calculator.installment.request');
	});
})
	->add($container->get(App\Middlewares\RequestsMiddleware::class));
