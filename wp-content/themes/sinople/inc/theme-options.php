<?php
/**
 * Theme Options for Sinople Theme
 *
 * Ticker settings, featured post count, social links.
 *
 * @package Sinople
 * @since 2.0.0
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get the number of featured posts to display.
 *
 * @return int
 */
function sinople_get_featured_count(): int {
    return (int) get_theme_mod( 'sinople_featured_count', 5 );
}

/**
 * Get ticker/breaking-news label text.
 *
 * @return string
 */
function sinople_get_ticker_label(): string {
    return get_theme_mod( 'sinople_ticker_label', __( 'Breaking News', 'sinople' ) );
}

/**
 * Get the number of ticker posts.
 *
 * @return int
 */
function sinople_get_ticker_count(): int {
    return (int) get_theme_mod( 'sinople_ticker_count', 6 );
}

/**
 * Whether to show the ticker on the front page.
 *
 * @return bool
 */
function sinople_show_ticker(): bool {
    return (bool) get_theme_mod( 'sinople_show_ticker', true );
}

/**
 * Get social media links.
 *
 * @return array<string, string> Slug => URL pairs.
 */
function sinople_get_social_links(): array {
    $defaults = array(
        'twitter'   => '',
        'facebook'  => '',
        'instagram' => '',
        'mastodon'  => '',
        'rss'       => get_bloginfo( 'rss2_url' ),
    );

    $links = array();
    foreach ( $defaults as $slug => $default ) {
        $url = get_theme_mod( 'sinople_social_' . $slug, $default );
        if ( ! empty( $url ) ) {
            $links[ $slug ] = $url;
        }
    }

    return $links;
}

/**
 * Get Font Awesome icon class for a social network.
 *
 * @param string $slug Social network slug.
 * @return string Icon class.
 */
function sinople_social_icon( string $slug ): string {
    $icons = array(
        'twitter'   => 'fa-brands fa-x-twitter',
        'facebook'  => 'fa-brands fa-facebook-f',
        'instagram' => 'fa-brands fa-instagram',
        'mastodon'  => 'fa-brands fa-mastodon',
        'rss'       => 'fa-solid fa-rss',
        'youtube'   => 'fa-brands fa-youtube',
        'linkedin'  => 'fa-brands fa-linkedin-in',
    );
    return $icons[ $slug ] ?? 'fa-solid fa-link';
}

/**
 * Get the number of "You may have missed" posts.
 *
 * @return int
 */
function sinople_get_missed_posts_count(): int {
    return (int) get_theme_mod( 'sinople_missed_posts_count', 4 );
}
