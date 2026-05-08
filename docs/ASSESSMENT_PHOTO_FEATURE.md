# 📸 Assessment Photo Upload Feature

## Overview

Fitur untuk upload foto penilaian surveyor. Surveyor bisa capture langsung dari kamera atau upload file dari device.

## Database Schema

```
assessments table:
- photo_path (string, nullable) — Path relatif file foto
- photo_uploaded_at (timestamp, nullable) — Waktu upload
```

## File Storage Structure

```
storage/app/public/assessments/
├── {year}/
│   ├── {period_id}/
│   │   ├── assessment_1_1715089200.jpg
│   │   ├── assessment_1_1715089300.jpg
│   │   └── ...
```

## Components & Classes

### 1. Migration

**File:** `database/migrations/0001_01_01_000009_create_assessments_table.php`

- Tambah kolom `photo_path` dan `photo_uploaded_at`

### 2. Model

**File:** `app/Models/Assessment.php`

- Method `getPhotoUrl()` — Get public URL foto
- Method `deletePhotoFile()` — Hapus file dari storage

### 3. Service

**File:** `app/Services/AssessmentPhotoService.php`

- Validasi file (size, format, MIME type)
- Upload ke storage dengan path yang konsisten
- Delete file lama saat re-upload

### 4. Request Validation

**File:** `app/Http/Requests/AssessmentPhotoRequest.php`

- Validasi dimensi gambar (100x100 hingga 4000x4000)
- Validasi ukuran (max 5 MB)
- Validasi format (jpg, png, gif, webp)

### 5. Blade Component

**File:** `resources/views/components/assessment-photo-upload.blade.php`

- Form input file dengan capture camera
- Preview foto sebelum/sesudah upload
- Info ukuran file dan constraint

## Usage in Controller

```php
<?php
namespace App\Http\Controllers;

use App\Services\AssessmentPhotoService;
use App\Http\Requests\AssessmentPhotoRequest;

class AssessmentController extends Controller
{
    private AssessmentPhotoService $photoService;

    public function __construct(AssessmentPhotoService $photoService)
    {
        $this->photoService = $photoService;
    }

    public function store(AssessmentPhotoRequest $request): RedirectResponse
    {
        $assessment = Assessment::create($request->validated());

        // Upload foto jika ada
        if ($request->hasFile('photo')) {
            $photoPath = $this->photoService->upload(
                $assessment,
                $request->file('photo')
            );
            $assessment->update([
                'photo_path' => $photoPath,
                'photo_uploaded_at' => now(),
            ]);
        }

        return redirect()->route('assessments.index');
    }

    public function update(AssessmentPhotoRequest $request, Assessment $assessment): RedirectResponse
    {
        $assessment->update($request->validated());

        // Handle foto baru
        if ($request->hasFile('photo')) {
            $photoPath = $this->photoService->upload($assessment, $request->file('photo'));
            $assessment->update([
                'photo_path' => $photoPath,
                'photo_uploaded_at' => now(),
            ]);
        }

        return redirect()->route('assessments.show', $assessment);
    }
}
```

## Usage in Blade View

```blade
<!-- Form input -->
@include('components.assessment-photo-upload', ['fieldId' => 'assessment_photo', 'assessment' => $assessment ?? null])

<!-- Display foto -->
@if($assessment->photo_path)
    <img src="{{ $assessment->getPhotoUrl() }}" alt="Foto penilaian" class="img-fluid">
@endif
```

## Constraints

| Attribute         | Value                       |
| ----------------- | --------------------------- |
| **Max Size**      | 5 MB                        |
| **Formats**       | jpg, jpeg, png, gif, webp   |
| **Min Dimension** | 100x100px                   |
| **Max Dimension** | 4000x4000px                 |
| **Storage**       | public (accessible via URL) |

## Notes

- Foto lama otomatis dihapus saat upload ulang
- Path disimpan relatif, bisa di-generate URL dengan `asset('storage/' . $photo_path)`
- File disimpan dengan struktur folder per tahun & periode untuk organizing
- Gunakan `hasFile('photo')` di request untuk check ada foto atau tidak
