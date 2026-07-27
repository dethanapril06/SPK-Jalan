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
    ];

    /**
     * Generate Kode Sub Kriteria berikutnya secara otomatis berdasarkan kriteria parent (e.g. K1.1, K1.2)
     */
    public static function generateNextCode(int $criteriaId): string
    {
        $criteria = Criteria::find($criteriaId);
        if (! $criteria) {
            return '';
        }

        $prefix = $criteria->code . '.';

        $maxNumber = self::where('criteria_id', $criteriaId)
            ->get()
            ->map(function ($sc) use ($prefix) {
                if (str_starts_with($sc->code, $prefix)) {
                    $num = substr($sc->code, strlen($prefix));
                    return (int) filter_var($num, FILTER_SANITIZE_NUMBER_INT);
                }
                return 0;
            })
            ->max();

        $nextNumber = ($maxNumber ?? 0) + 1;
        return $prefix . $nextNumber;
    }

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
