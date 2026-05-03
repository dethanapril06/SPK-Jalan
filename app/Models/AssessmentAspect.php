<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentAspect extends Model
{
    protected $table = "assessment_aspects";
    protected $fillable = [
        'sub_criteria_id',
        'name',
        'value',
        'description',
        'order',
    ];

    protected $casts = [
        'sub_criteria_id' => 'integer',
        'value' => 'integer',
    ];

    /**
     * Dapatkan sub-kriteria dari aspek penilaian ini
     */
    public function subCriteria(): BelongsTo
    {
        return $this->belongsTo(SubCriteria::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }
}
