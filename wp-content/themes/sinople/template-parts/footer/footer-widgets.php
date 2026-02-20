<?php
/**
 * Footer widget columns (4 columns)
 *
 * @package Sinople
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$has_widgets = false;
for ( $i = 1; $i <= 4; $i++ ) {
    if ( is_active_sidebar( 'footer-' . $i ) ) {
        $has_widgets = true;
        break;
    }
}

if ( ! $has_widgets ) {
    return;
}
?>

<div class="footer-widgets-area">
    <div class="container">
        <div class="footer-columns">
            <?php for ( $i = 1; $i <= 4; $i++ ) : ?>
                <div class="footer-column">
                    <?php if ( is_active_sidebar( 'footer-' . $i ) ) : ?>
                        <?php dynamic_sidebar( 'footer-' . $i ); ?>
                    <?php endif; ?>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</div>
