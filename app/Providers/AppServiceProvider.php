<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Auth::viaRequest('api_token', function (Request $request) {
            $token = $request->bearerToken();

            if (! $token) {
                return null;
            }

            return User::where('api_token', hash('sha256', $token))->first();
        });
    }
}
