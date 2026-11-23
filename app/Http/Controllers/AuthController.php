<?php

namespace App\Http\Controllers;

use App\Mail\AdminVerificationCodeMail;
use App\Models\AdminVerificationCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Throwable;

class AuthController extends Controller
{
    // Mostrar formulario de inicio de sesión
    public function showLogin()
    {
        return view('auth.login');
    }

    // Validar credenciales e identificar el rol
    public function login(Request $request)
    {
        // Eliminar cualquier verificación administrativa pendiente anterior
        $request->session()->forget('pending_admin_id');

        $datos = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $key = Str::lower($datos['email']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors([
                'email' => 'Demasiados intentos. Intenta nuevamente en un minuto.',
            ])->onlyInput('email');
        }

        if (!Auth::validate($datos)) {
            RateLimiter::hit($key, 60);

            return back()->withErrors([
                'email' => 'Las credenciales ingresadas son incorrectas.',
            ])->onlyInput('email');
        }

        RateLimiter::clear($key);

        $user = User::where('email', $datos['email'])->firstOrFail();

        // Acceso directo para empleados
        if ($user->rol === 'empleado') {
            
            Auth::login($user);
            
            $request->session()->regenerate();

            return redirect()->route('empleado.dashboard');
        }

        // Verificación adicional para administradores
        if ($user->rol === 'administrador') {

            $codigo = (string) random_int(100000, 999999);

            AdminVerificationCode::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'code_hash' => Hash::make($codigo),
                    'expires_at' => now()->addMinutes(5),
                    'attempts' => 0,
                ]
            );

            $request->session()->put('pending_admin_id', $user->id);
            $request->session()->regenerate();

            try {
                Mail::to($user->email)
                    ->send(new AdminVerificationCodeMail($codigo));
            } catch (Throwable $e) {
                report($e);

                AdminVerificationCode::where('user_id', $user->id)
                    ->delete();

                $request->session()->forget('pending_admin_id');

                return back()->withErrors([
                    'email' => 'No fue posible enviar el código. Intenta nuevamente.',
                ])->onlyInput('email');
            }

            return redirect()->route('verify-code')
                ->with('success', 'Se envió un código de verificación a tu correo.');
        }

        return back()->withErrors([
            'email' => 'El rol del usuario no es válido.',
        ])->onlyInput('email');
    }

    // Mostrar formulario de registro
    public function showRegister()
    {
        return view('auth.register');
    }

    // Registrar empleados
    public function register(Request $request)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => Hash::make($datos['password']),
            'rol' => 'empleado',
        ]);

        return redirect()->route('login')->with(
            'success',
            'Registro completado. Ahora puedes iniciar sesión.'
        );
    }

    // Mostrar formulario de verificación
    public function showVerifyCode(Request $request)
    {
        $userId = $request->session()->get('pending_admin_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        $verification = AdminVerificationCode::where('user_id', $userId)
            ->where('expires_at', '>', now())
            ->first();

        if (!$verification) {
            $request->session()->forget('pending_admin_id');

            return redirect()->route('login')->withErrors([
                'email' => 'La verificación venció. Inicia sesión nuevamente.',
            ]);
        }

        return view('auth.verify-code');
    }

    // Comprobar código de verificación
    public function verifyCode(Request $request)
    {
        $datos = $request->validate([
            'code' => 'required|digits:6',
        ]);

        $userId = $request->session()->get('pending_admin_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (!$user || $user->rol !== 'administrador') {
            $request->session()->forget('pending_admin_id');

            return redirect()->route('login');
        }

        $verification = AdminVerificationCode::where('user_id', $userId)
            ->first();

        if (!$verification) {
            $request->session()->forget('pending_admin_id');

            return redirect()->route('login')->withErrors([
                'email' => 'No existe un código de verificación válido.',
            ]);
        }

        if ($verification->expires_at->isPast()) {
            $verification->delete();
            $request->session()->forget('pending_admin_id');

            return redirect()->route('login')->withErrors([
                'email' => 'El código ha vencido. Inicia sesión nuevamente.',
            ]);
        }

        if ($verification->attempts >= 5) {
            $verification->delete();
            $request->session()->forget('pending_admin_id');

            return redirect()->route('login')->withErrors([
                'email' => 'Se agotaron los intentos. Inicia sesión nuevamente.',
            ]);
        }

        $verification->increment('attempts');

        if (!Hash::check($datos['code'], $verification->code_hash)) {

            if ($verification->attempts >= 5) {
                $verification->delete();
                $request->session()->forget('pending_admin_id');

                return redirect()->route('login')->withErrors([
                    'email' => 'Se agotaron los intentos de verificación.',
                ]);
            }

            return back()->withErrors([
                'code' => 'El código ingresado es incorrecto.',
            ]);
        }

        // Código correcto: completar autenticación
        $verification->delete();

        $request->session()->forget('pending_admin_id');

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Autenticación completada correctamente.');
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with(
            'success',
            'Sesión cerrada correctamente.'
        );
    }
}