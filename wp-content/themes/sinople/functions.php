<?php
/**
 * Sinople Theme Functions
 *
 * Core functionality for the Sinople WordPress theme.
 * News-magazine layout with NUJ green scheme, dark mode,
 * IndieWeb features, and WCAG AAA accessibility.
 *
 * @package Sinople
 * @since 2.0.0
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Load PhpAegis security library
require_once get_template_directory() . '/vendor/php-aegis/autoload.php';

// Theme constants
define( 'SINOPLE_VERSION', '2.0.0' );
define( 'SINOPLE_PATH', get_template_directory() );
define( 'SINOPLE_URL', get_template_directory_uri() );

/**
 * Theme Setup
 */
function sinople_theme_setup() {
    load_theme_textdomain( 'sinople', SINOPLE_PATH . '/languages' );

    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );

    // Post thumbnails with news-magazine sizes
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 1200, 630, true );
    add_image_size( 'sinople-featured', 1200, 675, true );
    add_image_size( 'sinople-card', 600, 400, true );
    add_image_size( 'sinople-thumbnail', 400, 300, true );
    add_image_size( 'sinople-ticker', 100, 100, true );

    // Navigation menus
    register_nav_menus( array(
        'primary' => esc_html__( 'Primary Menu', 'sinople' ),
        'topbar'  => esc_html__( 'Top Bar Menu', 'sinople' ),
        'footer'  => esc_html__( 'Footer Menu', 'sinople' ),
        'social'  => esc_html__( 'Social Links Menu', 'sinople' ),
    ) );

    add_theme_support( 'html5', array(
        'search-form', 'comment-form', 'comment-list',
        'gallery', 'caption', 'style', 'script', 'navigation-widgets',
    ) );

    add_theme_support( 'custom-logo', array(
        'height'      => 80,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    add_theme_support( 'custom-background', array(
        'default-color' => 'ffffff',
    ) );

    add_theme_support( 'editor-styles' );
    add_editor_style( 'assets/css/editor-style.css' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'custom-line-height' );
    add_theme_support( 'custom-spacing' );
}
add_action( 'after_setup_theme', 'sinople_theme_setup' );

/**
 * Set Content Width
 */
function sinople_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'sinople_content_width', 1200 );
}
add_action( 'after_setup_theme', 'sinople_content_width', 0 );

/**
 * Add Favicons to Head
 */
function sinople_add_favicons() {
    $favicon_path = SINOPLE_URL . '/assets/images';
    ?>
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
 * Theme Activation: Initial Setup
 */
function sinople_on_theme_activation() {
    if ( get_option( 'sinople_activation_cleanup_done' ) ) {
        return;
    }

    $default_plugins = array( 'akismet/akismet.php', 'hello.php' );
    foreach ( $default_plugins as $plugin ) {
        if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin ) ) {
            deactivate_plugins( $plugin, true );
        }
    }

    wp_clean_update_cache();
    wp_update_themes();
    wp_update_plugins();
    wp_version_check();

    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    $upgrader = new Language_Pack_Upgrader( new Automatic_Upgrader_Skin() );
    $translations = wp_get_translation_updates();
    if ( ! empty( $translations ) ) {
        $upgrader->bulk_upgrade( $translations );
    }

    update_option( 'sinople_activation_cleanup_done', true );
}
add_action( 'after_switch_theme', 'sinople_on_theme_activation' );

/**
 * Trigger Updates on First Admin Visit
 */
function sinople_trigger_initial_updates() {
    if ( ! current_user_can( 'update_core' ) ) {
        return;
    }
    if ( get_option( 'sinople_initial_updates_done' ) ) {
        return;
    }
    delete_site_transient( 'update_core' );
    delete_site_transient( 'update_plugins' );
    delete_site_transient( 'update_themes' );
    wp_version_check();
    wp_update_plugins();
    wp_update_themes();
    update_option( 'sinople_initial_updates_done', true );
}
add_action( 'admin_init', 'sinople_trigger_initial_updates', 1 );

/**
 * Libravatar Support
 */
function sinople_add_libravatar_support( $avatar_defaults ) {
    $avatar_defaults['libravatar'] = __( 'Libravatar (Free/Open Source alternative to Gravatar)', 'sinople' );
    return $avatar_defaults;
}
add_filter( 'avatar_defaults', 'sinople_add_libravatar_support' );

function sinople_libravatar_url( $avatar, $id_or_email, $size, $default, $alt, $args ) {
    $email = '';
    if ( is_numeric( $id_or_email ) ) {
        $user = get_user_by( 'id', absint( $id_or_email ) );
        if ( $user ) { $email = $user->user_email; }
    } elseif ( is_object( $id_or_email ) ) {
        if ( ! empty( $id_or_email->user_id ) ) {
            $user = get_user_by( 'id', absint( $id_or_email->user_id ) );
            if ( $user ) { $email = $user->user_email; }
        } elseif ( ! empty( $id_or_email->comment_author_email ) ) {
            $email = $id_or_email->comment_author_email;
        }
    } else {
        $email = $id_or_email;
    }

    if ( ! $email ) {
        return $avatar;
    }

    $email_hash = md5( strtolower( trim( $email ) ) );
    $libravatar_url = sprintf(
        'https://seccdn.libravatar.org/avatar/%s?s=%d&d=%s',
        $email_hash, $size, urlencode( $default )
    );

    $avatar = preg_replace(
        '/https?:\/\/[^\/]*gravatar\.com\/avatar\/[^\'\"]*/',
        $libravatar_url, $avatar
    );

    return $avatar;
}
add_filter( 'get_avatar', 'sinople_libravatar_url', 10, 6 );

function sinople_add_libravatar_settings() {
    add_settings_field(
        'sinople_use_libravatar',
        __( 'Use Libravatar', 'sinople' ),
        'sinople_libravatar_setting_callback',
        'discussion', 'avatars',
        array( 'label_for' => 'sinople_use_libravatar' )
    );
    register_setting( 'discussion', 'sinople_use_libravatar', array(
        'type'              => 'boolean',
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ) );
}
add_action( 'admin_init', 'sinople_add_libravatar_settings' );

function sinople_libravatar_setting_callback( $args ) {
    $value = get_option( 'sinople_use_libravatar', true );
    ?>
    <label for="<?php echo esc_attr( $args['label_for'] ); ?>">
        <input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>"
               name="<?php echo esc_attr( $args['label_for'] ); ?>" value="1"
               <?php checked( $value, true ); ?> />
        <?php esc_html_e( 'Use Libravatar (free/open-source) instead of Gravatar for user avatars', 'sinople' ); ?>
    </label>
    <p class="description">
        <?php printf(
            esc_html__( 'Libravatar is a free and open-source alternative to Gravatar. Learn more at %s', 'sinople' ),
            '<a href="https://www.libravatar.org/" target="_blank" rel="noopener">libravatar.org</a>'
        ); ?>
    </p>
    <?php
}

function sinople_maybe_use_libravatar( $avatar, $id_or_email, $size, $default, $alt, $args ) {
    if ( get_option( 'sinople_use_libravatar', true ) ) {
        return sinople_libravatar_url( $avatar, $id_or_email, $size, $default, $alt, $args );
    }
    return $avatar;
}
remove_filter( 'get_avatar', 'sinople_libravatar_url', 10 );
add_filter( 'get_avatar', 'sinople_maybe_use_libravatar', 10, 6 );

/**
 * Register Widget Areas
 */
function sinople_widgets_init() {
    $shared = array(
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    );

    register_sidebar( array_merge( $shared, array(
        'name' => esc_html__( 'Main Sidebar', 'sinople' ),
        'id'   => 'sidebar-1',
        'description' => esc_html__( 'Main sidebar widget area', 'sinople' ),
    ) ) );

    register_sidebar( array_merge( $shared, array(
        'name' => esc_html__( 'Featured Left', 'sinople' ),
        'id'   => 'featured-left',
        'description' => esc_html__( 'Left sidebar on featured section', 'sinople' ),
    ) ) );

    register_sidebar( array_merge( $shared, array(
        'name' => esc_html__( 'Featured Right', 'sinople' ),
        'id'   => 'featured-right',
        'description' => esc_html__( 'Right sidebar on featured section', 'sinople' ),
    ) ) );

    for ( $i = 1; $i <= 4; $i++ ) {
        register_sidebar( array_merge( $shared, array(
            'name' => sprintf( esc_html__( 'Footer Column %d', 'sinople' ), $i ),
            'id'   => 'footer-' . $i,
            'description' => sprintf( esc_html__( 'Footer widget column %d', 'sinople' ), $i ),
        ) ) );
    }
}
add_action( 'widgets_init', 'sinople_widgets_init' );

/**
 * Enqueue Scripts and Styles
 *
 * In production mode (SINOPLE_PRODUCTION defined and true), loads minified
 * bundles from assets/dist/. Otherwise loads individual files for development.
 * Run `php scripts/build-assets.php` to rebuild the bundles.
 */
function sinople_enqueue_assets() {
    $production = defined( 'SINOPLE_PRODUCTION' ) && SINOPLE_PRODUCTION;

    if ( $production && file_exists( SINOPLE_PATH . '/assets/dist/sinople.min.css' ) ) {
        // Production: single CSS bundle (includes variables, layout, components, dark mode, etc.)
        wp_enqueue_style( 'sinople-bundle', SINOPLE_URL . '/assets/dist/sinople.min.css', array(), SINOPLE_VERSION );
    } else {
        // Development: individual CSS files — order matters for cascade
        wp_enqueue_style( 'sinople-variables', SINOPLE_URL . '/assets/css/variables.css', array(), SINOPLE_VERSION );
        wp_enqueue_style( 'sinople-style', get_stylesheet_uri(), array( 'sinople-variables' ), SINOPLE_VERSION );
        wp_enqueue_style( 'sinople-layout', SINOPLE_URL . '/assets/css/layout.css', array( 'sinople-style' ), SINOPLE_VERSION );
        wp_enqueue_style( 'sinople-grid', SINOPLE_URL . '/assets/css/grid.css', array( 'sinople-layout' ), SINOPLE_VERSION );
        wp_enqueue_style( 'sinople-header', SINOPLE_URL . '/assets/css/header.css', array( 'sinople-style' ), SINOPLE_VERSION );
        wp_enqueue_style( 'sinople-offcanvas', SINOPLE_URL . '/assets/css/offcanvas.css', array( 'sinople-style' ), SINOPLE_VERSION );
        wp_enqueue_style( 'sinople-components', SINOPLE_URL . '/assets/css/components.css', array( 'sinople-style' ), SINOPLE_VERSION );
        wp_enqueue_style( 'sinople-cards', SINOPLE_URL . '/assets/css/cards.css', array( 'sinople-style' ), SINOPLE_VERSION );
        wp_enqueue_style( 'sinople-sidebar', SINOPLE_URL . '/assets/css/sidebar.css', array( 'sinople-style' ), SINOPLE_VERSION );
        wp_enqueue_style( 'sinople-footer-css', SINOPLE_URL . '/assets/css/footer.css', array( 'sinople-style' ), SINOPLE_VERSION );
        wp_enqueue_style( 'sinople-dark-mode', SINOPLE_URL . '/assets/css/dark-mode.css', array( 'sinople-variables' ), SINOPLE_VERSION );
        wp_enqueue_style( 'sinople-search-modal', SINOPLE_URL . '/assets/css/search-modal.css', array( 'sinople-style' ), SINOPLE_VERSION );
        wp_enqueue_style( 'sinople-accessibility', SINOPLE_URL . '/assets/css/accessibility.css', array( 'sinople-style' ), SINOPLE_VERSION );
        wp_enqueue_style( 'sinople-a11y-toolbar', SINOPLE_URL . '/assets/css/a11y-toolbar.css', array( 'sinople-variables' ), SINOPLE_VERSION );

        // Featured section CSS (front page only, already in bundle for production)
        if ( is_front_page() ) {
            wp_enqueue_style( 'sinople-featured', SINOPLE_URL . '/assets/css/featured.css', array( 'sinople-style' ), SINOPLE_VERSION );
        }
    }

    // Always separate: print (different media), vendor (different versioning), fonts
    wp_enqueue_style( 'sinople-print', SINOPLE_URL . '/assets/css/print.css', array(), SINOPLE_VERSION, 'print' );
    wp_enqueue_style( 'fontawesome', SINOPLE_URL . '/assets/vendor/fontawesome/css/fontawesome-subset.css', array(), '6.5.0' );
    wp_enqueue_style( 'sinople-fonts', SINOPLE_URL . '/assets/css/fonts.css', array(), SINOPLE_VERSION );

    if ( is_front_page() ) {
        wp_enqueue_style( 'swiper', SINOPLE_URL . '/assets/vendor/swiper/swiper-bundle.min.css', array(), '11.0.0' );
    }

    // JavaScript
    if ( $production && file_exists( SINOPLE_PATH . '/assets/dist/sinople.min.js' ) ) {
        // Production: single JS bundle (dark mode, offcanvas, search modal, navigation)
        wp_enqueue_script( 'sinople-bundle', SINOPLE_URL . '/assets/dist/sinople.min.js', array(), SINOPLE_VERSION, true );
    } else {
        // Development: individual JS files
        wp_enqueue_script( 'sinople-dark-mode', SINOPLE_URL . '/assets/js/dark-mode.js', array(), SINOPLE_VERSION, true );
        wp_enqueue_script( 'sinople-offcanvas', SINOPLE_URL . '/assets/js/offcanvas.js', array(), SINOPLE_VERSION, true );
        wp_enqueue_script( 'sinople-search-modal', SINOPLE_URL . '/assets/js/search-modal.js', array(), SINOPLE_VERSION, true );
        wp_enqueue_script( 'sinople-navigation', SINOPLE_URL . '/assets/js/navigation.js', array(), SINOPLE_VERSION, true );
        wp_enqueue_script( 'sinople-a11y-toolbar', SINOPLE_URL . '/assets/js/a11y-toolbar.js', array(), SINOPLE_VERSION, true );
    }

    // Swiper (front page only, always separate — vendor dependency)
    if ( is_front_page() ) {
        wp_enqueue_script( 'swiper', SINOPLE_URL . '/assets/vendor/swiper/swiper-bundle.min.js', array(), '11.0.0', true );
        $swiper_dep = $production ? 'sinople-bundle' : 'sinople-navigation';
        wp_enqueue_script( 'sinople-swiper-init', SINOPLE_URL . '/assets/js/swiper-init.js', array( 'swiper', $swiper_dep ), SINOPLE_VERSION, true );
    }

    // Comment reply
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }

    // Localize (attach to whichever script handle is active)
    $main_handle = $production ? 'sinople-bundle' : 'sinople-navigation';
    wp_localize_script( $main_handle, 'sinople', array(
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
require_once SINOPLE_PATH . '/inc/security.php';
require_once SINOPLE_PATH . '/inc/variants.php';
require_once SINOPLE_PATH . '/inc/widgets.php';
require_once SINOPLE_PATH . '/inc/customizer.php';
require_once SINOPLE_PATH . '/inc/indieweb.php';
require_once SINOPLE_PATH . '/inc/accessibility.php';
require_once SINOPLE_PATH . '/inc/template-tags.php';
require_once SINOPLE_PATH . '/inc/walker-nav.php';
require_once SINOPLE_PATH . '/inc/block-patterns.php';
require_once SINOPLE_PATH . '/inc/theme-options.php';

/**
 * Add body classes
 */
function sinople_body_classes( $classes ) {
    if ( is_active_sidebar( 'sidebar-1' ) ) {
        $classes[] = 'has-sidebar';
    }
    if ( is_singular() ) {
        $classes[] = 'singular';
    }
    if ( is_front_page() ) {
        $classes[] = 'front-page';
    }
    return $classes;
}
add_filter( 'body_class', 'sinople_body_classes' );

/**
 * Footer policy links.
 */
function sinople_get_policy_links(): array {
    $links = array();

    $privacy_policy_url = get_privacy_policy_url();
    if ( ! empty( $privacy_policy_url ) ) {
        $links[] = array(
            'label' => __( 'Privacy Policy', 'sinople' ),
            'url'   => $privacy_policy_url,
        );
    }

    // Only show page links if the page actually exists
    $optional_pages = array(
        'ai-usage-policy'    => __( 'AI Usage Policy', 'sinople' ),
        'imprint-impressum'  => __( 'Imprint', 'sinople' ),
    );
    foreach ( $optional_pages as $slug => $label ) {
        if ( get_page_by_path( $slug ) ) {
            $links[] = array( 'label' => $label, 'url' => home_url( '/' . $slug . '/' ) );
        }
    }

    // .well-known files are always available
    $links[] = array( 'label' => __( 'Security.txt', 'sinople' ), 'url' => home_url( '/.well-known/security.txt' ) );
    $links[] = array( 'label' => __( 'AI.txt', 'sinople' ), 'url' => home_url( '/.well-known/ai.txt' ) );

    return $links;
}

/**
 * Dublin Core metadata
 */
function sinople_dublin_core_metadata() {
    if ( is_singular() ) {
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
 * Open Graph metadata
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
 * Custom excerpt
 */
function sinople_excerpt_length( $length ) {
    return 25;
}
add_filter( 'excerpt_length', 'sinople_excerpt_length' );

function sinople_excerpt_more( $more ) {
    return '&hellip;';
}
add_filter( 'excerpt_more', 'sinople_excerpt_more' );

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
 * Security hardening
 */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Security headers via PhpAegis
 */
function sinople_security_headers(): void {
    if ( is_admin() || wp_doing_ajax() || headers_sent() ) {
        return;
    }

    \PhpAegis\Headers::removeInsecureHeaders();
    \PhpAegis\Headers::contentTypeOptions();
    \PhpAegis\Headers::frameOptions( 'SAMEORIGIN' );
    \PhpAegis\Headers::xssProtection();
    \PhpAegis\Headers::referrerPolicy( 'strict-origin-when-cross-origin' );

    if ( is_ssl() ) {
        \PhpAegis\Headers::strictTransportSecurity( 31536000, true, false );
    }

    \PhpAegis\Headers::contentSecurityPolicy( array(
        'default-src'  => array( "'self'" ),
        'script-src'   => array( "'self'", "'unsafe-inline'", "'unsafe-eval'" ),
        'style-src'    => array( "'self'", "'unsafe-inline'" ),
        'img-src'      => array( "'self'", 'data:', 'https:' ),
        'font-src'     => array( "'self'", 'data:' ),
        'connect-src'  => array( "'self'" ),
        'frame-src'    => array( "'self'" ),
        'object-src'   => array( "'none'" ),
        'base-uri'     => array( "'self'" ),
        'form-action'  => array( "'self'" ),
    ) );

    \PhpAegis\Headers::permissionsPolicy( array(
        'geolocation'     => array(),
        'microphone'      => array(),
        'camera'          => array(),
        'payment'         => array(),
        'usb'             => array(),
        'interest-cohort' => array(),
    ) );
}
add_action( 'send_headers', 'sinople_security_headers' );

/**
 * Debug mode
 */
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    error_log( 'Sinople Theme loaded - Version: ' . SINOPLE_VERSION );
}
