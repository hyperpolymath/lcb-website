<?php
/**
 * Search Modal Overlay
 *
 * @package Sinople
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }
?>

<div id="search-modal" class="search-modal" role="dialog" aria-modal="true"
     aria-label="<?php esc_attr_e( 'Search', 'sinople' ); ?>">
    <div class="search-modal-content">
        <div class="search-modal-header">
            <h2 class="search-modal-title"><?php esc_html_e( 'Search', 'sinople' ); ?></h2>
            <button class="search-modal-close" type="button"
                    aria-label="<?php esc_attr_e( 'Close search', 'sinople' ); ?>">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </div>

        <?php get_search_form(); ?>

        <p class="search-modal-hint">
            <?php
            printf(
                esc_html__( 'Press %1$s to search, %2$s to close', 'sinople' ),
                '<kbd>Enter</kbd>',
                '<kbd>Esc</kbd>'
            );
            ?>
        </p>
    </div>
</div>
