<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Алиасы middleware, необходимые по спецификации
        $middleware->alias([
            'auth' => Authenticate::class,
            'verified' => EnsureEmailIsVerified::class,
            'throttle' => ThrottleRequests::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'super_admin' => \App\Http\Middleware\SuperAdmin::class,
        ]);

        // Добавляем защитные и служебные middleware ко всем web маршрутам
        $middleware->web(append: [
            \App\Http\Middleware\SanitizeInput::class,
            \App\Http\Middleware\TrackImpersonation::class,
            \App\Http\Middleware\CaptureReferralParam::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Обработка expired session для редиректа на страницу логина
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->guest(route('login'));
        });

        // Обработка истекшей сессии (419)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() === 419) {
                return redirect()->route('login')->withErrors(['session' => 'Your session has expired. Please login again.']);
            }
        });
    })->create();
