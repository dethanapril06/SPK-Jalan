<?php

namespace App\Http\Requests;

use App\Models\AssessmentPeriod;
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
        $activePeriod = AssessmentPeriod::where('status', 'active')->first();

        return [
            'surveyor_id' => ['required', 'integer', 'exists:surveyors,id'],
            'alternative_ids' => ['required', 'array', 'min:1'],
            'alternative_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:alternatives,id',
                Rule::unique('surveyor_assignments', 'alternative_id')->where(function ($query) use ($activePeriod) {
                    return $query
                        ->where('period_id', $activePeriod?->id);
                }),
            ],
            'status' => ['required', Rule::in(['assigned', 'in_progress', 'submitted', 'reviewed'])],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'alternative_ids' => 'alternatif',
            'alternative_ids.*' => 'alternatif',
        ];
    }

    public function messages(): array
    {
        return [
            'alternative_ids.*.unique' => 'Alternatif ini sudah ditugaskan ke surveyor lain pada periode aktif.',
        ];
    }
}
