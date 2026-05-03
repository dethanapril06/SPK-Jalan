<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubCriteria extends Model
{
    protected $table = "sub_criteria";
    protected $fillable = [
        "criteria_id",
        "code",
        "name",
        "description",
        "order",
    ];

    /**
     * Dapatkan kriteria parent dari sub-kriteria ini
     */
    public function criteria(): BelongsTo
    {
        return $this->belongsTo(Criteria::class);
    }

    /**
     * Dapatkan aspek penilaian dari sub-kriteria ini
     */
    public function assessmentAspects(): HasMany
    {
        return $this->hasMany(AssessmentAspect::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }
}
