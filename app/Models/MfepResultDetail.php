<?php

namespace App\Models;

use App\Models\MfepResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MfepResultDetail extends Model
{
    protected $table = 'mfep_result_details';

    protected $fillable = [
        'mfep_result_id',
        'criteria_id',
        'sub_criteria_id',
        'assessment_aspect_id',
        'assessment_id',
        'evaluation_value',
        'weight',
        'weighted_value',
    ];

    protected $casts = [
        'evaluation_value' => 'decimal:4',
        'weight' => 'decimal:4',
        'weighted_value' => 'decimal:4',
    ];

    public function mfepResult(): BelongsTo
    {
        return $this->belongsTo(MfepResult::class);
    }

    public function criteria(): BelongsTo
    {
        return $this->belongsTo(Criteria::class);
    }

    public function subCriteria(): BelongsTo
    {
        return $this->belongsTo(SubCriteria::class);
    }

    public function assessmentAspect(): BelongsTo
    {
        return $this->belongsTo(AssessmentAspect::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }
}
