<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyorAssignment extends Model
{
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_REVIEWED = 'reviewed';

    protected $table = 'surveyor_assignments';

    protected $fillable = [
        'period_id',
        'surveyor_id',
        'alternative_id',
        'assigned_by_user_id',
        'status',
        'due_date',
        'assigned_at',
        'started_at',
        'submitted_at',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function surveyor(): BelongsTo
    {
        return $this->belongsTo(Surveyor::class);
    }

    public function alternative(): BelongsTo
    {
        return $this->belongsTo(Alternative::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(AssessmentPeriod::class, 'period_id');
    }

    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'surveyor_id', 'surveyor_id');
    }
}
