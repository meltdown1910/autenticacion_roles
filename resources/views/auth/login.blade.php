@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">

        <div class="card shadow">
            <div class="card-body p-4">

                <h2 class="text-center mb-4">
                    Iniciar sesión
                </h2>

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf

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
                            placeholder="Ingresa tu contraseña"
                            required
                        >
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">
                            Iniciar sesión
                        </button>
                    </div>

                </form>

                <hr>

                <p class="text-center mb-0">
                    ¿No tienes una cuenta?
                    <a href="{{ route('register') }}">
                        Regístrate aquí
                    </a>
                </p>

            </div>
        </div>

    </div>
</div>

@endsection
