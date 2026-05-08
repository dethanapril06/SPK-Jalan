<?php

namespace App\Services;

use App\Models\Assessment;
use Illuminate\Http\UploadedFile;

class AssessmentPhotoService
{
    /**
     * Maksimal ukuran file (10 MB)
     */
    private const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10 MB

    /**
     * Format file yang diizinkan
     */
    private const ALLOWED_FORMATS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    /**
     * Disk storage yang digunakan
     */
    private const DISK = 'public';

    /**
     * Upload foto penilaian
     *
     * @param Assessment $assessment
     * @param UploadedFile $file
     * @return string Path relatif ke file yang di-upload
     * @throws \Exception
     */
    public function upload(Assessment $assessment, UploadedFile $file): string
    {
        // 1. Validasi file
        $this->validateFile($file);

        // 2. Hapus foto lama jika ada
        if ($assessment->photo_path) {
            $assessment->deletePhotoFile();
        }

        // 3. Generate path folder
        $year = now()->year;
        $periodId = $assessment->surveyor_id; // atau dari relationship assessment->period_id
        $folderPath = "assessments/{$year}/{$periodId}";

        // 4. Generate nama file unik
        $extension = $file->getClientOriginalExtension();
        $filename = "assessment_{$assessment->id}_" . time() . ".{$extension}";

        // 5. Store file
        $storedPath = $file->storeAs($folderPath, $filename, self::DISK);

        return $storedPath;
    }

    /**
     * Validasi file upload
     *
     * @throws \Exception
     */
    private function validateFile(UploadedFile $file): void
    {
        // Cek ukuran file
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \Exception(
                'Ukuran file terlalu besar. Maksimal ' . $this->formatBytes(self::MAX_FILE_SIZE) . '.'
            );
        }

        // Cek format file
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_FORMATS)) {
            throw new \Exception(
                'Format file tidak didukung. Gunakan: ' . implode(', ', self::ALLOWED_FORMATS)
            );
        }

        // Validasi MIME type
        $mimeType = $file->getMimeType();
        $validMimes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ];
        if (!in_array($mimeType, $validMimes)) {
            throw new \Exception('File harus berupa gambar yang valid.');
        }
    }

    /**
     * Hapus foto
     */
    public function delete(Assessment $assessment): bool
    {
        return $assessment->deletePhotoFile();
    }

    /**
     * Format bytes ke format yang readable (KB, MB, GB)
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
