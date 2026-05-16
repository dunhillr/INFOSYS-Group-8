<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_name' => ['required', 'string', 'max:255'],
            'plate_number' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,reserved,in_transit,maintenance'],
            'assigned_driver_id' => ['nullable', 'exists:users,id'],
        ];
    }
}