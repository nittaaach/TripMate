<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVacationPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destination_id' => ['required', 'exists:destinations,id'],
            'origin_address' => ['required', 'string', 'max:500'],
            'origin_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'origin_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'passenger_count' => ['required', 'integer', 'min:1', 'max:30'],
            'luggage_level' => ['required', 'in:light,medium,heavy'],
            'trip_type' => ['required', 'in:one_way,round_trip,multi_stop'],
            'budget_per_day' => ['nullable', 'numeric', 'min:0'],
            'needs_driver' => ['nullable', 'boolean'],
            'special_requirements' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'destination_id.required' => 'Destinasi wisata wajib dipilih.',
            'destination_id.exists' => 'Destinasi yang dipilih tidak ditemukan.',
            'origin_address.required' => 'Titik awal / alamat penjemputan wajib diisi.',
            'start_date.required' => 'Tanggal mulai sewa wajib diisi.',
            'start_date.after_or_equal' => 'Tanggal mulai sewa tidak boleh di masa lampau.',
            'end_date.required' => 'Tanggal selesai sewa wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai sewa harus sama atau setelah tanggal mulai.',
            'passenger_count.required' => 'Jumlah penumpang wajib diisi.',
            'passenger_count.min' => 'Jumlah penumpang minimal 1 orang.',
            'luggage_level.required' => 'Tingkat bagasi wajib dipilih.',
        ];
    }
}
