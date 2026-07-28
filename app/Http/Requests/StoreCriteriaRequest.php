<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCriteriaRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['nullable', 'string'],
            'name' => ['required', 'string', 'max:255', Rule::unique('criteria', 'name')],
            'description' => ['nullable', 'string'],
            'weight' => ['required', 'numeric', 'min:0', 'max:1'],
        ];
    }

    /**
     * Custom validation messages in Indonesian.
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'Nama kriteria sudah ada. Silakan gunakan nama kriteria yang lain.',
        ];
    }
}
