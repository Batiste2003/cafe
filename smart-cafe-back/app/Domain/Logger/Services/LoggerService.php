<?php

namespace App\Domain\Logger\Services;

use App\Domain\Logger\Enumeration\LogLevelEnum;
use Illuminate\Support\Facades\Log;

class LoggerService
{
    public function log(LogLevelEnum $level, string $message, ?string $errorCode = null, array $context = []): void
    {
        $logContext = $this->buildContext($errorCode, $context);

        match ($level) {
            LogLevelEnum::DEBUG => Log::debug($message, $logContext),
            LogLevelEnum::INFO => Log::info($message, $logContext),
            LogLevelEnum::NOTICE => Log::notice($message, $logContext),
            LogLevelEnum::WARNING => Log::warning($message, $logContext),
            LogLevelEnum::ERROR => Log::error($message, $logContext),
            LogLevelEnum::CRITICAL => Log::critical($message, $logContext),
            LogLevelEnum::ALERT => Log::alert($message, $logContext),
            LogLevelEnum::EMERGENCY => Log::emergency($message, $logContext),
        };
    }

    public function debug(string $message, ?string $errorCode = null, array $context = []): void
    {
        $this->log(LogLevelEnum::DEBUG, $message, $errorCode, $context);
    }

    public function info(string $message, ?string $errorCode = null, array $context = []): void
    {
        $this->log(LogLevelEnum::INFO, $message, $errorCode, $context);
    }

    public function notice(string $message, ?string $errorCode = null, array $context = []): void
    {
        $this->log(LogLevelEnum::NOTICE, $message, $errorCode, $context);
    }

    public function warning(string $message, ?string $errorCode = null, array $context = []): void
    {
        $this->log(LogLevelEnum::WARNING, $message, $errorCode, $context);
    }

    public function error(string $message, ?string $errorCode = null, array $context = []): void
    {
        $this->log(LogLevelEnum::ERROR, $message, $errorCode, $context);
    }

    public function critical(string $message, ?string $errorCode = null, array $context = []): void
    {
        $this->log(LogLevelEnum::CRITICAL, $message, $errorCode, $context);
    }

    public function alert(string $message, ?string $errorCode = null, array $context = []): void
    {
        $this->log(LogLevelEnum::ALERT, $message, $errorCode, $context);
    }

    public function emergency(string $message, ?string $errorCode = null, array $context = []): void
    {
        $this->log(LogLevelEnum::EMERGENCY, $message, $errorCode, $context);
    }

    private function buildContext(?string $errorCode, array $context): array
    {
        if ($errorCode !== null) {
            $context['error_code'] = $errorCode;
        }

        return $context;
    }
}
