@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Catálogo de Usuarios</h2>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Foto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if ($user->profile_image)
                                <img src="{{ asset('images/profiles/' . $user->profile_image) }}" 
                                    alt="Perfil de {{ $user->name }}" 
                                    class="img-thumbnail" 
                                    style="max-width: 50px">
                            @else
                                <span class="text-muted">Sin imagen</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info">Ver Detalles</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection