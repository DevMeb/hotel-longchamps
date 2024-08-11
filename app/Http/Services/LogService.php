<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Log;

class LogService
{
    /**
     * Log an error message to a specific log file.
     *
     * @param string $message The main error message.
     * @param array $context Additional context or error details (optional).
     * @param string $channel The log channel to use (default: 'api').
     * @return void
     */
    public static function error(string $message, array $context = [], string $channel = 'api_errors'): void
    {
        Log::channel($channel)->error($message, $context);
    }

    /**
     * Log an informational message to a specific log file.
     *
     * @param string $message The informational message.
     * @param array $context Additional context or details (optional).
     * @param string $channel The log channel to use (default: 'api').
     * @return void
     */
    public static function info(string $message, array $context = [], string $channel = 'api_infos'): void
    {
        Log::channel($channel)->info($message, $context);
    }

    // You can add more methods for other log levels like warning, critical, etc.
}

