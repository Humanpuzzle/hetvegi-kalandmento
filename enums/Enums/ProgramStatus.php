<?php
/**
 * ProgramStatus enum.
 */

declare(strict_types=1);

namespace HetvegiKalandmento\Enums;

defined('ABSPATH') || exit;

enum ProgramStatus: string
{
    case AVAILABLE = 'available';
    case LIMITED = 'limited';
    case FULL = 'full';
    case CANCELLED = 'cancelled';
    case NOT_BOOKABLE = 'not_bookable';
}