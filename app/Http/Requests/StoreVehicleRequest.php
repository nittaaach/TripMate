<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vehicleId = $this->route('vehicle')?->id ?? $this->route('vehicle');

        return [
            'vehicle_code' => ['required', 'string', 'max:30', Rule::unique('vehicles', 'vehicle_code')->ignore($vehicleId)],
            'brand' => ['required', 'string', 'max:80'],
            'model' => ['required', 'string', 'max:100'],
            'year' => ['nullable', 'integer', 'between:1990,2035'],
            'category' => ['required', 'in:city_car,sedan,mpv,suv,jeep,van,pickup'],
            'transmission' => ['required', 'in:automatic,manual'],
            'fuel_type' => ['required', 'in:petrol,diesel,hybrid,electric'],
            'fuel_efficiency_km_per_l' => ['nullable', 'numeric', 'min:0'],
            'battery_range_km' => ['nullable', 'numeric', 'min:0'],
            'seats' => ['required', 'integer', 'min:1', 'max:30'],
            'luggage_capacity_l' => ['nullable', 'integer', 'min:0'],
            'ground_clearance_mm' => ['nullable', 'integer', 'min:0'],
            'suitable_terrain' => ['required', 'array'],
            'suitable_terrain.*' => ['in:city,highway,mountain,beach,offroad,mixed'],
            'suitable_road_conditions' => ['required', 'array'],
            'suitable_road_conditions.*' => ['in:good,moderate,rough,offroad'],
            'daily_rate' => ['required', 'numeric', 'min:0'],
            'deposit_amount' => ['required', 'numeric', 'min:0'],
            'requires_driver' => ['nullable', 'boolean'],
            'has_ac' => ['nullable', 'boolean'],
            'is_available' => ['nullable', 'boolean'],
            'status' => ['required', 'in:active,maintenance,inactive'],
            'description' => ['nullable', 'string'],
        ];
    }
}
