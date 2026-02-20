<?php
/**
 * Colour Scheme Variants for Sinople Theme
 *
 * Manages the data-scheme and data-theme attributes on <html>.
 * Supports NUJ Green colour scheme with light/dark mode.
 *
 * @package Sinople
 * @since 2.0.0
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get available colour schemes.
 *
 * @return array<string, array<string, string>>
 */
function sinople_get_colour_schemes(): array {
    return array(
        'nuj-green' => array(
            'label'   => __( 'NUJ Green', 'sinople' ),
            'primary' => '#006747',
        ),
    );
}

/**
 * Get the active colour scheme slug.
 *
 * @return string
 */
function sinople_get_active_scheme(): string {
    return get_theme_mod( 'sinople_colour_scheme', 'nuj-green' );
}

/**
 * Get the default dark mode preference.
 *
 * @return string 'light', 'dark', or 'auto'
 */
function sinople_get_default_theme_mode(): string {
    return get_theme_mod( 'sinople_default_theme_mode', 'auto' );
}

/**
 * Output data-scheme and data-theme attributes on <html>.
 *
 * The actual dark mode state is resolved client-side by dark-mode.js,
 * but we provide a default to avoid FOUC.
 *
 * @param string $output Existing language_attributes output.
 * @return string Modified output.
 */
function sinople_variant_attributes( string $output ): string {
    $scheme = esc_attr( sinople_get_active_scheme() );
    $default_mode = sinople_get_default_theme_mode();

    // Default to light unless admin has set dark as default
    $initial_theme = ( $default_mode === 'dark' ) ? 'dark' : 'light';

    $output .= ' data-scheme="' . $scheme . '"';
    $output .= ' data-theme="' . esc_attr( $initial_theme ) . '"';

    return $output;
}
add_filter( 'language_attributes', 'sinople_variant_attributes', 20 );

/**
 * Inline script to resolve dark mode preference before paint.
 *
 * Placed in <head> to prevent FOUC. Reads localStorage first,
 * then falls back to OS preference if theme default is 'auto'.
 */
function sinople_dark_mode_inline_script(): void {
    $default = sinople_get_default_theme_mode();
    ?>
    <script id="sinople-theme-resolver">
    (function(){
      var d = document.documentElement;
      var stored = null;
      try { stored = localStorage.getItem('sinople_theme'); } catch(e) {}
      if (stored === 'dark' || stored === 'light') {
        d.setAttribute('data-theme', stored);
      } else if (<?php echo $default === 'auto' ? 'true' : 'false'; ?>) {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
          d.setAttribute('data-theme', 'dark');
        }
      }
      /* Restore high contrast + font scale before paint */
      try {
        var c = localStorage.getItem('sinople_contrast');
        if (c === 'high') d.setAttribute('data-contrast', 'high');
        var f = localStorage.getItem('sinople_fontscale');
        if (f && ['small','normal','large','x-large'].indexOf(f) !== -1) d.setAttribute('data-fontscale', f);
      } catch(e2) {}
    })();
    </script>
    <?php
}
add_action( 'wp_head', 'sinople_dark_mode_inline_script', 1 );
