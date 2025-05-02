<?php

namespace App\Policies;

use App\Models\Alert;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AlertPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Alert $alert)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->role_id == 1; // Solo administradores pueden crear
    }

    public function update(User $user, Alert $alert)
    {
        return $user->role_id == 1; // Solo administradores pueden actualizar
    }

    public function delete(User $user, Alert $alert)
    {
        return $user->role_id == 1; // Solo administradores pueden eliminar
    }
}
