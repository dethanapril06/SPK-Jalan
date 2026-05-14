<div class="form-group">
    <label for="photo_{{ $fieldId ?? 'photo' }}" class="form-label">
        <span class="text-danger">*</span> Foto Penilaian
    </label>
    <small class="text-muted d-block mb-2">
        <i class="bi bi-info-circle"></i>
        Bisa memilih lebih dari satu foto. Ukuran maksimal 10 MB per foto. Format: JPG, PNG, GIF, WebP.
    </small>

    <div class="input-group">
        <input type="file" id="photo_{{ $fieldId ?? 'photo' }}" name="photos[]" multiple
            class="form-control @error('photos') is-invalid @enderror @error('photos.*') is-invalid @enderror" accept="image/*"
            data-preview-id="photo-preview-{{ $fieldId ?? 'photo' }}">
    </div>

    @error('photos')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    @error('photos.*')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror

    <div id="photo-preview-{{ $fieldId ?? 'photo' }}" class="mt-3 d-flex flex-wrap gap-2">
        @if (isset($assessment) && $assessment->photo_path)
            @php
                // Mengakomodasi jika data lama berbentuk string tunggal, atau data baru berbentuk array/JSON
                $photos = is_string($assessment->photo_path) ? json_decode($assessment->photo_path, true) : $assessment->photo_path;
                if (!is_array($photos)) {
                    $photos = [$assessment->photo_path]; 
                }
            @endphp
            
            @foreach(array_filter($photos) as $photo)
                <div class="position-relative d-inline-block">
                    <img src="{{ asset('storage/' . $photo) }}" alt="Foto penilaian" class="img-thumbnail"
                        style="max-width: 150px; height: auto;"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <div class="alert alert-light-warning small mt-2" style="display:none;">
                        File foto tidak ditemukan.
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle file preview untuk multiple files
            const fileInputs = document.querySelectorAll('input[type="file"][data-preview-id]');
            fileInputs.forEach(input => {
                input.addEventListener('change', function(e) {
                    const previewId = this.getAttribute('data-preview-id');
                    const previewDiv = document.getElementById(previewId);
                    
                    // Kosongkan preview sebelumnya (hanya visual saat user memilih file baru)
                    previewDiv.innerHTML = '';

                    if (this.files && this.files.length > 0) {
                        const maxSize = 10 * 1024 * 1024; // 10 MB
                        let hasError = false;

                        Array.from(this.files).forEach(file => {
                            if (file.size > maxSize) {
                                hasError = true;
                            } else {
                                const objectUrl = URL.createObjectURL(file);
                                
                                const imgContainer = document.createElement('div');
                                imgContainer.className = 'mt-3 me-2 d-inline-block';
                                imgContainer.innerHTML = `
                                    <img src="${objectUrl}" alt="Preview foto" class="img-thumbnail" style="max-width: 150px; height: auto;">
                                    <div class="small text-muted mt-1 text-center">
                                        ${(file.size / 1024 / 1024).toFixed(2)} MB
                                    </div>
                                `;
                                
                                previewDiv.appendChild(imgContainer);

                                const previewImage = imgContainer.querySelector('img');
                                previewImage?.addEventListener('load', () => URL.revokeObjectURL(objectUrl), { once: true });
                            }
                        });

                        if (hasError) {
                            previewDiv.insertAdjacentHTML('afterbegin', `
                                <div class="alert alert-light-danger small mb-2 w-100">
                                    Beberapa foto terlalu besar dan tidak ditampilkan di preview. Maksimal 10 MB per file.
                                </div>
                            `);
                        }
                    }
                });
            });
        });
    </script>
@endpush