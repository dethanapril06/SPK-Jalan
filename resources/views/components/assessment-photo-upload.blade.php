{{--
    FIX #6: Form delete dipindahkan ke LUAR form utama.
    Component ini hanya berisi input file dan preview.
    Form delete di-render via @push('modals') agar tidak nested di dalam <form> utama.
--}}

<div class="form-group">
    <label for="photo_{{ $fieldId ?? 'photo' }}" class="form-label">
        @php
            $hasPhotos = false;
            if (isset($assessment) && $assessment->photo_path) {
                $raw = $assessment->photo_path;
                $decoded = is_array($raw) ? $raw : json_decode($raw, true) ?? [];
                $hasPhotos = is_array($decoded) && count(array_filter($decoded)) > 0;
            }
        @endphp
        @if (!$hasPhotos)
            <span class="text-danger">*</span>
        @endif
        Foto Penilaian
    </label>
    <small class="text-muted d-block mb-2">
        <i class="bi bi-info-circle"></i>
        Bisa memilih lebih dari satu foto (maks. 5 total). Ukuran maksimal 10 MB per foto. Format: JPG, PNG, GIF, WebP.
    </small>

    @php
        $existingPhotos = [];
        if (isset($assessment) && $assessment->photo_path) {
            $raw = $assessment->photo_path;

            // Jika model punya cast array/json, $raw sudah berupa array.
            // Jika tidak ada cast, $raw masih string JSON — decode dulu.
            if (is_array($raw)) {
                $existingPhotos = array_values(array_filter($raw));
            } elseif (is_string($raw)) {
                $decoded = json_decode($raw, true);
                $existingPhotos = is_array($decoded) ? array_values(array_filter($decoded)) : [];
            }
        }
        $existingCount = count($existingPhotos);
        $remainingSlots = max(0, 5 - $existingCount);
    @endphp

    @if ($remainingSlots > 0)
        <div class="input-group">
            <input type="file" id="photo_{{ $fieldId ?? 'photo' }}" name="photos[]" multiple
                class="form-control @error('photos') is-invalid @enderror @error('photos.*') is-invalid @enderror"
                accept="image/*" data-preview-id="photo-preview-{{ $fieldId ?? 'photo' }}"
                data-max-files="{{ $remainingSlots }}">
        </div>
        <small class="text-muted">Sisa slot: {{ $remainingSlots }} foto lagi.</small>
    @else
        <div class="alert alert-light-warning small mb-2">
            <i class="bi bi-exclamation-triangle me-1"></i>
            Batas maksimal 5 foto sudah tercapai. Hapus foto lama untuk mengunggah yang baru.
        </div>
    @endif

    @error('photos')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    @error('photos.*')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror

    {{-- Preview foto yang sudah ada --}}
    <div id="photo-preview-{{ $fieldId ?? 'photo' }}" class="mt-3 d-flex flex-wrap gap-3">
        @foreach (array_filter($existingPhotos) as $photo)
            <div class="position-relative d-inline-block shadow-sm rounded p-1 border"
                id="photo-item-{{ md5($photo) }}">
                <img src="{{ asset('storage/' . $photo) }}" alt="Foto penilaian" class="img-thumbnail border-0"
                    style="width: 150px; height: 150px; object-fit: cover;"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <div class="alert alert-light-warning small mt-2" style="display:none;">
                    File foto tidak ditemukan.
                </div>
                <div class="mt-2 text-center">
                    <button type="button" class="btn btn-danger btn-sm w-100"
                        onclick="confirmDeletePhoto('{{ $photo }}', '{{ $assessment->id ?? '' }}')">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{--
    FIX #6: Form delete diletakkan di @push('modals') agar di-render
    di LUAR <form> utama, mencegah nested form yang tidak valid secara HTML.
--}}
@push('modals')
    <form id="delete-photo-form" action="{{ route('surveyor.assessment.photo.destroy', $assignment) }}" method="POST"
        style="display: none;">
        @csrf
        @method('DELETE')
        <input type="hidden" name="photo_path" id="delete-photo-path">
        <input type="hidden" name="assessment_id" id="delete-assessment-id">
    </form>
@endpush

@push('scripts')
    <script>
        // Fungsi global untuk dipanggil oleh onclick
        function confirmDeletePhoto(path, assessmentId) {
            if (!confirm('Apakah Anda yakin ingin menghapus foto ini secara permanen?')) return;

            const pathInput = document.getElementById('delete-photo-path');
            const assessmentInput = document.getElementById('delete-assessment-id');
            const deleteForm = document.getElementById('delete-photo-form');

            if (!pathInput || !assessmentInput || !deleteForm) {
                console.error('Elemen form hapus tidak ditemukan!');
                return;
            }

            pathInput.value = path;
            assessmentInput.value = assessmentId;
            deleteForm.submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const fileInputs = document.querySelectorAll('input[type="file"][data-preview-id]');

            fileInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const previewId = this.getAttribute('data-preview-id');
                    const previewDiv = document.getElementById(previewId);

                    // FIX: Hanya hapus preview baru (bukan foto lama yang sudah ada)
                    previewDiv.querySelectorAll('.new-photo-preview').forEach(el => el.remove());

                    if (!this.files || this.files.length === 0) return;

                    // FIX: Validasi jumlah file sesuai sisa slot
                    const maxFiles = parseInt(this.getAttribute('data-max-files') || '5', 10);
                    if (this.files.length > maxFiles) {
                        previewDiv.insertAdjacentHTML('afterbegin', `
                            <div class="alert alert-light-danger small mb-2 w-100 new-photo-preview">
                                Anda memilih ${this.files.length} foto, tapi hanya ${maxFiles} slot tersisa.
                                Hanya ${maxFiles} foto pertama yang akan diproses.
                            </div>
                        `);
                    }

                    const maxSize = 10 * 1024 * 1024; // 10 MB
                    let hasError = false;

                    Array.from(this.files).forEach((file, index) => {
                        if (index >= maxFiles) return; // Skip jika melebihi slot

                        if (file.size > maxSize) {
                            hasError = true;
                            return;
                        }

                        const objectUrl = URL.createObjectURL(file);
                        const imgContainer = document.createElement('div');
                        imgContainer.className =
                            'mt-3 me-2 d-inline-block new-photo-preview';
                        imgContainer.innerHTML = `
                            <img src="${objectUrl}" alt="Preview foto" class="img-thumbnail" style="max-width: 150px; height: auto;">
                            <div class="small text-muted mt-1 text-center">
                                ${(file.size / 1024 / 1024).toFixed(2)} MB
                            </div>
                        `;
                        previewDiv.appendChild(imgContainer);

                        const previewImage = imgContainer.querySelector('img');
                        previewImage?.addEventListener('load', () => URL.revokeObjectURL(
                            objectUrl), {
                            once: true
                        });
                    });

                    if (hasError) {
                        previewDiv.insertAdjacentHTML('afterbegin', `
                            <div class="alert alert-light-danger small mb-2 w-100 new-photo-preview">
                                Beberapa foto terlalu besar dan tidak ditampilkan. Maksimal 10 MB per file.
                            </div>
                        `);
                    }
                });
            });
        });
    </script>
@endpush
