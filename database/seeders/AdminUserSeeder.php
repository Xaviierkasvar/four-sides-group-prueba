<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1) Creamos el administrador; created_by = null
        $admin = User::create([
            'name'       => 'Administrador',
            'email'      => 'admin@mail.com',
            'password'   => Hash::make('admin123'),
            'is_admin'   => true,
            'created_by' => null,
        ]);

        // 2) Creamos un usuario normal; lo “crea” el admin
        User::create([
            'name'       => 'Usuario Normal',
            'email'      => 'user@mail.com',
            'password'   => Hash::make('admin123'),
            'is_admin'   => false,
            'created_by' => $admin->id,
        ]);
    }
}
