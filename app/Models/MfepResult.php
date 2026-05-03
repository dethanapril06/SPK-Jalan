<?php

namespace App\Models;

use App\Models\MfepCalculation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MfepResult extends Model
{
    protected $table = 'mfep_results';

    protected $fillable = [
        'mfep_calculation_id',
        'alternative_id',
        'raw_score',
        'weighted_score',
        'rank',
        'is_recommended',
        'notes',
    ];

    protected $casts = [
        'raw_score' => 'decimal:4',
        'weighted_score' => 'decimal:4',
        'is_recommended' => 'boolean',
    ];

    public function mfepCalculation(): BelongsTo
    {
        return $this->belongsTo(MfepCalculation::class);
    }

    public function alternative(): BelongsTo
    {
        return $this->belongsTo(Alternative::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(MfepResultDetail::class);
    }
}
