#!/usr/bin/env php
<?php
/**
 * Asset build script for Sinople theme.
 *
 * Concatenates and minifies CSS/JS files into production bundles.
 * No Node/npm required — pure PHP minification.
 *
 * Usage: php scripts/build-assets.php
 *
 * SPDX-License-Identifier: GPL-2.0-or-later
 *
 * @package Sinople
 */

$theme_dir = dirname( __DIR__ ) . '/wp-content/themes/sinople';
$css_dir   = $theme_dir . '/assets/css';
$js_dir    = $theme_dir . '/assets/js';
$dist_dir  = $theme_dir . '/assets/dist';

if ( ! is_dir( $dist_dir ) ) {
    mkdir( $dist_dir, 0755, true );
}

/**
 * Minify CSS by stripping comments, collapsing whitespace, and removing
 * unnecessary characters.
 */
function minify_css( string $css ): string {
    // Strip block comments (but keep /*! ... */ licence comments).
    $css = preg_replace( '#/\*(?!!).*?\*/#s', '', $css );
    // Strip line-leading/trailing whitespace.
    $css = preg_replace( '/^\s+/m', '', $css );
    // Collapse multiple whitespace to single space.
    $css = preg_replace( '/\s{2,}/', ' ', $css );
    // Remove space around : ; { } , > ~ +
    $css = preg_replace( '/\s*([:{};,>~+])\s*/', '$1', $css );
    // Remove trailing semicolons before closing braces.
    $css = str_replace( ';}', '}', $css );
    // Remove empty rules.
    $css = preg_replace( '/[^{}]+\{\s*\}/', '', $css );
    return trim( $css );
}

/**
 * Minify JS by stripping single-line comments (not in strings),
 * collapsing whitespace, and removing blank lines.
 */
function minify_js( string $js ): string {
    // Strip single-line comments (// ...) that are not inside strings.
    // Use a conservative approach: only strip lines that start with //.
    $js = preg_replace( '#^\s*//[^\n]*$#m', '', $js );
    // Strip block comments (but keep /*! ... */ licence comments).
    $js = preg_replace( '#/\*(?!!).*?\*/#s', '', $js );
    // Collapse multiple blank lines.
    $js = preg_replace( '/\n{3,}/', "\n\n", $js );
    // Strip leading whitespace per line (but keep single spaces).
    $js = preg_replace( '/^[ \t]+/m', '', $js );
    // Collapse multiple spaces (not newlines) to single.
    $js = preg_replace( '/[ \t]{2,}/', ' ', $js );
    return trim( $js );
}

// ============================================================================
// CSS BUNDLE — order matters (variables first, then base, then components)
// ============================================================================

$css_files = [
    // 1. Tokens & base
    "$css_dir/variables.css",
    "$css_dir/fonts.css",
    "$theme_dir/style.css",
    // 2. Layout
    "$css_dir/layout.css",
    "$css_dir/grid.css",
    // 3. Components
    "$css_dir/header.css",
    "$css_dir/offcanvas.css",
    "$css_dir/components.css",
    "$css_dir/cards.css",
    "$css_dir/sidebar.css",
    "$css_dir/footer.css",
    "$css_dir/search-modal.css",
    // 4. Feature CSS (included unconditionally in bundle)
    "$css_dir/featured.css",
    // 5. Overrides
    "$css_dir/dark-mode.css",
    "$css_dir/accessibility.css",
];

// Separate: print.css stays separate (media="print")
// Separate: vendor CSS (Swiper, Font Awesome) stays separate (different versioning)

$css_bundle = "/*! Sinople Theme v2.0.0 | GPL-2.0-or-later */\n";
$css_raw_size = 0;

foreach ( $css_files as $file ) {
    if ( ! file_exists( $file ) ) {
        fprintf( STDERR, "WARNING: Missing CSS file: %s\n", $file );
        continue;
    }
    $content = file_get_contents( $file );
    $css_raw_size += strlen( $content );
    $css_bundle .= $content . "\n";
}

$css_minified = minify_css( $css_bundle );
file_put_contents( "$dist_dir/sinople.min.css", $css_minified );

// ============================================================================
// JS BUNDLE — order matters (dark mode first for early execution)
// ============================================================================

$js_files = [
    "$js_dir/dark-mode.js",
    "$js_dir/offcanvas.js",
    "$js_dir/search-modal.js",
    "$js_dir/navigation.js",
];

// Separate: swiper-init.js stays separate (depends on Swiper vendor, front-page only)
// Separate: vendor JS (Swiper) stays separate (different versioning)

$js_bundle = "/*! Sinople Theme v2.0.0 | GPL-2.0-or-later */\n";
$js_raw_size = 0;

foreach ( $js_files as $file ) {
    if ( ! file_exists( $file ) ) {
        fprintf( STDERR, "WARNING: Missing JS file: %s\n", $file );
        continue;
    }
    $content = file_get_contents( $file );
    $js_raw_size += strlen( $content );
    $js_bundle .= ";(function(){\n" . $content . "\n})();\n";
}

$js_minified = minify_js( $js_bundle );
file_put_contents( "$dist_dir/sinople.min.js", $js_minified );

// ============================================================================
// Report
// ============================================================================

$css_min_size = strlen( $css_minified );
$js_min_size  = strlen( $js_minified );

printf( "CSS: %d files → sinople.min.css (%s → %s, %.0f%% reduction)\n",
    count( $css_files ),
    format_bytes( $css_raw_size ),
    format_bytes( $css_min_size ),
    ( 1 - $css_min_size / $css_raw_size ) * 100
);
printf( "JS:  %d files → sinople.min.js  (%s → %s, %.0f%% reduction)\n",
    count( $js_files ),
    format_bytes( $js_raw_size ),
    format_bytes( $js_min_size ),
    ( 1 - $js_min_size / $js_raw_size ) * 100
);

function format_bytes( int $bytes ): string {
    if ( $bytes < 1024 ) {
        return $bytes . 'B';
    }
    return round( $bytes / 1024, 1 ) . 'KB';
}
