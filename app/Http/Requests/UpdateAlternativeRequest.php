<?php

namespace App\Http\Requests;

use App\Models\Alternative;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAlternativeRequest extends FormRequest
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
        /** @var Alternative|null $alternative */
        $alternative = $this->route('alternative');

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('alternatives', 'code')->ignore($alternative?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'order' => ['required', 'integer', 'min:1'],
        ];
    }
}
