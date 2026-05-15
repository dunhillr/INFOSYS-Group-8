<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sale_id'       => ['required', 'exists:sales,id'],
            'customer_id'   => ['required', 'exists:customers,id'],
            'vehicle_id'    => ['nullable', 'exists:vehicles,id'],
            'driver_id'     => ['nullable', 'exists:users,id'],
            'destination'   => ['required', 'string', 'max:255'],
            'delivery_date' => ['required', 'date'],
            'delivery_time' => ['required'],
            'status'        => ['required', 'in:pending,out_for_delivery,delivered,cancelled'],
            'notes'         => ['nullable', 'string'],
        ];
    }
}