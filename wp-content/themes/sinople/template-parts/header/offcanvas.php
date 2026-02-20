<?php
/**
 * Offcanvas mobile navigation drawer
 *
 * @package Sinople
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<!-- Backdrop -->
<div id="offcanvas-backdrop" class="offcanvas-backdrop" aria-hidden="true"></div>

<!-- Drawer -->
<aside id="offcanvas-drawer" class="offcanvas-drawer" role="dialog" aria-modal="true"
       aria-label="<?php esc_attr_e( 'Mobile navigation', 'sinople' ); ?>">

    <div class="offcanvas-header">
        <span class="offcanvas-title"><?php esc_html_e( 'Menu', 'sinople' ); ?></span>
        <button id="offcanvas-close" class="offcanvas-close" type="button"
                aria-label="<?php esc_attr_e( 'Close menu', 'sinople' ); ?>">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
    </div>

    <nav class="offcanvas-body" role="navigation" aria-label="<?php esc_attr_e( 'Mobile Navigation', 'sinople' ); ?>">
        <?php
        wp_nav_menu( array(
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'offcanvas-menu',
            'fallback_cb'    => 'wp_page_menu',
            'depth'          => 2,
        ) );
        ?>

        <!-- Dark mode toggle in mobile -->
        <div style="margin-top: var(--space-6); padding-top: var(--space-4); border-top: 1px solid var(--color-border);">
            <button class="dark-mode-toggle" type="button" style="display: flex; align-items: center; gap: var(--space-2); background: none; border: none; color: var(--color-heading); cursor: pointer; font-size: var(--font-base); padding: var(--space-2) 0;">
                <i class="fa-solid fa-moon" aria-hidden="true"></i>
                <i class="fa-solid fa-sun" aria-hidden="true"></i>
                <span><?php esc_html_e( 'Toggle dark mode', 'sinople' ); ?></span>
            </button>
        </div>
    </nav>
</aside>
