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
        add_action('plugins_loaded', [$this, 'init']);
    }

    public function init(): void
    {
        $this->loadDependencies();
        $this->registerShortcode();
        $this->registerRestRoutes();
    }

    private function loadDependencies(): void
    {
        // Dependencies loaded via require_once in bootstrap
    }

    private function registerShortcode(): void
    {
        add_shortcode('hetvegi_kalandmento', [$this, 'renderShortcode']);
    }

    private function registerRestRoutes(): void
    {
        add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    public function registerRoutes(): void
    {
        // REST routes will be registered here
    }

    public function renderShortcode(): string
    {
        wp_enqueue_script('hetvegi-kalandmento-frontend');
        wp_enqueue_style('hetvegi-kalandmento-frontend');

        return '<div class="hk-container">Programok betöltése...</div>';
    }
}