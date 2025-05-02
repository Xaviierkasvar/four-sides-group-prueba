<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /**
     * Mostrar el formulario para restablecer la contraseña.
     */
    public function showResetForm(Request $request, $token)
    {
        $email = $request->session()->get('email', $request->email);
        
        if (!$email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'La sesión ha expirado. Por favor, solicite un nuevo enlace.']);
        }
        
        // Verificar que el token corresponda al email
        $validToken = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $token)
            ->exists();
            
        if (!$validToken) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'El enlace de restablecimiento de contraseña no es válido.']);
        }

        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $email]
        );
    }

    /**
     * Restablecer la contraseña.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.min' => 'La contraseña debe contener al menos 8 caracteres.',
        ]);

        try {
            // Verificar que el token corresponda al email
            $validToken = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->where('token', $request->token)
                ->exists();
                
            if (!$validToken) {
                return redirect()->route('password.request')
                    ->withErrors(['email' => 'El enlace de restablecimiento de contraseña no es válido.']);
            }

            // Actualizar la contraseña del usuario
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return redirect()->route('password.request')
                    ->withErrors(['email' => 'No se encontró un usuario con este correo electrónico.']);
            }

            $user->password = Hash::make($request->password);
            $user->setRememberToken(Str::random(60));
            $user->save();

            // Eliminar el token de restablecimiento
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            // Redirigir al login con mensaje de éxito
            return redirect()->route('login')
                ->with('status', 'Contraseña Restablecida');
                
        } catch (\Exception $e) {
            // Registrar el error para depuración
            Log::error('Error en restablecimiento de contraseña: ' . $e->getMessage());
            
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Ocurrió un error al restablecer la contraseña. Por favor, intente nuevamente.']);
        }
    }
}