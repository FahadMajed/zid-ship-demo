<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class ShipmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'retailer_name' => 'required|string|max:255',
            'shipments' => 'required|array|min:1',
            'shipments.*.customer.name' => 'required|string|max:255',
            'shipments.*.customer.phone' => 'required|string|max:20',
            'shipments.*.customer.email' => 'required|email|max:255',
            'shipments.*.customer.city' => 'required|string|max:500',
            'shipments.*.package.height' => 'required|numeric',
            'shipments.*.package.width' => 'required|numeric',
            'shipments.*.package.length' => 'required|numeric',
            'shipments.*.package.weight' => 'required|numeric',
            'shipments.*.package.description' => 'nullable|string|max:1000',
            'shipments.*.delivery_type' => 'required|string|max:30',
        ];
    }
}
