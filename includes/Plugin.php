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
        add_action('wp_enqueue_scripts', [$this, 'registerAssets']);
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
        $shortcode = new Shortcode();
        add_shortcode('hetvegi_kalandmento', [$shortcode, 'render']);
    }

    public function registerAssets(): void
    {
        wp_register_script(
            'hetvegi-kalandmento-frontend',
            HK_PLUGIN_URL . 'assets/js/frontend.js',
            [],
            HK_VERSION,
            true
        );
        wp_register_style(
            'hetvegi-kalandmento-frontend',
            HK_PLUGIN_URL . 'assets/css/frontend.css',
            [],
            HK_VERSION
        );
        wp_localize_script('hetvegi-kalandmento-frontend', 'hkData', [
            'restUrl' => rest_url('hetvegi-kalandmento/v1/programs'),
        ]);
    }
}