@extends('layouts.app')

@section('title', 'Panel de administrador')

@section('content')

<div class="container">

    <div class="card shadow">
        <div class="card-body p-5">

            <h1 class="text-center mb-3">
                Panel de administrador
            </h1>

            <p class="text-center text-muted">
                Bienvenido al sistema de autenticación.
            </p>

            <hr>

            <h4>Información de la sesión</h4>

            <p>
                <strong>Nombre:</strong>
                {{ Auth::user()->name }}
            </p>

            <p>
                <strong>Correo electrónico:</strong>
                {{ Auth::user()->email }}
            </p>

            <p>
                <strong>Rol:</strong>
                {{ Auth::user()->rol }}
            </p>

            <div class="d-grid mt-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf

                    <button type="submit" class="btn btn-danger">
                        Cerrar sesión
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>

@endsection
