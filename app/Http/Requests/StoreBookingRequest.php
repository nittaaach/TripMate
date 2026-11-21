<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'vacation_plan_id' => ['nullable', 'exists:vacation_plans,id'],
            'pickup_datetime' => ['required', 'date', 'after_or_equal:now'],
            'return_datetime' => ['required', 'date', 'after:pickup_datetime'],

            // Pickup address & regional hierarchy
            'pickup_address' => ['required', 'string', 'max:500'],
            'pickup_province_id' => ['nullable', 'exists:provinces,id'],
            'pickup_regency_id' => ['nullable', 'exists:regencies,id'],
            'pickup_district_id' => ['nullable', 'exists:districts,id'],
            'pickup_village_id' => ['nullable', 'exists:villages,id'],
            'pickup_rt_rw' => ['nullable', 'string', 'max:30'],
            'pickup_postal_code' => ['nullable', 'string', 'max:10'],
            'pickup_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'pickup_longitude' => ['nullable', 'numeric', 'between:-180,180'],

            // Return address & regional hierarchy
            'return_address' => ['required', 'string', 'max:500'],
            'return_province_id' => ['nullable', 'exists:provinces,id'],
            'return_regency_id' => ['nullable', 'exists:regencies,id'],
            'return_district_id' => ['nullable', 'exists:districts,id'],
            'return_village_id' => ['nullable', 'exists:villages,id'],
            'return_rt_rw' => ['nullable', 'string', 'max:30'],
            'return_postal_code' => ['nullable', 'string', 'max:10'],
            'return_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'return_longitude' => ['nullable', 'numeric', 'between:-180,180'],

            'needs_driver' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_id.required' => 'Kendaraan wajib dipilih.',
            'pickup_datetime.required' => 'Waktu penjemputan wajib diisi.',
            'pickup_datetime.after_or_equal' => 'Waktu penjemputan tidak boleh di masa lampau.',
            'return_datetime.required' => 'Waktu pengembalian wajib diisi.',
            'return_datetime.after' => 'Waktu pengembalian harus lebih lambat daripada waktu penjemputan.',
            'pickup_address.required' => 'Alamat penjemputan wajib diisi.',
            'return_address.required' => 'Alamat pengembalian wajib diisi.',
        ];
    }
}
