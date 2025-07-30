<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class StructuredLogger
{
    /**
     * アプリケーションログを記録
     */
    public static function application(string $message, array $context = [], string $level = 'info'): void
    {
        $logData = [
            'message' => $message,
            'context' => $context,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'timestamp' => now()->toISOString(),
        ];

        Log::channel('application')->$level($message, $logData);
    }

    /**
     * エラーログを記録
     */
    public static function error(string $message, ?\Throwable $exception = null, array $context = []): void
    {
        $logData = [
            'message' => $message,
            'context' => $context,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'timestamp' => now()->toISOString(),
        ];

        if ($exception) {
            $logData['exception'] = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        }

        Log::channel('structured')->error($message, $logData);
    }

    /**
     * ユーザーアクションログを記録
     */
    public static function userAction(string $action, array $data = [], string $level = 'info'): void
    {
        $logData = [
            'action' => $action,
            'data' => $data,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'timestamp' => now()->toISOString(),
        ];

        Log::channel('structured')->$level("User action: {$action}", $logData);
    }

    /**
     * パフォーマンスログを記録
     */
    public static function performance(string $operation, float $duration, array $context = []): void
    {
        $logData = [
            'operation' => $operation,
            'duration_ms' => round($duration * 1000, 2),
            'context' => $context,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'ip_address' => request()->ip(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'timestamp' => now()->toISOString(),
        ];

        Log::channel('structured')->info("Performance: {$operation}", $logData);
    }

    /**
     * データベース操作ログを記録
     */
    public static function database(string $operation, string $table, array $data = [], ?float $duration = null): void
    {
        $logData = [
            'operation' => $operation,
            'table' => $table,
            'data' => $data,
            'duration_ms' => $duration ? round($duration * 1000, 2) : null,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'ip_address' => request()->ip(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'timestamp' => now()->toISOString(),
        ];

        Log::channel('structured')->info("Database: {$operation} on {$table}", $logData);
    }
} 