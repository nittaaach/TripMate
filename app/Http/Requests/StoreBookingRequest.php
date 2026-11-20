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
            'pickup_address' => ['required', 'string', 'max:500'],
            'return_address' => ['required', 'string', 'max:500'],
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
