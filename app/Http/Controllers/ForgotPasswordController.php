<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Mostrar el formulario para solicitar el restablecimiento de contraseña.
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Enviar el correo con el código de verificación.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'El correo ingresado no se encuentra en la base de datos',
            ]);
        }

        // Generar un código de verificación aleatorio de 6 dígitos
        $verificationCode = sprintf('%06d', mt_rand(1, 999999));
        
        // Generar un token aleatorio para la ruta de restablecimiento
        $token = Str::random(64);

        try {
            // Almacenar el token y el código de verificación
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'token' => $token,
                    'verification_code' => $verificationCode,
                    'created_at' => Carbon::now()
                ]
            );

            // En lugar de enviar un correo, almacenamos el código en la sesión para pruebas
            // En un entorno de producción, aquí se enviaría el correo
            session()->flash('verification_code', $verificationCode);
            
            // Almacenar email y token en la sesión para uso posterior
            session(['email' => $request->email, 'token' => $token]);

            return redirect()->route('password.code')
                ->with('status', 'Hemos generado un código de verificación. Para pruebas, el código es: ' . $verificationCode);
        } catch (\Exception $e) {
            // Registrar el error para depuración
            Log::error('Error en recuperación de contraseña: ' . $e->getMessage());
            
            return back()->withErrors([
                'email' => 'Ocurrió un error al procesar la solicitud. Por favor, intente nuevamente.',
            ]);
        }
    }

    /**
     * Mostrar el formulario para ingresar el código de verificación.
     */
    public function showVerificationCodeForm()
    {
        if (!session('email') || !session('token')) {
            return redirect()->route('password.request');
        }

        return view('auth.passwords.code');
    }

    /**
     * Verificar el código ingresado.
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string|size:6',
        ]);

        $email = session('email');
        $token = session('token');

        if (!$email || !$token) {
            return redirect()->route('password.request');
        }

        try {
            $resetData = DB::table('password_reset_tokens')
                ->where('email', $email)
                ->where('verification_code', $request->verification_code)
                ->first();

            if (!$resetData) {
                return back()->withErrors([
                    'verification_code' => 'El código ingresado es incorrecto',
                ]);
            }

            // Si el código es correcto, redirigir al formulario para nueva contraseña
            return redirect()->route('password.reset', ['token' => $token])
                ->with('email', $email);
        } catch (\Exception $e) {
            // Registrar el error para depuración
            Log::error('Error en verificación de código: ' . $e->getMessage());
            
            return back()->withErrors([
                'verification_code' => 'Ocurrió un error al verificar el código. Por favor, intente nuevamente.',
            ]);
        }
    }
}