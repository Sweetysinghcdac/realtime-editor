<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Document; // Import your model
use App\Policies\DocumentPolicy;
use Illuminate\Support\Facades\Gate;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    protected $policies = [
        Document::class =>DocumentPolicy::class,
        \App\Models\Invitation::class => \App\Policies\InvitationPolicy::class,
    ];
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
