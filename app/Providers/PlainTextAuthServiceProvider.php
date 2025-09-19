<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Auth\PlainTextUserProvider;
use Illuminate\Support\Facades\Gate;

class PlainTextAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register the plain text user provider
        Auth::provider('plaintext', function ($app, array $config) {
            return new PlainTextUserProvider($app['hash'], $config['model']);
        });

        // Extend the auth system to use our custom guard
        Auth::extend('plaintext', function ($app, $name, array $config) {
            $guard = new \Illuminate\Auth\SessionGuard(
                $name,
                Auth::createUserProvider($config['provider']),
                $app['session.store'],
                $app['request']
            );

            // Set the cookie jar if available
            if (method_exists($guard, 'setCookieJar')) {
                $guard->setCookieJar($app['cookie']);
            }

            // Set the request instance
            if (method_exists($guard, 'setRequest')) {
                $guard->setRequest($app->refresh('request', $guard, 'setRequest'));
            }

            return $guard;
        });
    }
}
