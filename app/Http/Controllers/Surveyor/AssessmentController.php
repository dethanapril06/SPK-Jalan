<?php

namespace App\Http\Controllers\Surveyor;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAspect;
use App\Models\SubCriteria;
use App\Models\SurveyorAssignment;
use App\Services\AssessmentPhotoService;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    private AssessmentPhotoService $photoService;

    public function __construct(AssessmentPhotoService $photoService)
    {
        $this->photoService = $photoService;
    }

    /**
     * Show form untuk input penilaian per sub-kriteria
     */
    public function show(SurveyorAssignment $assignment)
    {
        $surveyor = auth()->user()->surveyor;

        if (! $surveyor) {
            return redirect()->route('login')->with('error', 'Data surveyor tidak ditemukan.');
        }

        if ((int) $assignment->surveyor_id !== (int) $surveyor->id) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        if (! $assignment->period || ! $assignment->period->isActive()) {
            return back()->with('error', 'Periode penilaian untuk tugas ini sudah tidak aktif.');
        }

        $assignment->load(['alternative']);

        if (! in_array($assignment->status, ['assigned', 'in_progress'])) {
            return back()->with('error', 'Penugasan sudah diselesaikan.');
        }

        return redirect()->route('surveyor.task.show', $assignment);
    }

    /**
     * Form edit untuk input penilaian per sub-kriteria
     */
    public function edit(SurveyorAssignment $assignment, Request $request)
    {
        $surveyor = auth()->user()->surveyor;

        if (! $surveyor) {
            return redirect()->route('login')->with('error', 'Data surveyor tidak ditemukan.');
        }

        if ((int) $assignment->surveyor_id !== (int) $surveyor->id) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        if (! $assignment->period || ! $assignment->period->isActive()) {
            return back()->with('error', 'Periode penilaian untuk tugas ini sudah tidak aktif.');
        }

        $assignment->load(['alternative', 'period']);

        $subCriteriaId = $request->query('sub_criteria_id');
        $subCriteria   = SubCriteria::findOrFail($subCriteriaId);
        $subCriteria->load(['criteria', 'assessmentAspects']);

        $existingAssessment = Assessment::where('surveyor_id', $surveyor->id)
            ->where('alternative_id', $assignment->alternative_id)
            ->where('sub_criteria_id', $subCriteriaId)
            ->where('period_id', $assignment->period_id)
            ->first();

        $aspects = $subCriteria->assessmentAspects()->orderBy('order')->get();

        return view('surveyor.assessment.edit', [
            'assignment'  => $assignment,
            'surveyor'    => $surveyor,
            'subCriteria' => $subCriteria,
            'assessment'  => $existingAssessment,
            'aspects'     => $aspects,
        ]);
    }

    /**
     * Simpan penilaian
     */
    public function update(SurveyorAssignment $assignment, Request $request)
    {
        $surveyor = auth()->user()->surveyor;

        if (! $surveyor) {
            return redirect()->route('login')->with('error', 'Data surveyor tidak ditemukan.');
        }

        if ((int) $assignment->surveyor_id !== (int) $surveyor->id) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        if (! $assignment->period || ! $assignment->period->isActive()) {
            return back()->with('error', 'Periode penilaian untuk tugas ini sudah tidak aktif.');
        }

        $existingAssessment = Assessment::where('surveyor_id', $surveyor->id)
            ->where('alternative_id', $assignment->alternative_id)
            ->where('sub_criteria_id', $request->sub_criteria_id)
            ->where('period_id', $assignment->period_id)
            ->first();

        // FIX #1: Cek jumlah foto yang sudah ada sebelum validasi
        $existingPhotos = [];
        if ($existingAssessment && $existingAssessment->photo_path) {
            $existingPhotos = $this->decodePhotos($existingAssessment->photo_path);
        }

        $existingPhotoCount = count($existingPhotos);
        $remainingSlots     = 5 - $existingPhotoCount;

        // Foto wajib hanya jika belum ada foto sama sekali
        $photoRule    = ($existingPhotoCount > 0) ? 'nullable' : 'required';
        $maxFileCount = max(1, $remainingSlots);

        $validated = $request->validate([
            'sub_criteria_id'      => ['required', 'integer', 'exists:sub_criteria,id'],
            'assessment_aspect_id' => ['required', 'integer', 'exists:assessment_aspects,id'],
            'notes'                => ['nullable', 'string'],
            'photos'               => [$photoRule, 'array', "max:{$maxFileCount}"],
            'photos.*'             => ['file', 'image', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp'],
        ]);

        $subCriteria = SubCriteria::findOrFail($validated['sub_criteria_id']);
        $aspect      = AssessmentAspect::findOrFail($validated['assessment_aspect_id']);

        if ((int) $aspect->sub_criteria_id !== (int) $subCriteria->id) {
            return back()->withErrors(['assessment_aspect_id' => 'Aspek tidak sesuai dengan sub-kriteria.']);
        }

        // Tentukan photo_path yang akan disimpan:
        // Upload foto DULU sebelum menyimpan record, agar:
        // (1) foto wajib benar-benar ditegakkan — record tidak tersimpan tanpa foto
        // (2) jika upload gagal, record lama tidak berubah
        $newPhotoPath = $existingPhotos; // default: pertahankan foto lama

        if ($request->hasFile('photos')) {
            if ($existingPhotoCount >= 5) {
                return back()->withErrors(['photos' => 'Maksimal 5 foto per penilaian. Hapus foto lama terlebih dahulu.'])
                    ->withInput();
            }

            try {
                // Buat objek assessment sementara untuk keperluan upload path
                // gunakan existing atau buat instance kosong (id belum ada jika baru)
                $tempAssessment = $existingAssessment ?? new Assessment([
                    'surveyor_id'    => $surveyor->id,
                    'alternative_id' => $assignment->alternative_id,
                    'sub_criteria_id'=> $validated['sub_criteria_id'],
                    'period_id'      => $assignment->period_id,
                ]);

                $uploadedPaths = $this->photoService->uploadMultiple($tempAssessment, $request->file('photos'));
                $newPhotoPath  = array_values(array_slice(
                    array_merge($existingPhotos, $uploadedPaths),
                    0, 5
                ));

            } catch (\Exception $e) {
                return back()->withErrors(['photos' => 'Gagal mengunggah foto: ' . $e->getMessage()])
                    ->withInput();
            }
        }

        // Setelah foto siap (atau foto lama dipertahankan), simpan record
        $assessment = Assessment::updateOrCreate(
            [
                'surveyor_id'     => $surveyor->id,
                'alternative_id'  => $assignment->alternative_id,
                'sub_criteria_id' => $validated['sub_criteria_id'],
                'period_id'       => $assignment->period_id,
            ],
            [
                'assessment_aspect_id' => $validated['assessment_aspect_id'],
                'notes'                => $validated['notes'] ?? null,
                'assessed_at'          => now(),
                'photo_path'           => $newPhotoPath,
                'photo_uploaded_at'    => $request->hasFile('photos') ? now() : $existingAssessment?->photo_uploaded_at,
            ]
        );

        if ($assignment->status === 'assigned') {
            $assignment->update([
                'status'     => 'in_progress',
                'started_at' => $assignment->started_at ?? now(),
            ]);
        }

        // Hitung total sub-kriteria yang harus dinilai.
        // Karena tidak ada relasi langsung SubCriteria -> Alternative,
        // bandingkan dengan jumlah assessment yang sudah selesai untuk assignment ini.
        $totalSubCriteria = SubCriteria::count();

        $completedSubCriteria = Assessment::where('surveyor_id', $surveyor->id)
            ->where('alternative_id', $assignment->alternative_id)
            ->where('period_id', $assignment->period_id)
            ->distinct('sub_criteria_id')
            ->count('sub_criteria_id');

        if ($completedSubCriteria >= $totalSubCriteria) {
            $assignment->update([
                'status'       => 'submitted',
                'submitted_at' => now(),
            ]);

            return redirect()->route('surveyor.task.show', $assignment)
                ->with('success', 'Semua penilaian selesai dan sudah dikumpulkan!');
        }

        return redirect()->route('surveyor.task.show', $assignment)
            ->with('success', 'Penilaian berhasil disimpan.');
    }

    /**
     * Menghapus satu foto spesifik dari penilaian
     */
    public function destroyPhoto(SurveyorAssignment $assignment, Request $request)
    {
        // FIX #5: Tambahkan auth check — surveyor hanya bisa hapus foto miliknya
        $surveyor = auth()->user()->surveyor;

        if (! $surveyor) {
            return redirect()->route('login')->with('error', 'Data surveyor tidak ditemukan.');
        }

        if ((int) $assignment->surveyor_id !== (int) $surveyor->id) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        $validated = $request->validate([
            'photo_path'    => ['required', 'string'],
            'assessment_id' => ['required', 'integer', 'exists:assessments,id'],
        ]);

        // FIX #5: Scope assessment ke surveyor yang login, bukan findOrFail biasa
        $assessment = Assessment::where('id', $validated['assessment_id'])
            ->where('surveyor_id', $surveyor->id)
            ->firstOrFail();

        // FIX #3: Decode konsisten menggunakan helper
        $photos = $this->decodePhotos($assessment->photo_path);

        $key = array_search($validated['photo_path'], $photos);

        if ($key === false) {
            return back()->with('error', 'Foto tidak ditemukan.');
        }

        if (\Storage::disk('public')->exists($validated['photo_path'])) {
            \Storage::disk('public')->delete($validated['photo_path']);
        }

        unset($photos[$key]);

        // Jika model punya cast 'array'/'json', kirim array langsung — Laravel encode otomatis.
        // Jika tidak ada cast, ganti dengan: json_encode(array_values($photos))
        $assessment->update([
            'photo_path' => array_values($photos),
        ]);

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    /**
     * Helper: decode photo_path secara konsisten, selalu kembalikan array.
     * FIX #2 & #3: Sentralisasi decode agar tidak ada null pointer di mana-mana.
     */
    private function decodePhotos(mixed $photoPath): array
    {
        if (empty($photoPath)) {
            return [];
        }

        if (is_array($photoPath)) {
            return array_values(array_filter($photoPath));
        }

        $decoded = json_decode($photoPath, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            return array_filter([$photoPath]);
        }

        return array_values(array_filter($decoded));
    }
}