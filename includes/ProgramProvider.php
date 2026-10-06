<?php
/**
 * Program Provider class - loads program data from JSON.
 */

declare(strict_types=1);

namespace HetvegiKalandmento;

defined('ABSPATH') || exit;

class ProgramProvider
{
    private string $dataFile;

    public function __construct(?string $dataFile = null)
    {
        $this->dataFile = $dataFile ?? plugin_dir_path(__FILE__) . '../../data/programs.json';
    }

    public function getPrograms(): array
    {
        if (!file_exists($this->dataFile)) {
            return [];
        }

        $content = file_get_contents($this->dataFile);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }

        return $data['programs'] ?? [];
    }

    public function getReferenceTime(): ?string
    {
        if (!file_exists($this->dataFile)) {
            return null;
        }

        $content = file_get_contents($this->dataFile);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return $data['reference_time'] ?? null;
    }
}