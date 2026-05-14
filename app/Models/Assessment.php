<?php

namespace App\Models;

use App\Models\Surveyor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Assessment extends Model
{

    protected $table = "assessments";
    protected $fillable = [
        'period_id',
        'surveyor_id',
        'alternative_id',
        'sub_criteria_id',
        'assessment_aspect_id',
        'notes',
        'photo_path',
        'photo_uploaded_at',
        'assessed_at',
    ];

    protected $casts = [
        'assessed_at' => 'datetime',
        'photo_path' => 'array',
        'photo_uploaded_at' => 'datetime',
    ];

    public function surveyor(): BelongsTo
    {
        return $this->belongsTo(Surveyor::class);
    }

    public function alternative(): BelongsTo
    {
        return $this->belongsTo(Alternative::class);
    }

    public function subCriteria(): BelongsTo
    {
        return $this->belongsTo(SubCriteria::class);
    }

    public function assessmentAspect(): BelongsTo
    {
        return $this->belongsTo(AssessmentAspect::class);
    }

    /**
     * Get photo URL for display
     */
    public function getPhotoUrl(): ?string
    {
        return $this->photo_path ? asset('storage/' . $this->photo_path) : null;
    }

    /**
     * Delete photo file from storage
     */
    public function deletePhotoFile(): bool
    {
        if ($this->photo_path && Storage::disk('public')->exists($this->photo_path)) {
            return Storage::disk('public')->delete($this->photo_path);
        }
        return false;
    }
}
