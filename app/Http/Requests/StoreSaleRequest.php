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
            'customer_id'     => ['nullable', 'exists:customers,id'],
            'vehicle_id'      => ['nullable', 'exists:vehicles,id'],
            'delivery_type'   => ['required', 'in:walk_in,delivery'],
            
            // Multiple items
            'items'                 => ['required', 'array', 'min:1'],
            'items.*.product_id'    => ['required', 'exists:products,id'],
            'items.*.quantity'      => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price'    => ['required', 'numeric', 'min:0'],
            
            'delivery_fee'    => ['nullable', 'numeric', 'min:0'],
            'discount_type'   => ['nullable', 'in:fixed,percent'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_status'  => ['required', 'in:paid,partial,unpaid'],
            'amount_paid'     => ['nullable', 'numeric', 'min:0'],
            'amount_tendered' => ['nullable', 'numeric', 'min:0'],
            'payment_method'  => ['nullable', 'string', 'max:50'],
            'notes'           => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->delivery_type === 'delivery' && $this->vehicle_id) {
                $vehicle = \App\Models\Vehicle::find($this->vehicle_id);
                if ($vehicle) {
                    if (!$vehicle->assigned_driver_id) {
                        $validator->errors()->add(
                            'vehicle_id', 
                            '⚠️ Ang sasakyang ito ay walang nakatalagang driver ngayon. Mangyaring mag-assign muna sa Vehicles Page.'
                        );
                    }

                    // Calculate total weight of new items being sold
                    $totalWeight = 0;
                    if (is_array($this->items)) {
                        foreach ($this->items as $item) {
                            if (isset($item['product_id']) && isset($item['quantity'])) {
                                $product = \App\Models\Product::find($item['product_id']);
                                if ($product) {
                                    $totalWeight += ((float)$item['quantity'] * (float)($product->weight_kg ?? 0));
                                }
                            }
                        }
                    }

                    // Calculate current load on the vehicle
                    $query = \App\Models\Delivery::where('vehicle_id', $vehicle->id)
                        ->whereIn('status', ['pending', 'out_for_delivery'])
                        ->with('sale.saleItems.product');
                        
                    // If updating an existing sale, exclude it from current load
                    if ($this->route('sale')) {
                        $query->where('sale_id', '!=', $this->route('sale')->id);
                    }

                    $currentLoad = $query->get()->sum(function ($delivery) {
                            return $delivery->sale->saleItems->sum(function ($item) {
                                return (float) $item->quantity * (float) ($item->product->weight_kg ?? 0);
                            });
                        });

                    if ((float)$vehicle->capacity < ($currentLoad + $totalWeight)) {
                        $validator->errors()->add(
                            'vehicle_id',
                            '⚠️ Overloaded! Lumagpas sa Max Capacity ng sasakyan ang bigat ng yelo. Bawasan ang order o pumili ng ibang sasakyan.'
                        );
                    }
                }
            }
        });
    }
}