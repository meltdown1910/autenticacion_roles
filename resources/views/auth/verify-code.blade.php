@extends('layouts.app')

@section('title', 'Verificación de administrador')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">

        <div class="card shadow">
            <div class="card-body p-4">

                <h2 class="text-center mb-3">
                    Verificación de administrador
                </h2>

                <p class="text-center text-muted mb-4">
                    Hemos enviado un código de verificación a tu correo.
                    Introdúcelo para completar el inicio de sesión.
                </p>

                <form action="{{ route('verify-code.post') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="code" class="form-label">
                            Código de verificación
                        </label>

                        <input
                            type="text"
                            class="form-control text-center"
                            id="code"
                            name="code"
                            placeholder="000000"
                            maxlength="6"
                            minlength="6"
                            pattern="[0-9]{6}"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            required
                        >
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">
                            Verificar código
                        </button>
                    </div>

                </form>

                <hr>

                <p class="text-center mb-0">
                    <a href="{{ route('login') }}">
                        Volver al inicio de sesión
                    </a>
                </p>

            </div>
        </div>

    </div>
</div>

@endsection
