<?php
/**
 * Main header: logo + primary nav + dark toggle + search button
 *
 * @package Sinople
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<header id="masthead" class="site-header" role="banner">
    <div class="container">
        <!-- Logo / Branding -->
        <div class="site-branding">
            <?php if ( has_custom_logo() ) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <p class="site-title">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                        <?php bloginfo( 'name' ); ?>
                    </a>
                </p>
                <?php
                $description = get_bloginfo( 'description', 'display' );
                if ( $description ) :
                    ?>
                    <p class="site-description"><?php echo esc_html( $description ); ?></p>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Primary Navigation (desktop) -->
        <nav id="nav" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'sinople' ); ?>">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'primary-menu',
                'fallback_cb'    => 'wp_page_menu',
                'walker'         => new Sinople_Nav_Walker(),
                'items_wrap'     => '<ul id="%1$s" class="%2$s" role="menubar">%3$s</ul>',
            ) );
            ?>
        </nav>

        <!-- Header Actions -->
        <div class="header-actions">
            <!-- Dark mode toggle -->
            <button class="header-btn dark-mode-toggle" type="button"
                    aria-label="<?php esc_attr_e( 'Switch to dark mode', 'sinople' ); ?>">
                <i class="fa-solid fa-moon" aria-hidden="true"></i>
                <i class="fa-solid fa-sun" aria-hidden="true"></i>
            </button>

            <!-- Search trigger -->
            <button class="header-btn search-trigger" type="button"
                    aria-label="<?php esc_attr_e( 'Open search', 'sinople' ); ?>"
                    data-search-open>
                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            </button>

            <!-- Mobile menu toggle -->
            <button class="header-btn offcanvas-toggle" type="button"
                    id="offcanvas-open"
                    aria-expanded="false"
                    aria-controls="offcanvas-drawer"
                    aria-label="<?php esc_attr_e( 'Open menu', 'sinople' ); ?>">
                <i class="fa-solid fa-bars" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</header>
