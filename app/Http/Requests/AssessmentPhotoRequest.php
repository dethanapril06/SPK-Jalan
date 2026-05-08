<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssessmentPhotoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'photo' => [
                'required',
                'file',
                'image',
                'max:5120', // 5 MB
                'mimes:jpg,jpeg,png,gif,webp',
                'dimensions:min_width=100,min_height=100,max_width=4000,max_height=4000',
            ],
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'photo.required' => 'Foto wajib diunggah.',
            'photo.file' => 'File harus berupa file yang valid.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.max' => 'Ukuran file maksimal 5 MB.',
            'photo.mimes' => 'Format file harus jpg, jpeg, png, gif, atau webp.',
            'photo.dimensions' => 'Dimensi gambar minimal 100x100px dan maksimal 4000x4000px.',
        ];
    }
}
