<?php
/**
 * Program Provider class - loads program data from JSON.
 */

declare(strict_types=1);

namespace HetvegiKalandmento;

use RuntimeException;

defined('ABSPATH') || exit;

class ProgramProvider
{
    private string $dataFile;
    private ?array $parsedData = null;

    public function __construct(?string $dataFile = null)
    {
        $this->dataFile = $dataFile ?? plugin_dir_path(__FILE__) . '../../data/programs.json';
    }

    private function loadData(): array
    {
        if ($this->parsedData !== null) {
            return $this->parsedData;
        }

        if (!file_exists($this->dataFile)) {
            throw new RuntimeException('A program adatfájl nem található');
        }

        $content = file_get_contents($this->dataFile);
        if ($content === false) {
            throw new RuntimeException('Nem sikerült beolvasni a program adatfájlt');
        }

        try {
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (RuntimeException $e) {
            throw new RuntimeException('Érvénytelen JSON a program adatokban: ' . $e->getMessage(), 0, $e);
        }

        if (!isset($data['programs']) || !is_array($data['programs'])) {
            throw new RuntimeException('Hiányzó vagy érvénytelen "programs" a fájlban');
        }

        if (!isset($data['reference_time']) || !is_string($data['reference_time'])) {
            throw new RuntimeException('Hiányzó vagy érvénytelen "reference_time" a fájlban');
        }

        $this->parsedData = $data;
        return $data;
    }

    public function getPrograms(): array
    {
        return $this->loadData()['programs'];
    }

    public function getReferenceTime(): string
    {
        return $this->loadData()['reference_time'];
    }
}