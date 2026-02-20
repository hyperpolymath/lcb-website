<?php
/**
 * Ticker item template part
 *
 * @package Sinople
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }
?>

<a href="<?php the_permalink(); ?>" class="ticker-item">
    <?php the_title(); ?>
</a>
