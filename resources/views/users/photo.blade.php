@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Adjuntar Foto de Perfil</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('users.update.photo', $user) }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-3 row">
                <label class="col-md-4 col-form-label text-md-end">Usuario:</label>
                <div class="col-md-6">
                    <p class="form-control-static">{{ $user->name }}</p>
                </div>
            </div>

            <!-- Vista previa de la imagen actual si existe -->
            @if ($user->profile_image)
                <div class="mb-3 row">
                    <label class="col-md-4 col-form-label text-md-end">Imagen Actual:</label>
                    <div class="col-md-6">
                        <img src="{{ asset('images/profiles/' . $user->profile_image) }}" 
                            alt="Perfil de {{ $user->name }}" 
                            class="img-thumbnail" 
                            style="max-width: 150px">
                    </div>
                </div>
            @endif

            <div class="mb-3 row">
                <label for="profile_image" class="col-md-4 col-form-label text-md-end">Nueva Imagen:</label>
                <div class="col-md-6">
                    <input id="profile_image" type="file" class="form-control @error('profile_image') is-invalid @enderror" name="profile_image" required>
                    
                    @error('profile_image')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror

                    <div class="form-text">
                        Solo se permiten archivos JPG, JPEG y PNG (máx. 2MB)
                    </div>
                </div>
            </div>

            <!-- Vista previa de la imagen seleccionada -->
            <div class="mb-3 row" id="previewContainer" style="display: none;">
                <label class="col-md-4 col-form-label text-md-end">Vista Previa:</label>
                <div class="col-md-6">
                    <img id="imagePreview" src="#" alt="Vista previa" class="img-thumbnail" style="max-width: 150px">
                </div>
            </div>

            <div class="mb-3 row">
                <div class="col-md-6 offset-md-4">
                    <button type="submit" class="btn btn-primary">
                        Guardar
                    </button>
                    <a href="{{ route('users.show', $user) }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Script para vista previa de imagen
    document.getElementById('profile_image').onchange = function(e) {
        const previewContainer = document.getElementById('previewContainer');
        const preview = document.getElementById('imagePreview');
        const file = this.files[0];
        
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.style.display = 'flex';
            }
            
            reader.readAsDataURL(file);
        } else {
            preview.src = '';
            previewContainer.style.display = 'none';
        }
    };
</script>
@endpush
@endsection