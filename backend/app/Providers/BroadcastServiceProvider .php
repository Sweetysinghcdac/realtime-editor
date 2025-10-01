<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // This line is CRUCIAL to load your authorization logic
        // Broadcast::routes(); 
        Broadcast::routes(['middleware' => ['auth:sanctum']]); 
        // Broadcast::routes(['prefix' => 'api', 'middleware' => ['auth:sanctum']]); 

        require base_path('routes/channels.php');
    }
}