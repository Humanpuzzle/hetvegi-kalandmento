<?php
/**
 * Plugin Name: Hétvégi Kalandmentő
 * Plugin URI: https://example.com/hetvegi-kalandmento
 * Description: Hétvégi programok megjelenítése és szűrése WordPress oldalon.
 * Version: 1.0.0
 * Author: Aktiv Magyarország
 * Author URI: https://example.com
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: hetvegi-kalandmento
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

define('HK_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HK_PLUGIN_URL', plugin_dir_url(__FILE__));
define('HK_VERSION', '1.0.0');

spl_autoload_register(function (string $class): void {
    $prefix = 'HetvegiKalandmento\\';
    $baseDirs = [
        HK_PLUGIN_DIR . 'includes/',
        HK_PLUGIN_DIR . 'enums/',
    ];

    if (str_starts_with($class, $prefix)) {
        $relativeClass = substr($class, strlen($prefix));
        foreach ($baseDirs as $baseDir) {
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

new \HetvegiKalandmento\Plugin();