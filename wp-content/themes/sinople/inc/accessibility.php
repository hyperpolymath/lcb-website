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
 * Determine whether high contrast mode is enabled.
 */
function sinople_accessibility_is_high_contrast_enabled(): bool {
    $cookie_value = isset( $_COOKIE['sinople_high_contrast'] ) ? sanitize_text_field( wp_unslash( (string) $_COOKIE['sinople_high_contrast'] ) ) : '0';
    return (bool) get_theme_mod( 'sinople_high_contrast_mode', false ) || '1' === $cookie_value;
}

/**
 * Body class hook for contrast mode.
 *
 * @param array<string> $classes Existing classes.
 * @return array<string>
 */
function sinople_accessibility_body_class( array $classes ): array {
    if ( sinople_accessibility_is_high_contrast_enabled() && ! in_array( 'high-contrast', $classes, true ) ) {
        $classes[] = 'high-contrast';
    }

    return $classes;
}
add_filter( 'body_class', 'sinople_accessibility_body_class', 20 );

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
 * Process contrast mode toggle query parameter and persist in cookie.
 */
function sinople_accessibility_capture_contrast_query(): void {
    if ( is_admin() || wp_doing_ajax() || ! isset( $_GET['sinople_contrast'] ) ) {
        return;
    }

    $mode = sanitize_key( (string) wp_unslash( $_GET['sinople_contrast'] ) );
    if ( ! in_array( $mode, array( 'high', 'normal' ), true ) ) {
        return;
    }

    $value = 'high' === $mode ? '1' : '0';
    $path = defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/';
    $domain = defined( 'COOKIE_DOMAIN' ) && COOKIE_DOMAIN ? COOKIE_DOMAIN : '';

    setcookie(
        'sinople_high_contrast',
        $value,
        array(
            'expires'  => time() + ( 180 * DAY_IN_SECONDS ),
            'path'     => $path,
            'domain'   => $domain,
            'secure'   => is_ssl(),
            'httponly' => false,
            'samesite' => 'Lax',
        )
    );

    $_COOKIE['sinople_high_contrast'] = $value;

    if ( ! headers_sent() ) {
        wp_safe_redirect( remove_query_arg( 'sinople_contrast' ) );
        exit;
    }
}
add_action( 'template_redirect', 'sinople_accessibility_capture_contrast_query' );

/**
 * Footer toggle for high contrast mode.
 */
function sinople_accessibility_render_contrast_toggle(): void {
    if ( is_admin() ) {
        return;
    }

    $is_high_contrast = sinople_accessibility_is_high_contrast_enabled();
    $next_mode = $is_high_contrast ? 'normal' : 'high';
    $label = $is_high_contrast
        ? __( 'Disable high contrast mode', 'sinople' )
        : __( 'Enable high contrast mode', 'sinople' );
    $url = esc_url( add_query_arg( 'sinople_contrast', $next_mode ) );
    ?>
    <p class="sinople-contrast-toggle-wrapper">
        <a class="sinople-contrast-toggle" href="<?php echo $url; ?>">
            <?php echo esc_html( $label ); ?>
        </a>
    </p>
    <?php
}
add_action( 'wp_footer', 'sinople_accessibility_render_contrast_toggle', 20 );

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
