<?php

namespace App\Http\Requests;

use App\Models\SurveyorAssignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSurveyorAssignmentRequest extends FormRequest
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
        /** @var SurveyorAssignment|null $assignment */
        $assignment = $this->route('assignment');

        return [
            'surveyor_id' => ['required', 'integer', 'exists:surveyors,id'],
            'alternative_id' => [
                'required',
                'integer',
                'exists:alternatives,id',
                Rule::unique('surveyor_assignments')
                    ->ignore($assignment?->id)
                    ->where(function ($query) use ($assignment) {
                        return $query
                            ->where('period_id', $assignment?->period_id)
                            ->where('alternative_id', $this->input('alternative_id'));
                    }),
            ],
            'status' => ['required', Rule::in(['assigned', 'in_progress', 'submitted', 'reviewed'])],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'alternative_id.unique' => 'Alternatif ini sudah ditugaskan ke surveyor lain pada periode yang sama.',
        ];
    }
}
