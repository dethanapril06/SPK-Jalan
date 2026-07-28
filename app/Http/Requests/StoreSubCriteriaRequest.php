<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubCriteriaRequest extends FormRequest
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
            'criteria_id' => ['required', 'integer', Rule::exists('criteria', 'id')],
            'code' => ['nullable', 'string', 'max:50'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sub_criteria', 'name')->where('criteria_id', $this->criteria_id),
            ],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Custom validation messages in Indonesian.
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'Nama sub kriteria tersebut sudah ada pada kriteria induk yang dipilih.',
        ];
    }
}
