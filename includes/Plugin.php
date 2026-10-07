<?php
/**
 * Main Plugin class.
 */

declare(strict_types=1);

namespace HetvegiKalandmento;

defined('ABSPATH') || exit;

class Plugin
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'registerRestRoutes']);
        add_action('init', [$this, 'registerShortcode']);
    }

    public function registerRestRoutes(): void
    {
        $provider = new ProgramProvider();
        $calculator = new ProgramStatusCalculator($provider->getReferenceTime());
        $restController = new RestController($provider, $calculator);

        $restController->registerRoutes();
    }

    public function registerShortcode(): void
    {
        add_shortcode('hetvegi_kalandmento', [$this, 'renderShortcode']);
    }

    public function renderShortcode(): string
    {
        wp_enqueue_script('hetvegi-kalandmento-frontend');
        wp_enqueue_style('hetvegi-kalandmento-frontend');

        return '<div class="hk-container">Programok betöltése...</div>';
    }
}