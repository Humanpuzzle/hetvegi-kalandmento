<?php
/**
 * Program Status Calculator class - calculates program status.
 */

declare(strict_types=1);

namespace HetvegiKalandmento;

use HetvegiKalandmento\Enums\ProgramStatus;

defined('ABSPATH') || exit;

class ProgramStatusCalculator
{
    public function __construct(
        private readonly string $referenceTime
    ) {}

    public function calculate(array $program): array
    {
        $status = $this->determineStatus($program);
        $remaining = $this->calculateRemaining($program);

        $program['status'] = $status->value;
        $program['status_label'] = $this->getStatusLabel($status);
        $program['bookable'] = $this->isBookable($status);
        $program['remaining'] = $remaining;

        return $program;
    }

    private function getStatusLabel(ProgramStatus $status): string
    {
        return match ($status) {
            ProgramStatus::AVAILABLE => 'Elérhető',
            ProgramStatus::LIMITED => 'Már csak néhány hely',
            ProgramStatus::FULL => 'Betelt',
            ProgramStatus::CANCELLED => 'Lemondva',
            ProgramStatus::NOT_BOOKABLE => 'Nem foglalható',
        };
    }

    private function determineStatus(array $program): ProgramStatus
    {
        // 1. cancelled === true
        if (($program['cancelled'] ?? false) === true) {
            return ProgramStatus::CANCELLED;
        }

        // 2. Critical booking data is invalid
        $capacity = (int)($program['capacity'] ?? 0);
        $booked = (int)($program['booked'] ?? 0);

        if ($capacity <= 0 || $booked < 0 || $booked > $capacity) {
            return ProgramStatus::NOT_BOOKABLE;
        }

        // 3. start_at <= reference_time (past event)
        $startAt = $program['start_at'] ?? null;
        if ($startAt === null || !is_string($startAt)) {
            return ProgramStatus::NOT_BOOKABLE;
        }

        try {
            $startDt = new \DateTime($startAt);
            $refDt = new \DateTime($this->referenceTime);
            if ($startDt <= $refDt) {
                return ProgramStatus::NOT_BOOKABLE;
            }
        } catch (\Exception) {
            return ProgramStatus::NOT_BOOKABLE;
        }

        // 4. booked >= capacity
        if ($booked >= $capacity) {
            return ProgramStatus::FULL;
        }

        // 5. remaining * 100 <= capacity * 20 (exact integer arithmetic)
        $remaining = $capacity - $booked;
        if ($remaining * 100 <= $capacity * 20) {
            return ProgramStatus::LIMITED;
        }

        // 6. otherwise
        return ProgramStatus::AVAILABLE;
    }

    private function calculateRemaining(array $program): int
    {
        $capacity = (int)($program['capacity'] ?? 0);
        $booked = (int)($program['booked'] ?? 0);

        if ($capacity <= 0 || $booked < 0) {
            return 0;
        }

        $remaining = $capacity - $booked;
        return $remaining > 0 ? $remaining : 0;
    }

    private function isBookable(ProgramStatus $status): bool
    {
        return match ($status) {
            ProgramStatus::AVAILABLE, ProgramStatus::LIMITED => true,
            default => false,
        };
    }
}