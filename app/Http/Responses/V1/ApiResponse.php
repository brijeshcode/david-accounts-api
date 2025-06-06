<?php

namespace App\Http\Responses\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiResponse
{
    public static function send(string $message = '', int $code = 200, mixed $result = []): JsonResponse
    {
        $response = [];

        if (!empty($message)) {
            $response['message'] = $message;
        }

        if (!empty($result)) {
            $response['data'] = $result;
        }

        return response()->json($response, $code);
    }

    public static function throw(mixed $errors = [], string $message = 'Something went wrong', int $code = 422): JsonResponse
    {
        
        $response = [
            'message' => $message,
            'errors' => $errors,
        ];

        Log::error('API Exception', [
            'status' => $code,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => now()->toDateTimeString(),
        ]);

        throw new HttpResponseException(response()->json($response, $code));

    }

    
    public static function index(string $message = '', mixed $result = []): JsonResponse
    {
        return self::send($message, 200, $result);
    }

    public static function store(string $message = 'Created successfully', mixed $result = []): JsonResponse
    {
        return self::send($message, 201, $result);
    }

    public static function update(string $message = 'Updated successfully'): JsonResponse
    {
        return self::send($message, 200);
    }

    public static function delete(string $message = 'Deleted successfully'): JsonResponse
    {
        return self::send($message, 204);
    }

    public static function notFound(string $message = 'Not found!'): JsonResponse
    {
        return self::send($message, 404);
    }

    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return self::send($message, 401);
    }

    public static function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return self::send($message, 403);
    }

    public static function serverError(string $message = 'Server error'): JsonResponse
    {
        return self::send($message, 500);
    }

    public static function failValidation(mixed $errors, string $message = 'Validation failed'): JsonResponse
    {
        return self::throw($errors, $message, 422);
    }

    public static function successMessage(string $message = 'Success'): JsonResponse
    {
        return self::send($message, 200);
    }

    public static function custom(string $message = '', int $code = 200, mixed $data = []): JsonResponse
    {
        return self::send($message, $code, $data);
    }
}
