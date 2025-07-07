<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;

require_once __DIR__ . '/../app/Support/CustomAliases.php';
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append([
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                $status = 500;

                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    $status = 422;
                    $message = $e->validator->errors()->first();
                    $errors  = $e->errors();
                } elseif ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                    $status  = 404;
                    $message = 'Resource not found.';
                    $errors  = [];
                } elseif ($e instanceof AuthenticationException) {
                    $status = 401;
                    $message = 'Unauthenticated.';
                    $errors = [];
                } else {
                    $message = $e->getMessage();
                    $errors  = config('app.debug') ? $e->getTrace() : [];
                }
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'errors'  => $errors
                ], $status);
            }
        });
    })->create();
