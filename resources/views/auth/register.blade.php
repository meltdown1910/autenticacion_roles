@extends('layouts.app')

@section('title', 'Registro de empleados')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">

        <div class="card shadow">
            <div class="card-body p-4">

                <h2 class="text-center mb-4">
                    Registro de empleados
                </h2>

                <form action="{{ route('register.post') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            Nombre completo
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Ingresa tu nombre"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Ingresa tu correo"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Contraseña
                        </label>

                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="Mínimo 8 caracteres"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">
                            Confirmar contraseña
                        </label>

                        <input
                            type="password"
                            class="form-control"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Repite tu contraseña"
                            required
                        >
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-success">
                            Registrarme
                        </button>
                    </div>

                </form>

                <hr>

                <p class="text-center mb-0">
                    ¿Ya tienes una cuenta?
                    <a href="{{ route('login') }}">
                        Inicia sesión aquí
                    </a>
                </p>

            </div>
        </div>

    </div>
</div>

@endsection
