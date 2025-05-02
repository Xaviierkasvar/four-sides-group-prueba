<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * URL de la API de EmailJS
     *
     * @var string
     */
    protected $apiUrl = 'https://api.emailjs.com/api/v1.0/email/send';

    /**
     * Mostrar el formulario para solicitar el restablecimiento de contraseña.
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Enviar el correo con el código de verificación utilizando EmailJS.
     * También muestra el código en pantalla para facilitar las pruebas.
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

            // Enviar el código por correo utilizando EmailJS
            $this->sendEmailWithCode($user, $verificationCode);
            
            // Para propósitos de prueba, almacenamos el código en la sesión
            session()->flash('verification_code', $verificationCode);
            
            // Almacenar email y token en la sesión para uso posterior
            session(['email' => $request->email, 'token' => $token]);

            // Mensaje que incluye el código para facilitar las pruebas
            $message = 'Hemos enviado un código de verificación a su correo electrónico. '.
                       'Para facilitar las pruebas, el código es: ' . $verificationCode;

            return redirect()->route('password.code')
                ->with('status', $message);
        } catch (\Exception $e) {
            // Registrar el error para depuración
            Log::error('Error en recuperación de contraseña: ' . $e->getMessage());
            
            return back()->withErrors([
                'email' => 'Ocurrió un error al procesar la solicitud. Por favor, intente nuevamente.',
            ]);
        }
    }

    /**
     * Enviar correo con código de verificación usando EmailJS
     * 
     * @param User $user
     * @param string $code
     * @return bool
     */
    protected function sendEmailWithCode($user, $code)
    {
        try {
            // Obtener configuración de EmailJS desde el archivo .env
            $serviceId = env('EMAILJS_SERVICE_ID');
            $templateId = env('EMAILJS_TEMPLATE_ID');
            $userId = env('EMAILJS_API_KEY');
            
            // Preparar datos para la plantilla
            $data = [
                'service_id' => $serviceId,
                'template_id' => $templateId,
                'user_id' => $userId,
                'template_params' => [
                    'user_name' => $user->name ?? 'Usuario',
                    'user_email' => $user->email,
                    'verification_code' => $code
                ]
            ];
            
            // Registrar lo que estamos enviando
            Log::debug('Enviando solicitud a EmailJS', [
                'email' => $user->email,
                'service_id' => $serviceId,
                'template_id' => $templateId
            ]);
            
            // Enviar solicitud a EmailJS
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Origin' => 'https://www.emailjs.com',
                'Referer' => 'https://www.emailjs.com',
            ])->post($this->apiUrl, $data);
            
            // Verificar la respuesta
            if ($response->successful()) {
                Log::info('Código de verificación enviado por email', [
                    'email' => $user->email
                ]);
                
                return true;
            } else {
                Log::error('Error en la respuesta de EmailJS', [
                    'email' => $user->email,
                    'response' => $response->body()
                ]);
                
                // No marcar como error para continuar con el flujo
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Excepción al enviar correo con EmailJS', [
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
            
            // No marcar como error para continuar con el flujo
            return false;
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
            // Añadir logs de depuración
            Log::debug('Verificando código', [
                'email' => $email,
                'código_ingresado' => $request->verification_code
            ]);
            
            $resetData = DB::table('password_reset_tokens')
                ->where('email', $email)
                ->where('verification_code', $request->verification_code)
                ->first();

            if ($resetData) {
                Log::debug('Datos encontrados', [
                    'código_almacenado' => $resetData->verification_code,
                    'fecha_creación' => $resetData->created_at
                ]);
            } else {
                Log::debug('No se encontraron datos con el código proporcionado');
            }

            if (!$resetData) {
                return back()->withErrors([
                    'verification_code' => 'El código ingresado es incorrecto',
                ]);
            }

            // Verificar que el código no tenga más de 15 minutos
            $createdAt = Carbon::parse($resetData->created_at);
            if ($createdAt->diffInMinutes(Carbon::now()) > 15) {
                return back()->withErrors([
                    'verification_code' => 'El código ha expirado. Por favor, solicite uno nuevo.',
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

    /**
     * Mostrar el formulario para restablecer la contraseña.
     */
    public function showResetForm(Request $request, $token)
    {
        $email = session('email');
        
        if (!$email) {
            return redirect()->route('password.request');
        }
        
        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $email
        ]);
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
        ]);

        try {
            // Verificar el token almacenado en la base de datos
            $tokenData = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->where('token', $request->token)
                ->first();

            if (!$tokenData) {
                return redirect()->route('password.request')
                    ->withErrors(['email' => 'Token inválido o expirado.']);
            }

            // Verificar que el token no tenga más de 15 minutos de antigüedad
            $createdAt = Carbon::parse($tokenData->created_at);
            if ($createdAt->diffInMinutes(Carbon::now()) > 15) {
                DB::table('password_reset_tokens')->where('email', $request->email)->delete();
                
                return redirect()->route('password.request')
                    ->withErrors(['email' => 'El token ha expirado. Por favor, solicite uno nuevo.']);
            }

            // Actualizar la contraseña del usuario
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return redirect()->route('password.request')
                    ->withErrors(['email' => 'No se encontró un usuario con ese correo electrónico.']);
            }

            $user->password = bcrypt($request->password);
            $user->save();

            // Eliminar el token de restablecimiento
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            // Limpiar la sesión
            session()->forget(['email', 'token']);

            return redirect()->route('login')
                ->with('status', 'Contraseña Restablecida');
        } catch (\Exception $e) {
            Log::error('Error al restablecer contraseña: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['email' => 'Ocurrió un error al restablecer la contraseña. Por favor, intente nuevamente.']);
        }
    }
}