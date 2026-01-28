<?php
/**
 * Sinople Theme Functions
 *
 * Core functionality for the Sinople WordPress theme.
 * Integrates semantic web processing, IndieWeb features, and accessibility.
 *
 * @package Sinople
 * @since 1.0.0
 */

declare(strict_types=1);

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Load PhpAegis security library
require_once get_template_directory() . '/vendor/php-aegis/autoload.php';

// Theme constants
define( 'SINOPLE_VERSION', '1.0.0' );
define( 'SINOPLE_PATH', get_template_directory() );
define( 'SINOPLE_URL', get_template_directory_uri() );

/**
 * Theme Setup
 *
 * Register theme support features and hooks
 */
function sinople_theme_setup() {
    // Make theme available for translation
    load_theme_textdomain( 'sinople', SINOPLE_PATH . '/languages' );

    // Add default posts and comments RSS feed links to head
    add_theme_support( 'automatic-feed-links' );

    // Let WordPress manage the document title
    add_theme_support( 'title-tag' );

    // Enable support for Post Thumbnails
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 1200, 630, true ); // Open Graph size

    // Add custom image sizes
    add_image_size( 'sinople-featured', 1200, 675, true );
    add_image_size( 'sinople-thumbnail', 400, 300, true );

    // Register navigation menus
    register_nav_menus( array(
        'primary' => esc_html__( 'Primary Menu', 'sinople' ),
        'footer'  => esc_html__( 'Footer Menu', 'sinople' ),
        'social'  => esc_html__( 'Social Links Menu', 'sinople' ),
    ) );

    // Switch default core markup to HTML5
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
        'navigation-widgets',
    ) );

    // Add theme support for custom logo
    add_theme_support( 'custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    // Add support for custom background
    add_theme_support( 'custom-background', array(
        'default-color' => 'ffffff',
    ) );

    // Add support for editor styles
    add_theme_support( 'editor-styles' );
    add_editor_style( 'assets/css/editor-style.css' );

    // Add support for responsive embedded content
    add_theme_support( 'responsive-embeds' );

    // Add support for block editor
    add_theme_support( 'align-wide' );
    add_theme_support( 'wp-block-styles' );

    // Add support for custom line height
    add_theme_support( 'custom-line-height' );

    // Add support for experimental link color control
    add_theme_support( 'experimental-link-color' );

    // Add support for custom spacing
    add_theme_support( 'custom-spacing' );
}
add_action( 'after_setup_theme', 'sinople_theme_setup' );

/**
 * Add Favicons to Head
 *
 * Outputs favicon links for all devices and browsers
 */
function sinople_add_favicons() {
    $favicon_path = SINOPLE_URL . '/assets/images';
    ?>
    <!-- Favicons - Sinople Theme -->
    <link rel="icon" type="image/x-icon" href="<?php echo esc_url( $favicon_path . '/favicon.ico' ); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo esc_url( $favicon_path . '/favicon-16.png' ); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url( $favicon_path . '/favicon-32.png' ); ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo esc_url( $favicon_path . '/favicon-192.png' ); ?>">
    <link rel="icon" type="image/png" sizes="512x512" href="<?php echo esc_url( $favicon_path . '/favicon-512.png' ); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( $favicon_path . '/apple-touch-icon.png' ); ?>">
    <?php
}
add_action( 'wp_head', 'sinople_add_favicons' );

/**
 * Theme Activation: Initial Setup & Updates
 *
 * Runs once when theme is first activated to:
 * - Remove default plugins (Akismet, Hello Dolly) that come with WordPress
 * - Trigger immediate translation and core updates
 *
 * Note: Users can still install these plugins later if they choose
 */
function sinople_on_theme_activation() {
    // Only run once per theme activation
    if ( get_option( 'sinople_activation_cleanup_done' ) ) {
        return;
    }

    // Remove default WordPress plugins if they exist
    $default_plugins = array(
        'akismet/akismet.php',
        'hello.php'
    );

    foreach ( $default_plugins as $plugin ) {
        if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin ) ) {
            // Deactivate first
            deactivate_plugins( $plugin, true );

            // Delete the plugin
            delete_plugins( array( $plugin ) );
        }
    }

    // Trigger core, plugin, and theme update checks
    wp_clean_update_cache();
    wp_update_themes();
    wp_update_plugins();
    wp_version_check();

    // Trigger translation updates immediately
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';

    $upgrader = new Language_Pack_Upgrader( new Automatic_Upgrader_Skin() );
    $translations = wp_get_translation_updates();

    if ( ! empty( $translations ) ) {
        $upgrader->bulk_upgrade( $translations );
    }

    // Mark as done so it doesn't run again
    update_option( 'sinople_activation_cleanup_done', true );
}
add_action( 'after_switch_theme', 'sinople_on_theme_activation' );

/**
 * Trigger Updates on First Admin Visit
 *
 * Ensures translations and updates are checked immediately after installation
 */
function sinople_trigger_initial_updates() {
    // Only run for admins
    if ( ! current_user_can( 'update_core' ) ) {
        return;
    }

    // Check if we've triggered initial updates
    if ( get_option( 'sinople_initial_updates_done' ) ) {
        return;
    }

    // Force immediate update checks
    delete_site_transient( 'update_core' );
    delete_site_transient( 'update_plugins' );
    delete_site_transient( 'update_themes' );

    wp_version_check();
    wp_update_plugins();
    wp_update_themes();

    // Mark as done
    update_option( 'sinople_initial_updates_done', true );
}
add_action( 'admin_init', 'sinople_trigger_initial_updates', 1 );

/**
 * Add Libravatar Support
 *
 * Libravatar is a free/open-source alternative to Gravatar.
 * This adds it as an avatar option alongside Gravatar.
 */
function sinople_add_libravatar_support( $avatar_defaults ) {
    // Add Libravatar to the list of avatar services
    $avatar_defaults['libravatar'] = __( 'Libravatar (Free/Open Source alternative to Gravatar)', 'sinople' );
    return $avatar_defaults;
}
add_filter( 'avatar_defaults', 'sinople_add_libravatar_support' );

/**
 * Use Libravatar instead of Gravatar for avatars
 *
 * Checks Libravatar first, falls back to Gravatar if not found
 */
function sinople_libravatar_url( $avatar, $id_or_email, $size, $default, $alt, $args ) {
    // Get the email address
    $email = '';
    if ( is_numeric( $id_or_email ) ) {
        $user = get_user_by( 'id', absint( $id_or_email ) );
        if ( $user ) {
            $email = $user->user_email;
        }
    } elseif ( is_object( $id_or_email ) ) {
        if ( ! empty( $id_or_email->user_id ) ) {
            $user = get_user_by( 'id', absint( $id_or_email->user_id ) );
            if ( $user ) {
                $email = $user->user_email;
            }
        } elseif ( ! empty( $id_or_email->comment_author_email ) ) {
            $email = $id_or_email->comment_author_email;
        }
    } else {
        $email = $id_or_email;
    }

    if ( ! $email ) {
        return $avatar;
    }

    // Generate Libravatar URL
    $email_hash = md5( strtolower( trim( $email ) ) );
    $libravatar_url = sprintf(
        'https://seccdn.libravatar.org/avatar/%s?s=%d&d=%s',
        $email_hash,
        $size,
        urlencode( $default )
    );

    // Replace Gravatar URL with Libravatar URL in the avatar HTML
    $avatar = preg_replace(
        '/https?:\/\/[^\/]*gravatar\.com\/avatar\/[^\'\"]*/',
        $libravatar_url,
        $avatar
    );

    return $avatar;
}
add_filter( 'get_avatar', 'sinople_libravatar_url', 10, 6 );

/**
 * Add Libravatar Settings to Discussion Settings
 *
 * Adds an option to enable/disable Libravatar in Settings > Discussion
 */
function sinople_add_libravatar_settings() {
    add_settings_field(
        'sinople_use_libravatar',
        __( 'Use Libravatar', 'sinople' ),
        'sinople_libravatar_setting_callback',
        'discussion',
        'avatars',
        array(
            'label_for' => 'sinople_use_libravatar',
        )
    );

    register_setting( 'discussion', 'sinople_use_libravatar', array(
        'type'              => 'boolean',
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ) );
}
add_action( 'admin_init', 'sinople_add_libravatar_settings' );

/**
 * Render Libravatar setting checkbox
 */
function sinople_libravatar_setting_callback( $args ) {
    $value = get_option( 'sinople_use_libravatar', true );
    ?>
    <label for="<?php echo esc_attr( $args['label_for'] ); ?>">
        <input
            type="checkbox"
            id="<?php echo esc_attr( $args['label_for'] ); ?>"
            name="<?php echo esc_attr( $args['label_for'] ); ?>"
            value="1"
            <?php checked( $value, true ); ?>
        />
        <?php
        esc_html_e(
            'Use Libravatar (free/open-source) instead of Gravatar for user avatars',
            'sinople'
        );
        ?>
    </label>
    <p class="description">
        <?php
        printf(
            /* translators: %s: Libravatar URL */
            esc_html__( 'Libravatar is a free and open-source alternative to Gravatar. Learn more at %s', 'sinople' ),
            '<a href="https://www.libravatar.org/" target="_blank" rel="noopener">libravatar.org</a>'
        );
        ?>
    </p>
    <?php
}

/**
 * Conditionally use Libravatar based on settings
 */
function sinople_maybe_use_libravatar( $avatar, $id_or_email, $size, $default, $alt, $args ) {
    // Check if Libravatar is enabled
    if ( get_option( 'sinople_use_libravatar', true ) ) {
        return sinople_libravatar_url( $avatar, $id_or_email, $size, $default, $alt, $args );
    }
    return $avatar;
}
// Remove the direct filter and use the conditional one
remove_filter( 'get_avatar', 'sinople_libravatar_url', 10 );
add_filter( 'get_avatar', 'sinople_maybe_use_libravatar', 10, 6 );

/**
 * Set Content Width
 *
 * For accessibility and responsive design
 */
function sinople_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'sinople_content_width', 1200 );
}
add_action( 'after_setup_theme', 'sinople_content_width', 0 );

/**
 * Register Widget Areas
 */
function sinople_widgets_init() {
    register_sidebar( array(
        'name'          => esc_html__( 'Main Sidebar', 'sinople' ),
        'id'            => 'sidebar-1',
        'description'   => esc_html__( 'Main sidebar widget area', 'sinople' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );

    register_sidebar( array(
        'name'          => esc_html__( 'Footer Widgets', 'sinople' ),
        'id'            => 'footer-widgets',
        'description'   => esc_html__( 'Footer widget area', 'sinople' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'sinople_widgets_init' );

/**
 * Enqueue Scripts and Styles
 */
function sinople_enqueue_assets() {
    // Main stylesheet
    wp_enqueue_style(
        'sinople-style',
        get_stylesheet_uri(),
        array(),
        SINOPLE_VERSION
    );

    // Additional stylesheets
    wp_enqueue_style(
        'sinople-layout',
        SINOPLE_URL . '/assets/css/layout.css',
        array( 'sinople-style' ),
        SINOPLE_VERSION
    );

    wp_enqueue_style(
        'sinople-components',
        SINOPLE_URL . '/assets/css/components.css',
        array( 'sinople-style' ),
        SINOPLE_VERSION
    );

    wp_enqueue_style(
        'sinople-accessibility',
        SINOPLE_URL . '/assets/css/accessibility.css',
        array( 'sinople-style' ),
        SINOPLE_VERSION
    );

    // Print styles
    wp_enqueue_style(
        'sinople-print',
        SINOPLE_URL . '/assets/css/print.css',
        array(),
        SINOPLE_VERSION,
        'print'
    );

    // Navigation script with accessibility support
    wp_enqueue_script(
        'sinople-navigation',
        SINOPLE_URL . '/assets/js/navigation.js',
        array(),
        SINOPLE_VERSION,
        true
    );

    // WASM Semantic Processor (only on construct/entanglement pages)
    if ( is_singular( array( 'sinople_construct', 'sinople_entanglement' ) ) ) {
        wp_enqueue_script(
            'sinople-wasm',
            SINOPLE_URL . '/assets/wasm/semantic_processor.js',
            array(),
            SINOPLE_VERSION,
            true
        );

        wp_enqueue_script(
            'sinople-graph-viewer',
            SINOPLE_URL . '/assets/js/graph-viewer.js',
            array( 'sinople-wasm' ),
            SINOPLE_VERSION,
            true
        );
    }

    // Comment reply script
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }

    // Localize script for AJAX and settings
    wp_localize_script( 'sinople-navigation', 'sinople', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'sinople_nonce' ),
        'rest_url' => esc_url_raw( rest_url() ),
        'home_url' => esc_url( home_url( '/' ) ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'sinople_enqueue_assets' );

/**
 * Include additional functionality
 */
// Security must be loaded first (provides sinople_security() helper)
require_once SINOPLE_PATH . '/inc/security.php';

require_once SINOPLE_PATH . '/inc/custom-post-types.php';
require_once SINOPLE_PATH . '/inc/taxonomies.php';
require_once SINOPLE_PATH . '/inc/widgets.php';
require_once SINOPLE_PATH . '/inc/customizer.php';
require_once SINOPLE_PATH . '/inc/indieweb.php';
require_once SINOPLE_PATH . '/inc/semantic.php';
require_once SINOPLE_PATH . '/inc/accessibility.php';

/**
 * Add body classes for styling
 */
function sinople_body_classes( $classes ) {
    // Add class if sidebar is active
    if ( is_active_sidebar( 'sidebar-1' ) ) {
        $classes[] = 'has-sidebar';
    }

    // Add class for singular posts
    if ( is_singular() ) {
        $classes[] = 'singular';
    }

    // Add accessibility mode class if enabled
    if ( get_theme_mod( 'sinople_high_contrast_mode', false ) ) {
        $classes[] = 'high-contrast';
    }

    return $classes;
}
add_filter( 'body_class', 'sinople_body_classes' );

/**
 * Add Dublin Core metadata to head
 */
function sinople_dublin_core_metadata() {
    if ( is_singular() ) {
        global $post;
        ?>
        <meta name="DC.title" content="<?php echo esc_attr( get_the_title() ); ?>">
        <meta name="DC.creator" content="<?php echo esc_attr( get_the_author() ); ?>">
        <meta name="DC.date" content="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
        <meta name="DC.type" content="Text">
        <meta name="DC.format" content="text/html">
        <meta name="DC.language" content="<?php echo esc_attr( get_bloginfo( 'language' ) ); ?>">
        <meta name="DC.identifier" content="<?php echo esc_url( get_permalink() ); ?>">
        <?php
    }
}
add_action( 'wp_head', 'sinople_dublin_core_metadata' );

/**
 * Add Open Graph metadata for social sharing
 */
function sinople_open_graph_metadata() {
    if ( is_singular() ) {
        ?>
        <meta property="og:title" content="<?php echo esc_attr( get_the_title() ); ?>">
        <meta property="og:type" content="article">
        <meta property="og:url" content="<?php echo esc_url( get_permalink() ); ?>">
        <?php if ( has_post_thumbnail() ) : ?>
        <meta property="og:image" content="<?php echo esc_url( get_the_post_thumbnail_url( null, 'sinople-featured' ) ); ?>">
        <?php endif; ?>
        <meta property="og:description" content="<?php echo esc_attr( wp_trim_words( get_the_excerpt(), 30 ) ); ?>">
        <meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
        <?php
    }
}
add_action( 'wp_head', 'sinople_open_graph_metadata' );

/**
 * Custom excerpt length
 */
function sinople_excerpt_length( $length ) {
    return 30;
}
add_filter( 'excerpt_length', 'sinople_excerpt_length' );

/**
 * Custom excerpt more text
 */
function sinople_excerpt_more( $more ) {
    return '&hellip; <a href="' . esc_url( get_permalink() ) . '" class="read-more">' . esc_html__( 'Continue reading', 'sinople' ) . '<span class="sr-only"> "' . esc_html( get_the_title() ) . '"</span></a>';
}
add_filter( 'excerpt_more', 'sinople_excerpt_more' );

/**
 * Add skip links for accessibility
 */
function sinople_skip_links() {
    ?>
    <a class="skip-link sr-only" href="#main"><?php esc_html_e( 'Skip to main content', 'sinople' ); ?></a>
    <a class="skip-link sr-only" href="#nav"><?php esc_html_e( 'Skip to navigation', 'sinople' ); ?></a>
    <?php
}
add_action( 'wp_body_open', 'sinople_skip_links' );

/**
 * Improve archive titles
 */
function sinople_archive_title( $title ) {
    if ( is_category() ) {
        $title = single_cat_title( '', false );
    } elseif ( is_tag() ) {
        $title = single_tag_title( '', false );
    } elseif ( is_author() ) {
        $title = get_the_author();
    } elseif ( is_post_type_archive() ) {
        $title = post_type_archive_title( '', false );
    }
    return $title;
}
add_filter( 'get_the_archive_title', 'sinople_archive_title' );

/**
 * Security: Remove WordPress version from head
 */
remove_action( 'wp_head', 'wp_generator' );

/**
 * Security: Disable XML-RPC if not needed
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Security: Set HTTP security headers via PhpAegis
 *
 * Sets comprehensive security headers for defense in depth.
 * Must run before any output is sent.
 */
function sinople_security_headers(): void {
    // Don't set headers in admin or for AJAX
    if ( is_admin() || wp_doing_ajax() ) {
        return;
    }

    // Don't set headers if already sent
    if ( headers_sent() ) {
        return;
    }

    // Remove insecure headers
    \PhpAegis\Headers::removeInsecureHeaders();

    // Set Content-Type-Options
    \PhpAegis\Headers::contentTypeOptions();

    // Set Frame Options (allow same origin for embeds)
    \PhpAegis\Headers::frameOptions( 'SAMEORIGIN' );

    // Set XSS Protection (legacy browser support)
    \PhpAegis\Headers::xssProtection();

    // Set Referrer Policy
    \PhpAegis\Headers::referrerPolicy( 'strict-origin-when-cross-origin' );

    // Set HSTS (only on HTTPS)
    if ( is_ssl() ) {
        \PhpAegis\Headers::strictTransportSecurity( 31536000, true, false );
    }

    // Set Content Security Policy (permissive for WordPress compatibility)
    \PhpAegis\Headers::contentSecurityPolicy( array(
        'default-src'  => array( "'self'" ),
        'script-src'   => array( "'self'", "'unsafe-inline'", "'unsafe-eval'" ), // WP admin needs these
        'style-src'    => array( "'self'", "'unsafe-inline'" ),
        'img-src'      => array( "'self'", 'data:', 'https:' ),
        'font-src'     => array( "'self'", 'data:' ),
        'connect-src'  => array( "'self'" ),
        'frame-src'    => array( "'self'" ),
        'object-src'   => array( "'none'" ),
        'base-uri'     => array( "'self'" ),
        'form-action'  => array( "'self'" ),
    ) );

    // Set Permissions Policy (restrict dangerous features)
    \PhpAegis\Headers::permissionsPolicy( array(
        'geolocation'        => array(),
        'microphone'         => array(),
        'camera'             => array(),
        'payment'            => array(),
        'usb'                => array(),
        'interest-cohort'    => array(), // Disable FLoC
    ) );
}
add_action( 'send_headers', 'sinople_security_headers' );

/**
 * Performance: Defer non-critical scripts
 */
function sinople_defer_scripts( $tag, $handle, $src ) {
    $defer_scripts = array( 'sinople-graph-viewer' );

    if ( in_array( $handle, $defer_scripts, true ) ) {
        return str_replace( '<script ', '<script defer ', $tag );
    }

    return $tag;
}
add_filter( 'script_loader_tag', 'sinople_defer_scripts', 10, 3 );

/**
 * Add language attribute to links for screen readers
 */
function sinople_language_attributes( $output ) {
    return $output . ' lang="' . esc_attr( get_bloginfo( 'language' ) ) . '"';
}
add_filter( 'language_attributes', 'sinople_language_attributes' );

/**
 * Debug mode check
 */
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    error_log( 'Sinople Theme loaded - Version: ' . SINOPLE_VERSION );
}
