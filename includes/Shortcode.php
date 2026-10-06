<?php
/**
 * Shortcode class - handles shortcode rendering.
 */

declare(strict_types=1);

namespace HetvegiKalandmento;

defined('ABSPATH') || exit;

class Shortcode
{
    public function render(): string
    {
        wp_enqueue_script('hetvegi-kalandmento-frontend');
        wp_enqueue_style('hetvegi-kalandmento-frontend');

        return '<div class="hk-container">Programok betöltése...</div>';
    }
}