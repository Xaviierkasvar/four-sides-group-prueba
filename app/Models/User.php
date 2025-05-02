<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Si utilizas la tabla por defecto "users", no es necesario especificar el nombre
    protected $table = 'users';

    // Definición de los atributos que se pueden llenar
    protected $fillable = [
        'email',
        'name',
        'last_name',
        'phone',
        'password',
        'role_id',
        'is_active',    // Agregado el campo 'is_active'
        'created_by',   // Agregado el campo 'created_by'
        'updated_by',   // Agregado el campo 'updated_by'
    ];

    // Campos que no deberían ser visibles en las respuestas JSON
    protected $hidden = [
        'password', // Contraseña oculta
    ];

    // Campos que se deben cast a tipos específicos
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active'  => 'boolean', // Cast para is_active
    ];

    // Definición de la relación con Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }
}
