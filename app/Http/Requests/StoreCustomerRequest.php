<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_contact' => ['nullable', 'string', 'max:50'],
            'customer_address' => ['required', 'string'],
            'customer_type' => ['required', 'in:walk-in,wholesale'],
            'notes' => ['nullable', 'string'],
        ];
    }
}