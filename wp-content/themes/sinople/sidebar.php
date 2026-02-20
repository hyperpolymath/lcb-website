<?php
/**
 * Sidebar Template
 *
 * @package Sinople
 * @since 2.0.0
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
    return;
}
?>

<aside id="secondary" class="widget-area" role="complementary"
       aria-label="<?php esc_attr_e( 'Sidebar', 'sinople' ); ?>">
    <?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
