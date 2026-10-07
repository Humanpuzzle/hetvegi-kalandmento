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

        $restUrl = esc_url(rest_url('hetvegi-kalandmento/v1/programs'));

        return <<<HTML
<div class="hk-container" data-rest-url="{$restUrl}">
  <div class="hk-loading" aria-live="polite">
    <p>Programok betöltése…</p>
  </div>

  <div class="hk-error" hidden aria-live="assertive">
    <p>A programok betöltése sikertelen. Kérjük, próbálja meg később.</p>
  </div>

  <form class="hk-filters" aria-label="Programok szűrése">
    <div class="hk-filter-group">
      <label for="hk-difficulty-filter">Nehézség</label>
      <select id="hk-difficulty-filter" name="difficulty">
        <option value="">Mindent</option>
        <option value="könnyű">Könnyű</option>
        <option value="közepes">Közepes</option>
        <option value="nehéz">Nehéz</option>
        <option value="null">Nincs megadva</option>
      </select>
    </div>
  </form>

  <div class="hk-programs" hidden>
    <ul class="hk-program-list" role="list"></ul>
    <p class="hk-empty" hidden>Nincs a feltételeknek megfelelő program.</p>
  </div>
</div>
HTML;
    }
}