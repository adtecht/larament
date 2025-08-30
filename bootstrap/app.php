<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\InitializeTenancyBySession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Ensure Livewire & all web requests initialize tenant from session
        $middleware->appendToGroup('web', InitializeTenancyBySession::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
