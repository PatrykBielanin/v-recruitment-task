<?php

namespace App\Controllers\Calculator;

use App\Services\CalculatorService;

class CalculatorController
{
	public function __construct(protected CalculatorService $calculatorService)
	{
	}

	public function calculate($request, $response)
	{
		$item = (object) $request -> fromValidator();

		$calculated = $this -> calculatorService -> calculateItem($item);

		return $response -> withJson($calculated);
	}

	public function installment($request, $response)
	{
		$item = (object) $request -> fromValidator();

		$installment = $this -> calculatorService -> calcuateInstallment($item);

		return $response -> withJson($installment);
	}
}
