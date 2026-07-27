<?php

namespace App\Models;

use App\Models\SubCriteria;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Criteria extends Model
{
    protected $table = "criteria";
    protected $fillable = [
        'code',
        'name',
        'description',
        'weight',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
    ];

    /**
     * Generate Kode Kriteria berikutnya secara otomatis (K1, K2, dst.)
     */
    public static function generateNextCode(): string
    {
        $maxNumber = self::all()->map(function ($c) {
            return (int) preg_replace('/[^0-9]/', '', $c->code);
        })->max();

        $nextNumber = ($maxNumber ?? 0) + 1;
        return 'K' . $nextNumber;
    }

    /**
     * Dapatkan sub-kriteria dari kriteria ini
     */
    public function subCriteria(): HasMany
    {
        return $this->hasMany(SubCriteria::class);
    }
}
