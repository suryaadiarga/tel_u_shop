<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     */
    public function report(Throwable $exception): void
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            if ($exception instanceof ValidationException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation error.',
                    'data' => null,
                    'errors' => $exception->errors(),
                ], 422);
            }

            if ($exception instanceof AuthenticationException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized.',
                    'data' => null,
                    'errors' => null,
                ], 401);
            }

            if ($exception instanceof AuthorizationException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden.',
                    'data' => null,
                    'errors' => null,
                ], 403);
            }

            if ($exception instanceof ModelNotFoundException || $exception instanceof NotFoundHttpException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not found.',
                    'data' => null,
                    'errors' => null,
                ], 404);
            }

            if ($exception instanceof MethodNotAllowedHttpException) {
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
        }

        return parent::render($request, $exception);
    }
}
