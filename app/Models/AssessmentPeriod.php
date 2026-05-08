<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentPeriod extends Model
{
    protected $fillable = [
        'code',
        'name',
        'year',
        'description',
        'start_date',
        'end_date',
        'status',
        'created_by_user_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'year'       => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function surveyorAssignments(): HasMany
    {
        return $this->hasMany(SurveyorAssignment::class, 'period_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'period_id');
    }

    public function mfepCalculations(): HasMany
    {
        return $this->hasMany(MfepCalculation::class, 'period_id');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }
}