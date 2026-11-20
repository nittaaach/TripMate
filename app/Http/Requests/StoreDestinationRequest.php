<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDestinationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $destId = $this->route('destination')?->id ?? $this->route('destination');

        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:180', Rule::unique('destinations', 'slug')->ignore($destId)],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'terrain_type' => ['required', 'in:city,highway,mountain,beach,offroad,mixed'],
            'road_condition' => ['required', 'in:good,moderate,rough,offroad'],
            'accessibility_notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
