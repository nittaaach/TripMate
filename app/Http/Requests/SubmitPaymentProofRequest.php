<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitPaymentProofRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proof_file' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'proof_file.required' => 'File bukti transfer wajib diunggah.',
            'proof_file.image' => 'File bukti harus berupa gambar (JPEG, PNG, WEBP).',
            'proof_file.max' => 'Ukuran file maksimal 3 MB.',
        ];
    }
}
