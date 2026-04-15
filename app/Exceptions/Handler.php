<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Shared\Exceptions\ExchangeRateNotFoundException;
use App\Shared\Exceptions\MaxActiveOffersException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = ['current_password', 'password', 'password_confirmation'];

    public function register(): void
    {
        $this->renderable(function (Throwable $e, $request) {
            if (!$request->expectsJson()) {
                return null;
            }

            return match (true) {
                $e instanceof ValidationException => response()->json([
                    'success' => false,
                    'message' => 'Error de validación.',
                    'errors'  => $e->errors(),
                ], 422),

                $e instanceof AuthenticationException => response()->json([
                    'success' => false,
                    'message' => 'No autenticado.',
                ], 401),

                $e instanceof NotFoundHttpException => response()->json([
                    'success' => false,
                    'message' => 'Recurso no encontrado.',
                ], 404),

                $e instanceof ExchangeRateNotFoundException => response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 404),

                $e instanceof MaxActiveOffersException => response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422),

                $e instanceof \DomainException,
                $e instanceof \InvalidArgumentException => response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422),

                default => null,
            };
        });
    }
}
