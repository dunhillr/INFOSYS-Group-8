<?php

namespace App\Http\Requests;

use App\Models\Product;
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
            'production_date'      => ['required', 'date'],
            'product_id'           => ['required', 'exists:products,id'],
            'quantity_produced'    => ['required', 'numeric', 'min:0.01'],
            // Only required when the selected product has a parent
            'parent_product_id'    => ['nullable', 'exists:products,id'],
            'parent_quantity_used' => ['nullable', 'numeric', 'min:0.01'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $productId = $this->input('product_id');
            if (!$productId) return;

            $product = Product::find($productId);
            if (!$product) return;

            if ($product->parent_product_id) {
                // Product has a parent — both parent fields are required
                if (!$this->filled('parent_product_id')) {
                    $validator->errors()->add('parent_product_id', 'Please specify which raw material was used.');
                }
                if (!$this->filled('parent_quantity_used') || (float) $this->input('parent_quantity_used') <= 0) {
                    $validator->errors()->add('parent_quantity_used', 'Please specify how many units of the raw material were used.');
                }
            }
        });
    }
}