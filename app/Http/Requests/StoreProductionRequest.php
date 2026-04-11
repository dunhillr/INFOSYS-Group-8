<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'production_date' => ['required', 'date'],
            'batch_reference' => ['nullable', 'string', 'max:255'],
            'quantity_produced' => ['required', 'numeric', 'min:0.01'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}