@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Detalle del Usuario</h2>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-4 text-center">
                @if ($user->profile_image)
                    <img src="{{ asset('images/profiles/' . $user->profile_image) }}" 
                        alt="Perfil de {{ $user->name }}" 
                        class="img-thumbnail mb-3" 
                        style="max-width: 150px">
                @else
                    <div class="text-center p-5 bg-light mb-3">
                        <p class="mt-2 text-muted">Sin imagen de perfil</p>
                    </div>
                @endif

                <a href="{{ route('users.edit.photo', $user) }}" class="btn btn-primary">
                    {{ $user->profile_image ? 'Cambiar Foto' : 'Adjuntar Foto' }}
                </a>
            </div>
            <div class="col-md-8">
                <h3>{{ $user->name }}</h3>
                <p><strong>Correo:</strong> {{ $user->email }}</p>
                <p><strong>Registrado:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
                
                <a href="{{ route('users.index') }}" class="btn btn-secondary mt-3">
                    Volver al listado
                </a>
            </div>
        </div>
    </div>
</div>
@endsection