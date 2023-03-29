<?php

namespace App\Requests\Calculator;

use App\Core\Requests\Request;

class InstallmentRequest extends Request
{
    public function validator()
    {
        return [
            'price' => 'notEmpty',
            'years' => 'notEmpty'
        ];
    }
}
