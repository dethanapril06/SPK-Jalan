# Penjelasan Perhitungan Metode MFEP (Multi-Factor Evaluation Process)

Dokumen ini menjelaskan alur dan implementasi perhitungan metode **MFEP (Multi-Factor Evaluation Process)** pada file [`app/Services/MfepCalculationService.php`](file:///c:/laragon/www/SPK-Jalan/app/Services/MfepCalculationService.php) lengkap dengan formula matematika dan potongan kode PHP untuk setiap langkahnya.

---

## Gambaran Umum

Metode **MFEP (Multi-Factor Evaluation Process)** adalah metode pengambilan keputusan multi-kriteria yang memberikan pembobotan pada setiap faktor/kriteria. Nilai total akhir dari suatu alternatif diperoleh dengan mengalikan Nilai Evaluasi ($E$) setiap kriteria dengan Bobot Evaluasi ($W$), kemudian menjumlahkan seluruh nilai terbobot tersebut.

---

## Diagram Alur Perhitungan

Proses kalkulasi utama dijalankan melalui metode [`calculate`](file:///c:/laragon/www/SPK-Jalan/app/Services/MfepCalculationService.php#L16-L42) di dalam transaksi database (`DB::transaction`) untuk menjaga konsistensi data.

```
┌─────────────────────────────────────────────────────────┐
│ 1. Inisialisasi Perhitungan (Status: draft)             │
└───────────────────────────┬─────────────────────────────┘
                            ▼
┌─────────────────────────────────────────────────────────┐
│ 2. Iterasi Setiap Alternatif                            │
│   ├── a. Hitung Skor Sub-Kriteria (Rata-rata Aspek)     │
│   ├── b. Hitung Nilai Evaluasi (E) Kriteria             │
│   ├── c. Hitung Nilai Terbobot (E × W)                  │
│   └── d. Akumulasi Total Skor Akhir (Σ(E × W))          │
└───────────────────────────┬─────────────────────────────┘
                            ▼
┌─────────────────────────────────────────────────────────┐
│ 3. Pemeringkatan & Rekomendasi (Ranking)                │
└───────────────────────────┬─────────────────────────────┘
                            ▼
┌─────────────────────────────────────────────────────────┐
│ 4. Finalisasi Perhitungan (Status: finalized)           │
└─────────────────────────────────────────────────────────┘
```

---

## Detail Langkah demi Langkah & Potongan Kode

### Langkah 1: Inisialisasi & Pengambilan Data
📌 **Fungsi:** [`calculate()`](file:///c:/laragon/www/SPK-Jalan/app/Services/MfepCalculationService.php#L16-L42)

Mengambil record kalkulasi, mengubah status menjadi `draft`, lalu mengambil data seluruh Alternatif dan Kriteria.

#### Potongan Kode PHP:
```php
public function calculate(int $mfepCalculationId): MfepCalculation
{
    $mfepCalculation = MfepCalculation::findOrFail($mfepCalculationId);
    
    DB::transaction(function () use ($mfepCalculation) {
        $mfepCalculation->update([
            'started_at' => now(),
            'status' => 'draft',
        ]);

        $alternatives = Alternative::orderBy('order')->get();
        $criteria = Criteria::orderBy('code')->get();

        foreach ($alternatives as $alternative) {
            $this->calculateForAlternative($mfepCalculation, $alternative, $criteria);
        }

        $this->assignRankings($mfepCalculation);

        $mfepCalculation->update([
            'status' => 'finalized',
            'finalized_at' => now(),
        ]);
    });

    return $mfepCalculation->refresh();
}
```

---

### Langkah 2: Perhitungan Skor Sub-Kriteria
📌 **Fungsi:** [`calculateSubCriteriaScore()`](file:///c:/laragon/www/SPK-Jalan/app/Services/MfepCalculationService.php#L106-L122)

Sebelum mendapatkan Nilai Evaluasi Kriteria, sistem menghitung rata-rata nilai penilaian aspek pada tingkat Sub-Kriteria untuk alternatif tertentu.

#### Formula Matematika:
```
SubCriteriaScore = Total Nilai Aspek / Jumlah Aspek
```
$$\text{SubCriteriaScore} = \frac{\sum \text{Nilai Aspek Asesmen}}{\text{Jumlah Aspek Penilaian}}$$

#### Potongan Kode PHP:
```php
private function calculateSubCriteriaScore(int $alternativeId, int $subCriteriaId): ?float
{
    $assessments = Assessment::where('alternative_id', $alternativeId)
        ->where('sub_criteria_id', $subCriteriaId)
        ->with('assessmentAspect')
        ->get();

    if ($assessments->isEmpty()) {
        return null;
    }

    $values = $assessments->map(function ($assessment) {
        return $assessment->assessmentAspect?->value ?? 0;
    })->toArray();

    return array_sum($values) / count($values);
}
```

---

### Langkah 3: Perhitungan Nilai Evaluasi ($E$) per Kriteria
📌 **Fungsi:** [`calculateForAlternative()`](file:///c:/laragon/www/SPK-Jalan/app/Services/MfepCalculationService.php#L50-L73)

Untuk setiap kriteria, sistem mengumpulkan skor sub-kriteria di bawahnya dan menghitung rata-ratanya sebagai **Nilai Evaluasi ($E$)**.

#### Formula Matematika:
```
E = Total Skor Sub-Kriteria / Jumlah Sub-Kriteria
```
$$E = \frac{\sum \text{SubCriteriaScore}}{\text{Jumlah SubCriteria}}$$

#### Potongan Kode PHP:
```php
// Loop setiap Kriteria
foreach ($criteria as $criterion) {
    $subCriteriaList = SubCriteria::where('criteria_id', $criterion->id)
        ->orderBy('code')
        ->get();

    $subCriteriaScores = [];

    // Kumpulkan skor semua sub-kriteria dalam kriteria ini
    foreach ($subCriteriaList as $subCriteria) {
        $subCriteriaScore = $this->calculateSubCriteriaScore(
            $alternative->id,
            $subCriteria->id
        );

        if ($subCriteriaScore !== null) {
            $subCriteriaScores[] = $subCriteriaScore;
        }
    }

    // Hitung Nilai Evaluasi (E) = rata-rata sub-kriteria
    $evaluationValue = count($subCriteriaScores) > 0 
        ? array_sum($subCriteriaScores) / count($subCriteriaScores)
        : 0;
```

---

### Langkah 4: Perhitungan Nilai Terbobot ($E \times W$) per Kriteria
📌 **Fungsi:** [`calculateForAlternative()`](file:///c:/laragon/www/SPK-Jalan/app/Services/MfepCalculationService.php#L74-L88)

Nilai Evaluasi ($E$) yang didapat kemudian dikalikan dengan bobot kriteria ($W$).

#### Formula Matematika:
```
Weighted Value = E × W
```
$$\text{Weighted Value} = E \times W$$

#### Potongan Kode PHP:
```php
    // Hitung E × W (Weighted Value)
    $weight = (float) $criterion->weight;
    $weightedValue = $evaluationValue * $weight;

    // Akumulasi ke total skor
    $totalWeightedScore += $weightedValue;

    // Simpan detail per kriteria (bukan per sub-kriteria)
    $resultsToSave[] = [
        'criteria_id' => $criterion->id,
        'evaluation_value' => $evaluationValue,
        'weight' => $weight,
        'weighted_value' => $weightedValue,
    ];
}
```

---

### Langkah 5: Penentuan Total Skor Akhir Alternatif & Penyimpanan
📌 **Fungsi:** [`calculateForAlternative()`](file:///c:/laragon/www/SPK-Jalan/app/Services/MfepCalculationService.php#L90-L104)

Total skor akhir suatu alternatif diperoleh dari penjumlahan seluruh Nilai Terbobot dari semua kriteria:

#### Formula Matematika:
```
Total Skor Akhir = Σ(E × W)
```
$$\text{Total Weighted Score} = \sum_{i=1}^{n} (E_i \times W_i)$$

#### Potongan Kode PHP:
```php
// Skor Akhir = Σ(E × W) - TIDAK dinormalisasi
$finalScore = $totalWeightedScore;

$mfepResult = MfepResult::create([
    'mfep_calculation_id' => $mfepCalculation->id,
    'alternative_id' => $alternative->id,
    'raw_score' => $finalScore,
    'weighted_score' => $finalScore,
]);

foreach ($resultsToSave as $detailData) {
    $detailData['mfep_result_id'] = $mfepResult->id;
    MfepResultDetail::create($detailData);
}
```

---

### Langkah 6: Pemeringkatan (Ranking) & Rekomendasi
📌 **Fungsi:** [`assignRankings()`](file:///c:/laragon/www/SPK-Jalan/app/Services/MfepCalculationService.php#L124-L138)

Mengurutkan seluruh alternatif berdasarkan `weighted_score` secara menurun (descending), memberikan peringkat 1..N, serta menandai peringkat 1 sebagai rekomendasi (`is_recommended = true`).

#### Potongan Kode PHP:
```php
private function assignRankings(MfepCalculation $mfepCalculation): void
{
    $results = MfepResult::where('mfep_calculation_id', $mfepCalculation->id)
        ->orderByDesc('weighted_score')
        ->get();

    foreach ($results->values() as $index => $result) {
        $result->update(['rank' => $index + 1]);
    }

    $topResult = $results->first();
    if ($topResult) {
        $topResult->update(['is_recommended' => true]);
    }
}
```
