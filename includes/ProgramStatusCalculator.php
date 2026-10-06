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
        // Business logic will be implemented in later milestones
        // For now, return program with default status
        $program['status'] = ProgramStatus::AVAILABLE->value;
        $program['status_label'] = 'Elérhető';
        $program['bookable'] = true;

        return $program;
    }
}