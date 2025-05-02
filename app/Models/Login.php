<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Login extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'role_id',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = session('role_id');
            $model->updated_by = session('role_id');
        });

        static::updating(function ($model) {
            $model->updated_by = session('role_id');
        });
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
