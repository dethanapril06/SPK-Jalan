<?php

namespace App\Models;

use App\Models\AssessmentPeriod;
use App\Models\MfepResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MfepCalculation extends Model
{
    protected $table = 'mfep_calculations';

    protected $fillable = [
        'period_id',
        'code',
        'name',
        'description',
        'calculation_date',
        'status',
        'calculated_by_user_id',
        'started_at',
        'finalized_at',
    ];

    protected $casts = [
        'calculation_date' => 'date',
        'started_at' => 'datetime',
        'finalized_at' => 'datetime',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(AssessmentPeriod::class, 'period_id');
    }

    public function calculatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calculated_by_user_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(MfepResult::class);
    }
}
