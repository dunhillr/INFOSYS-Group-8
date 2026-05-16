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
            'destination'   => ['required', 'string', 'max:255'],
            'delivery_date' => ['required', 'date'],
            'delivery_time' => ['required'],
            'status'        => ['required', 'in:pending,out_for_delivery,delivered,cancelled'],
            'notes'         => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->vehicle_id) {
                $vehicle = \App\Models\Vehicle::find($this->vehicle_id);
                if ($vehicle && !$vehicle->assigned_driver_id) {
                    $validator->errors()->add(
                        'vehicle_id', 
                        '⚠️ Ang sasakyang ito ay walang nakatalagang driver ngayon. Mangyaring mag-assign muna sa Vehicles Page.'
                    );
                }
            }
        });
    }
}