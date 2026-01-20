<?php

namespace App\Domain\Logger\Enumeration;

enum LogLevelEnum: string
{
    case DEBUG = 'debug';
    case INFO = 'info';
    case NOTICE = 'notice';
    case WARNING = 'warning';
    case ERROR = 'error';
    case CRITICAL = 'critical';
    case ALERT = 'alert';
    case EMERGENCY = 'emergency';

    public function label(): string
    {
        return match ($this) {
            self::DEBUG => 'Debug',
            self::INFO => 'Information',
            self::NOTICE => 'Notice',
            self::WARNING => 'Avertissement',
            self::ERROR => 'Erreur',
            self::CRITICAL => 'Critique',
            self::ALERT => 'Alerte',
            self::EMERGENCY => 'Urgence',
        };
    }

    public function isHighPriority(): bool
    {
        return in_array($this, [
            self::ERROR,
            self::CRITICAL,
            self::ALERT,
            self::EMERGENCY,
        ]);
    }
}
