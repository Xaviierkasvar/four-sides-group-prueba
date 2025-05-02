<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Alert;
use App\Policies\AlertPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Alert::class => AlertPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
