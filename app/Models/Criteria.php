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
        'order',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
    ];

    /**
     * Dapatkan sub-kriteria dari kriteria ini
     */
    public function subCriteria(): HasMany
    {
        return $this->hasMany(SubCriteria::class);
    }
}
