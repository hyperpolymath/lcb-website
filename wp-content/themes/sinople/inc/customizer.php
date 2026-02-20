<?php
/**
 * Theme Customizer for Sinople Theme
 *
 * Colour scheme, dark mode, social links, ticker, featured count.
 *
 * @package Sinople
 * @since 2.0.0
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function sinople_customize_register( WP_Customize_Manager $wp_customize ) {

    /* ------------------------------------------------------------------ */
    /*  Theme Options Panel                                               */
    /* ------------------------------------------------------------------ */
    $wp_customize->add_panel( 'sinople_options', array(
        'title'    => __( 'Sinople Theme Options', 'sinople' ),
        'priority' => 30,
    ) );

    /* ------------------------------------------------------------------ */
    /*  Appearance Section                                                */
    /* ------------------------------------------------------------------ */
    $wp_customize->add_section( 'sinople_appearance', array(
        'title' => __( 'Appearance', 'sinople' ),
        'panel' => 'sinople_options',
    ) );

    // Colour scheme
    $wp_customize->add_setting( 'sinople_colour_scheme', array(
        'default'           => 'nuj-green',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'sinople_colour_scheme', array(
        'label'   => __( 'Colour Scheme', 'sinople' ),
        'section' => 'sinople_appearance',
        'type'    => 'select',
        'choices' => array(
            'nuj-green' => __( 'NUJ Green', 'sinople' ),
        ),
    ) );

    // Default theme mode
    $wp_customize->add_setting( 'sinople_default_theme_mode', array(
        'default'           => 'auto',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'sinople_default_theme_mode', array(
        'label'   => __( 'Default Theme Mode', 'sinople' ),
        'section' => 'sinople_appearance',
        'type'    => 'radio',
        'choices' => array(
            'light' => __( 'Always Light', 'sinople' ),
            'dark'  => __( 'Always Dark', 'sinople' ),
            'auto'  => __( 'Follow OS Preference', 'sinople' ),
        ),
    ) );

    // High contrast
    $wp_customize->add_setting( 'sinople_high_contrast_mode', array(
        'default'   => false,
        'transport' => 'refresh',
    ) );

    /* ------------------------------------------------------------------ */
    /*  Homepage Section                                                  */
    /* ------------------------------------------------------------------ */
    $wp_customize->add_section( 'sinople_homepage', array(
        'title' => __( 'Homepage', 'sinople' ),
        'panel' => 'sinople_options',
    ) );

    // Show ticker
    $wp_customize->add_setting( 'sinople_show_ticker', array(
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'sinople_show_ticker', array(
        'label'   => __( 'Show Breaking News Ticker', 'sinople' ),
        'section' => 'sinople_homepage',
        'type'    => 'checkbox',
    ) );

    // Ticker label
    $wp_customize->add_setting( 'sinople_ticker_label', array(
        'default'           => __( 'Breaking News', 'sinople' ),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'sinople_ticker_label', array(
        'label'   => __( 'Ticker Label', 'sinople' ),
        'section' => 'sinople_homepage',
        'type'    => 'text',
    ) );

    // Ticker count
    $wp_customize->add_setting( 'sinople_ticker_count', array(
        'default'           => 6,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'sinople_ticker_count', array(
        'label'       => __( 'Number of Ticker Posts', 'sinople' ),
        'section'     => 'sinople_homepage',
        'type'        => 'number',
        'input_attrs' => array( 'min' => 1, 'max' => 20 ),
    ) );

    // Featured count
    $wp_customize->add_setting( 'sinople_featured_count', array(
        'default'           => 5,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'sinople_featured_count', array(
        'label'       => __( 'Number of Featured Posts', 'sinople' ),
        'section'     => 'sinople_homepage',
        'type'        => 'number',
        'input_attrs' => array( 'min' => 1, 'max' => 10 ),
    ) );

    // Missed posts count
    $wp_customize->add_setting( 'sinople_missed_posts_count', array(
        'default'           => 4,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'sinople_missed_posts_count', array(
        'label'       => __( 'Number of "You May Have Missed" Posts', 'sinople' ),
        'section'     => 'sinople_homepage',
        'type'        => 'number',
        'input_attrs' => array( 'min' => 0, 'max' => 8 ),
    ) );

    /* ------------------------------------------------------------------ */
    /*  Social Links Section                                              */
    /* ------------------------------------------------------------------ */
    $wp_customize->add_section( 'sinople_social', array(
        'title' => __( 'Social Links', 'sinople' ),
        'panel' => 'sinople_options',
    ) );

    $social_fields = array(
        'twitter'   => __( 'X (Twitter) URL', 'sinople' ),
        'facebook'  => __( 'Facebook URL', 'sinople' ),
        'instagram' => __( 'Instagram URL', 'sinople' ),
        'mastodon'  => __( 'Mastodon URL', 'sinople' ),
        'youtube'   => __( 'YouTube URL', 'sinople' ),
        'linkedin'  => __( 'LinkedIn URL', 'sinople' ),
    );

    foreach ( $social_fields as $slug => $label ) {
        $wp_customize->add_setting( 'sinople_social_' . $slug, array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'sinople_social_' . $slug, array(
            'label'   => $label,
            'section' => 'sinople_social',
            'type'    => 'url',
        ) );
    }
}
add_action( 'customize_register', 'sinople_customize_register' );
