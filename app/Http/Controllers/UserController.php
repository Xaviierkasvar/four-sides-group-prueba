<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Mostrar listado de usuarios
     */
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    /**
     * Mostrar detalles de un usuario
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Mostrar formulario para adjuntar foto de perfil
     */
    public function editPhoto(User $user)
    {
        return view('users.photo', compact('user'));
    }

    /**
     * Procesar y guardar la foto de perfil
     */
    public function updatePhoto(Request $request, User $user)
    {
        // Validación con mensajes personalizados
        $request->validate([
            'profile_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048', // 2MB máximo
            ],
        ], [
            'profile_image.required' => 'Debe seleccionar una imagen.',
            'profile_image.image' => 'El archivo debe ser una imagen.',
            'profile_image.mimes' => 'Solo se permiten imágenes .jpg, .jpeg o .png',
            'profile_image.max' => 'El tamaño de la imagen no debe exceder 2MB.',
        ]);
        
        // Usar directamente la carpeta public/images
        $uploadPath = public_path('images/profiles');
        
        // Asegurarse de que la carpeta existe
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        // Generar nombre único para la imagen
        $imageName = time() . '.' . $request->profile_image->extension();
        
        // Guardar la imagen
        $request->profile_image->move($uploadPath, $imageName);
        
        // Actualizar el registro del usuario
        $user->profile_image = $imageName;
        $user->save();
        
        return redirect()->route('users.show', $user)
            ->with('success', 'La imagen de perfil se ha actualizado correctamente.');
    }
}