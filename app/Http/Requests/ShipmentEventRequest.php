<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Config;

class ShipmentEventRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        $courierNames = array_keys(config('couriers'));

        return [
            'shipment_id' => 'required',
            'courier_name' => 'required|in:' . implode(',', $courierNames),
            'order_id' => 'required',
            'status' => ['required', function ($attribute, $value, $fail) {
                $courierName = $this->input('courier_name');
                $statusPath = config("couriers.$courierName.status_path");
                $courierStatus = data_get($this->all(), $statusPath);

                if (!$courierStatus) {
                    $fail("Unable to extract status for courier $courierName.");
                    return;
                }

                $unifiedStatus = Config::get("couriers.$courierName.status_mappings.$courierStatus");
                if (!$unifiedStatus) {
                    $fail("The provided status for courier $courierName is not valid.");
                } else {
                    $this->attributes->add(['unified_status' => $unifiedStatus]); // Store the unified status
                }
            }],
        ];
    }
}
