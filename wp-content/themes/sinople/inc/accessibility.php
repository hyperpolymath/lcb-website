<?php
/**
 * Accessibility Features for Sinople Theme
 *
 * WCAG 2.3 AAA compliance utilities
 *
 * @package Sinople
 * @since 1.0.0
 */

declare(strict_types=1);

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * High contrast mode is now managed client-side via the accessibility
 * toolbar (a11y-toolbar.js) using data-contrast="high" on <html>
 * and persisted to localStorage. The old cookie-based approach is removed.
 */

/**
 * Skip links for keyboard and screen-reader users.
 */
function sinople_accessibility_skip_links(): void {
    ?>
    <a class="skip-link sr-only" href="#main"><?php esc_html_e( 'Skip to main content', 'sinople' ); ?></a>
    <a class="skip-link sr-only" href="#nav"><?php esc_html_e( 'Skip to navigation', 'sinople' ); ?></a>
    <?php
}
add_action( 'wp_body_open', 'sinople_accessibility_skip_links', 5 );

/**
 * Add aria-current and fallback labels to menu links.
 *
 * @param array<string, string> $atts Link attributes.
 * @param WP_Post               $item Menu item.
 * @param stdClass              $args Menu args.
 * @param int                   $depth Menu depth.
 * @return array<string, string>
 */
function sinople_accessibility_nav_menu_link_attributes( array $atts, WP_Post $item, stdClass $args, int $depth ): array {
    if ( ! empty( $item->current ) ) {
        $atts['aria-current'] = 'page';
    }

    if ( ! isset( $atts['aria-label'] ) && ! empty( $item->title ) ) {
        $atts['aria-label'] = wp_strip_all_tags( (string) $item->title );
    }

    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'sinople_accessibility_nav_menu_link_attributes', 10, 4 );

/**
 * Contrast and font-scale toggles are now handled entirely client-side
 * by the accessibility toolbar (a11y-toolbar.js). No server-side
 * cookie or query-parameter processing is needed.
 */

/**
 * Add a small focus-management enhancement for navigation.
 */
function sinople_accessibility_focus_script(): void {
    if ( is_admin() ) {
        return;
    }
    ?>
    <script id="sinople-accessibility-focus">
    (function () {
        var nav = document.querySelector(".main-navigation");
        if (!nav) return;

        var toggle = nav.querySelector(".menu-toggle");
        var links = nav.querySelectorAll("a");
        if (!toggle || !links.length) return;

        toggle.addEventListener("click", function () {
            if (this.getAttribute("aria-expanded") === "true") {
                links[0].focus();
            }
        });

        nav.addEventListener("keydown", function (event) {
            if (event.key !== "Escape") return;
            nav.classList.remove("toggled");
            toggle.setAttribute("aria-expanded", "false");
            toggle.focus();
        });
    })();
    </script>
    <?php
}
add_action( 'wp_footer', 'sinople_accessibility_focus_script', 30 );

/**
 * Screen-reader helper span.
 */
function sinople_sr_text( string $text ): string {
    return '<span class="screen-reader-text">' . esc_html( $text ) . '</span>';
}
