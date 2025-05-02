<!-- resources/views/auth/passwords/code.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Verificar Código') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.verify') }}">
                        @csrf

                        <div class="mb-3 row">
                            <label for="verification_code" class="col-md-4 col-form-label text-md-end">{{ __('Código de Verificación') }}</label>

                            <div class="col-md-6">
                                <input id="verification_code" type="text" class="form-control @error('verification_code') is-invalid @enderror" name="verification_code" required autofocus>

                                @error('verification_code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Verificar') }}
                                </button>
                                <a href="{{ route('login') }}" class="btn btn-secondary">
                                    {{ __('Cancelar') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection