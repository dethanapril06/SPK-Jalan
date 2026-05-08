<!-- Photo Upload Component -->
<div class="form-group">
    <label for="photo_{{ $fieldId ?? 'photo' }}" class="form-label">
        <span class="text-danger">*</span> Foto Penilaian
    </label>
    <small class="text-muted d-block mb-2">
        <i class="bi bi-info-circle"></i>
        Saat dibuka dari HP akan langsung membuka kamera. Ukuran maksimal 10 MB. Format: JPG, PNG, GIF, WebP.
    </small>

    <div class="input-group">
        <input type="file" id="photo_{{ $fieldId ?? 'photo' }}" name="photo"
            class="form-control @error('photo') is-invalid @enderror" accept="image/*" capture="environment"
            data-preview-id="photo-preview-{{ $fieldId ?? 'photo' }}">
    </div>

    @error('photo')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror

    <!-- Photo Preview -->
    <div id="photo-preview-{{ $fieldId ?? 'photo' }}" class="mt-3">
        @if (isset($assessment) && $assessment->photo_path)
            <div class="position-relative d-inline-block">
                <img src="{{ asset('storage/' . $assessment->photo_path) }}" alt="Foto penilaian" class="img-thumbnail"
                    style="max-width: 300px; height: auto;"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <div class="alert alert-light-warning small mt-2" style="display:none;">
                    File foto tidak ditemukan. Silakan upload ulang.
                </div>
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 delete-photo-btn"
                    data-assessment-id="{{ $assessment->id }}" title="Hapus foto">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle file preview
            const fileInputs = document.querySelectorAll('input[type="file"][data-preview-id]');
            fileInputs.forEach(input => {
                input.addEventListener('change', function(e) {
                    const previewId = this.getAttribute('data-preview-id');
                    const previewDiv = document.getElementById(previewId);

                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        const maxSize = 10 * 1024 * 1024;

                        if (file.size > maxSize) {
                            previewDiv.innerHTML = `
                                <div class="alert alert-light-danger small mb-0">
                                    Ukuran foto terlalu besar. Maksimal 10 MB.
                                </div>
                            `;
                            this.value = '';
                            return;
                        }

                        const objectUrl = URL.createObjectURL(file);

                        previewDiv.innerHTML = `
                            <div class="mt-3">
                                <img 
                                    src="${objectUrl}" 
                                    alt="Preview foto"
                                    class="img-thumbnail"
                                    style="max-width: 300px; height: auto;"
                                >
                                <div class="small text-muted mt-2">
                                    ${(file.size / 1024 / 1024).toFixed(2)} MB
                                </div>
                            </div>
                        `;

                        const previewImage = previewDiv.querySelector('img');
                        previewImage?.addEventListener('load', () => URL.revokeObjectURL(
                            objectUrl), {
                            once: true
                        });
                    } else {
                        previewDiv.innerHTML = '';
                    }
                });
            });

            // Handle delete photo button (jika ada)
            const deletePhotoButtons = document.querySelectorAll('.delete-photo-btn');
            deletePhotoButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Ini akan di-handle di controller, bisa di-trigger via AJAX
                    alert('Foto akan dihapus setelah form disimpan.');
                });
            });
        });
    </script>
@endpush
