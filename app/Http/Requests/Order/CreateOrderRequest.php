<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_id'       => ['required','string','max:64'],
            'items'             => ['required','array','min:1'],
            'items.*.product_id'=> ['required','string','max:32'],
            'items.*.qty'       => ['required','integer','min:1','max:50'],
            'notes'             => ['sometimes','string','max:500'],
        ];
    }
}
