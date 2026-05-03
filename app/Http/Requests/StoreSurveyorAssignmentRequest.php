<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSurveyorAssignmentRequest extends FormRequest
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
            'surveyor_id' => ['required', 'integer', 'exists:surveyors,id'],
            'alternative_id' => [
                'required',
                'integer',
                'exists:alternatives,id',
                Rule::unique('surveyor_assignments')->where(function ($query) {
                    return $query
                        ->where('surveyor_id', $this->input('surveyor_id'))
                        ->where('alternative_id', $this->input('alternative_id'));
                }),
            ],
            'status' => ['required', Rule::in(['assigned', 'in_progress', 'submitted', 'reviewed'])],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
