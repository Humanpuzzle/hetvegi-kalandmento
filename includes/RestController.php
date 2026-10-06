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
        // Implementation will be added in later milestones
        return new \WP_REST_Response(['data' => []], 200);
    }
}