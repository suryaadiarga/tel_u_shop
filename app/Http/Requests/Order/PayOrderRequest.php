<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class PayOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'string', 'max:32'], // e.g. CASH, QRIS
        ];
    }
}