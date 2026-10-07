<?php
/**
 * REST Controller class - registers API endpoints.
 */

declare(strict_types=1);

namespace HetvegiKalandmento;

defined('ABSPATH') || exit;

class RestController
{
    public function __construct(
        private readonly ProgramProvider $provider,
        private readonly ProgramStatusCalculator $calculator
    ) {}

    public function registerRoutes(): void
    {
        register_rest_route('hetvegi-kalandmento/v1', '/programs', [
            'methods' => 'GET',
            'callback' => [$this, 'getPrograms'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function getPrograms(\WP_REST_Request $request): \WP_REST_Response
    {
        try {
            $programs = $this->provider->getPrograms();
            $referenceTime = $this->provider->getReferenceTime();
        } catch (\RuntimeException $e) {
            return new \WP_Error(
                'hk_data_source_error',
                'Program adatforrás hiba: ' . $e->getMessage(),
                ['status' => 500]
            );
        }

        $enriched = [];
        foreach ($programs as $program) {
            $enriched[] = $this->calculator->calculate($program);
        }

        $sorted = $this->sortPrograms($enriched);

        $responseData = array_map(fn($p) => $this->normalizeResponse($p), $sorted);

        return new \WP_REST_Response(['data' => $responseData], 200);
    }

    private function sortPrograms(array $programs): array
    {
        usort($programs, function (array $a, array $b): int {
            $aBookable = $a['bookable'] ?? false;
            $bBookable = $b['bookable'] ?? false;

            if ($aBookable !== $bBookable) {
                return $aBookable ? -1 : 1;
            }

            $aTime = $a['start_at'] ?? '';
            $bTime = $b['start_at'] ?? '';
            return strcmp($aTime, $bTime);
        });

        return $programs;
    }

    private function normalizeResponse(array $program): array
    {
        return [
            'id' => (int)($program['id'] ?? 0),
            'title' => (string)($program['title'] ?? ''),
            'location' => (string)($program['location'] ?? ''),
            'start_at' => (string)($program['start_at'] ?? ''),
            'capacity' => (int)($program['capacity'] ?? 0),
            'booked' => (int)($program['booked'] ?? 0),
            'remaining' => (int)($program['remaining'] ?? 0),
            'difficulty' => $program['difficulty'] ?? null,
            'price_huf' => (int)($program['price_huf'] ?? 0),
            'cancelled' => (bool)($program['cancelled'] ?? false),
            'status' => (string)($program['status'] ?? 'not_bookable'),
            'status_label' => (string)($program['status_label'] ?? 'Nem foglalható'),
            'bookable' => (bool)($program['bookable'] ?? false),
        ];
    }
}