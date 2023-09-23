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
            'shipper.phone' => 'required|string|max:20',
            'shipper.email' => 'required|email|max:255',
            'shipper.city' => 'required|string|max:500',
            'recipient' => 'required|array|min:1',
            'recipients.*.name' => 'required|string|max:255',
            'recipients.*.phone' => 'required|string|max:20',
            'recipients.*.email' => 'required|email|max:255',
            'recipients.*.city' => 'required|string|max:500',
            'recipients.*.package.height' => 'required|numeric',
            'recipients.*.package.width' => 'required|numeric',
            'recipients.*.package.length' => 'required|numeric',
            'recipients.*.package.weight' => 'required|numeric',
            'recipients.*.package.description' => 'nullable|string|max:1000',
            'delivery_type' => 'required|string|max:30',
        ];
    }
}
