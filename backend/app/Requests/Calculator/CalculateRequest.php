<?php

namespace App\Requests\Calculator;

use App\Core\Requests\Request;

class CalculateRequest extends Request
{
    public function validator()
    {
        return [
            'price' => 'notEmpty',
            'year' => 'notEmpty',
            'gps' => 'Boolean'
        ];
    }
}
