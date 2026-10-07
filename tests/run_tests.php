<?php
/**
 * Simple test runner for ProgramStatusCalculator.
 * No external dependencies required.
 */

define('ABSPATH', true);

// Mock WordPress functions
function plugin_dir_path(string $file): string { return dirname($file) . '/'; }
function plugin_dir_url(string $file): string { return 'http://test.local/plugins/'; }
function add_action(string $hook, $callback, int $priority = 10, int $accepted_args = 1): bool { return true; }
function add_shortcode(string $tag, $callback): void {}
function wp_enqueue_script(string $handle, string $src = '', array $deps = [], $ver = false, bool $in_footer = false): void {}
function wp_enqueue_style(string $handle, string $src = '', array $deps = [], $ver = false, string $media = 'all'): void {}
function rest_url(string $path = ''): string { return 'http://test.local/wp-json/' . $path; }
function esc_url(string $url): string { return $url; }
function register_rest_route(string $namespace, string $route, array $args = []): bool { return true; }
function __return_true(): bool { return true; }

// The plugin is in the parent directory of the tests folder
$pluginDir = dirname(__DIR__);

require_once $pluginDir . '/hetvegi-kalandmento.php';

$calculator = new \HetvegiKalandmento\ProgramStatusCalculator('2026-10-09T12:00:00+02:00');

$tests = [
    'available' => function($c) {
        $p = ['id'=>1,'start_at'=>'2026-10-10T09:00:00+02:00','capacity'=>20,'booked'=>11,'cancelled'=>false];
        $r = $c->calculate($p);
        return $r['status'] === 'available' && $r['bookable'] === true;
    },
    'limited' => function($c) {
        $p = ['id'=>2,'start_at'=>'2026-10-10T17:00:00+02:00','capacity'=>12,'booked'=>10,'cancelled'=>false];
        $r = $c->calculate($p);
        return $r['status'] === 'limited' && $r['bookable'] === true;
    },
    'exact_20_percent_boundary' => function($c) {
        $p = ['id'=>7,'start_at'=>'2026-10-10T11:00:00+02:00','capacity'=>30,'booked'=>24,'cancelled'=>false];
        $r = $c->calculate($p);
        return $r['status'] === 'limited' && $r['remaining'] === 6 && $r['bookable'] === true;
    },
    'full' => function($c) {
        $p = ['id'=>3,'start_at'=>'2026-10-11T08:00:00+02:00','capacity'=>15,'booked'=>15,'cancelled'=>false];
        $r = $c->calculate($p);
        return $r['status'] === 'full' && $r['bookable'] === false;
    },
    'cancelled_precedence' => function($c) {
        $p = ['id'=>4,'start_at'=>'2026-10-11T10:00:00+02:00','capacity'=>25,'booked'=>8,'cancelled'=>true];
        $r = $c->calculate($p);
        return $r['status'] === 'cancelled' && $r['bookable'] === false;
    },
    'past_event' => function($c) {
        $p = ['id'=>6,'start_at'=>'2026-10-08T19:00:00+02:00','capacity'=>18,'booked'=>7,'cancelled'=>false];
        $r = $c->calculate($p);
        return $r['status'] === 'not_bookable' && $r['bookable'] === false;
    },
    'start_at_equals_reference_time' => function($c) {
        $p = ['id'=>99,'start_at'=>'2026-10-09T12:00:00+02:00','capacity'=>20,'booked'=>5,'cancelled'=>false];
        $r = $c->calculate($p);
        return $r['status'] === 'not_bookable' && $r['bookable'] === false;
    },
    'capacity_zero' => function($c) {
        $p = ['id'=>5,'start_at'=>'2026-10-11T13:00:00+02:00','capacity'=>0,'booked'=>2,'cancelled'=>false];
        $r = $c->calculate($p);
        return $r['status'] === 'not_bookable' && $r['bookable'] === false;
    },
    'booked_negative' => function($c) {
        $p = ['id'=>99,'start_at'=>'2026-10-10T09:00:00+02:00','capacity'=>20,'booked'=>-1,'cancelled'=>false];
        $r = $c->calculate($p);
        return $r['status'] === 'not_bookable' && $r['bookable'] === false;
    },
    'booked_exceeds_capacity' => function($c) {
        $p = ['id'=>99,'start_at'=>'2026-10-10T09:00:00+02:00','capacity'=>20,'booked'=>25,'cancelled'=>false];
        $r = $c->calculate($p);
        return $r['status'] === 'not_bookable' && $r['bookable'] === false;
    },
    'exact_reference_time_boundary' => function($c) {
        $p = ['id'=>99,'start_at'=>'2026-10-09T12:00:00+02:00','capacity'=>20,'booked'=>5,'cancelled'=>false];
        $r = $c->calculate($p);
        return $r['status'] === 'not_bookable' && $r['bookable'] === false;
    },
    'null_difficulty' => function($c) {
        $p = ['id'=>7,'start_at'=>'2026-10-10T11:00:00+02:00','capacity'=>30,'booked'=>24,'cancelled'=>false,'difficulty'=>null];
        $r = $c->calculate($p);
        return $r['status'] === 'limited';
    },
    'price_zero' => function($c) {
        $p = ['id'=>7,'start_at'=>'2026-10-10T11:00:00+02:00','capacity'=>30,'booked'=>24,'cancelled'=>false,'price_huf'=>0];
        $r = $c->calculate($p);
        return $r['status'] === 'limited';
    },
    'cancelled_precedence_over_other_conditions' => function($c) {
        $p = ['id'=>99,'start_at'=>'2026-10-10T09:00:00+02:00','capacity'=>0,'booked'=>2,'cancelled'=>true];
        $r = $c->calculate($p);
        return $r['status'] === 'cancelled' && $r['bookable'] === false;
    },
    'past_event_precedence_over_invalid_capacity' => function($c) {
        $p = ['id'=>99,'start_at'=>'2026-10-08T19:00:00+02:00','capacity'=>0,'booked'=>2,'cancelled'=>false];
        $r = $c->calculate($p);
        return $r['status'] === 'not_bookable' && $r['bookable'] === false;
    },
    'remaining_calculation' => function($c) {
        $p1 = ['id'=>1,'start_at'=>'2026-10-10T09:00:00+02:00','capacity'=>20,'booked'=>11,'cancelled'=>false];
        $r1 = $c->calculate($p1);
        $p2 = ['id'=>3,'start_at'=>'2026-10-11T08:00:00+02:00','capacity'=>15,'booked'=>15,'cancelled'=>false];
        $r2 = $c->calculate($p2);
        return $r1['remaining'] === 9 && $r2['remaining'] === 0;
    },
    'status_labels' => function($c) {
        $p = ['id'=>1,'start_at'=>'2026-10-10T09:00:00+02:00','capacity'=>20,'booked'=>11,'cancelled'=>false];
        $r = $c->calculate($p);
        return isset($r['status_label']) && is_string($r['status_label']) && $r['status_label'] !== '';
    },
];

$passed = 0;
$failed = 0;

foreach ($tests as $name => $test) {
    try {
        $result = $test($calculator);
        if ($result) {
            echo "✓ $name\n";
            $passed++;
        } else {
            echo "✗ $name\n";
            $failed++;
        }
    } catch (Throwable $e) {
        echo "✗ $name (exception: " . $e->getMessage() . ")\n";
        $failed++;
    }
}

echo "\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";
echo "Total: " . ($passed + $failed) . "\n";

if ($failed > 0) {
    exit(1);
}