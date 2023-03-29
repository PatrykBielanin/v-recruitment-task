<?php

namespace App\Services;

class CalculatorService
{
	public function __construct()
	{
	}

	public function calculateItem(?object $item): Array
	{
		$insurence['price'] = $this -> calculatePrice($item -> price, $item -> year, $item -> gps);

		return $insurence;
	}

	protected function calculatePrice(?float $price, ?int $year, ?int $hasGPS): float
	{
		$isVehicleNew = $this -> isVehicleNew($year);
		$factor = $this -> getFactor($price);

		$factor = $hasGPS ? $factor : ($factor + 11);

		if(! $isVehicleNew){
			return round((($factor + 1) / 100) * $price, 2);
		}

		return round(($factor / 100) * $price, 2);
	}

	protected function getFactor(?float $price): int
	{
		if($price < 400000){
			return 8;
		}

		if($price >= 400000 && $price < 1000000){
			return 5;
		}

		if($price >= 1000000 && $price < 2000000){
			return 4;
		}

		if($price >= 2000000 && $price < 4000000){
			return 2;
		}

		return 0;
	}

	protected function isVehicleNew(?int $year): bool
	{
		return $year >= 2022 ? true : false;
	}

	public function calcuateInstallment($item)
	{
		$installment['price'] = round($item -> price + ((($item -> years / 100) * $item -> price) + 200), 2);

		return $installment;
	}
}
