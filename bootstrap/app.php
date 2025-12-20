<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'not-banned' => \App\Http\Middleware\EnsureNotBanned::class,
        ]);

        $middleware->group('api', [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \App\Http\Middleware\TransformApiResponse::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $exception, \Illuminate\Http\Request $request) {
            if (!$request->expectsJson()) {
                return null;
            }

            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation error.',
                    'data' => null,
                    'errors' => $exception->errors(),
                ], 422);
            }

            if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized.',
                    'data' => null,
                    'errors' => null,
                ], 401);
            }

            if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden.',
                    'data' => null,
                    'errors' => null,
                ], 403);
            }

            if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException
                || $exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not found.',
                    'data' => null,
                    'errors' => null,
                ], 404);
            }

            if ($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Method not allowed.',
                    'data' => null,
                    'errors' => null,
                ], 405);
            }

            $message = config('app.debug') ? $exception->getMessage() : 'Internal server error.';

            return response()->json([
                'status' => 'error',
                'message' => $message,
                'data' => null,
                'errors' => null,
            ], 500);
        });
    })->create();
