<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // Si utilizas la tabla por defecto "roles", no es necesario especificar el nombre
    protected $table = 'roles';

    // Definición de los atributos que se pueden llenar
    protected $fillable = [
        'name', // Nombre del rol
    ];

    // Definición de la relación con el modelo User
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
