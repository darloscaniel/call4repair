<?php

namespace App\Enums;

/**
 * Call lifecycle status plus the allowed transitions between states.
 * Keeping the state machine here (instead of free-form strings) prevents
 * invalid jumps such as done -> open.
 */
enum CallStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Done = 'done';
    case Rejected = 'rejected';

    /**
     * All status values, e.g. for validation `in:` rules.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Statuses this one may move to (besides staying unchanged).
     *
     * @return array<int, self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Open       => [self::InProgress, self::Rejected],
            self::InProgress => [self::Done, self::Rejected],
            self::Done       => [],
            self::Rejected   => [self::Open], // allow reopening a rejected call
        };
    }

    /**
     * Whether moving to $target is allowed. Staying on the same status is
     * always allowed (e.g. when only reassigning employees).
     */
    public function canTransitionTo(self $target): bool
    {
        return $target === $this || in_array($target, $this->allowedTransitions(), true);
    }
}
