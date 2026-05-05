<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id'      => ['nullable', 'exists:products,id'],
            'customer_id'     => ['nullable', 'exists:customers,id'],
            'vehicle_id'      => ['nullable', 'exists:vehicles,id'],
            'delivery_type'   => ['required', 'in:walk_in,delivery'],
            'quantity'        => ['required', 'numeric', 'min:0.01'],
            'unit_price'      => ['required', 'numeric', 'min:0'],
            'discount_type'   => ['nullable', 'in:fixed,percent'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_status'  => ['required', 'in:paid,partial,unpaid'],
            'amount_paid'     => ['nullable', 'numeric', 'min:0'],
            'amount_tendered' => ['nullable', 'numeric', 'min:0'],
            'payment_method'  => ['nullable', 'string', 'max:50'],
            'notes'           => ['nullable', 'string'],
        ];
    }
}