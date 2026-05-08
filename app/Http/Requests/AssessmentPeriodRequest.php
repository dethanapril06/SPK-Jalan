<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssessmentPeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $periodId = $this->route('assessment_period')?->id;

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('assessment_periods', 'code')->ignore($periodId),
            ],
            'name'        => ['required', 'string', 'max:255'],
            'year'        => ['required', 'integer', 'min:2000', 'max:2100'],
            'description' => ['nullable', 'string'],
            'start_date'  => ['nullable', 'date'],
            'end_date'    => ['nullable', 'date', 'after_or_equal:start_date'],
            'status'      => ['required', Rule::in(['draft', 'active', 'closed'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'code'        => 'Kode Periode',
            'name'        => 'Nama Periode',
            'year'        => 'Tahun',
            'description' => 'Deskripsi',
            'start_date'  => 'Tanggal Mulai',
            'end_date'    => 'Tanggal Selesai',
            'status'      => 'Status',
        ];
    }
}