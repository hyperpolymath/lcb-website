<?php
/**
 * Custom search form template for WCAG AAA compliance.
 *
 * Provides an explicit <label> element linked to the search input.
 *
 * SPDX-License-Identifier: GPL-2.0-or-later
 *
 * @package Sinople
 */

$unique_id = wp_unique_id( 'search-form-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label for="<?php echo esc_attr( $unique_id ); ?>" class="screen-reader-text">
        <?php esc_html_e( 'Search for:', 'sinople' ); ?>
    </label>
    <input type="search"
           id="<?php echo esc_attr( $unique_id ); ?>"
           class="search-field"
           placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'sinople' ); ?>"
           value="<?php echo get_search_query(); ?>"
           name="s"
           required />
    <button type="submit" class="search-submit">
        <span class="screen-reader-text"><?php esc_html_e( 'Search', 'sinople' ); ?></span>
        <i class="fa-solid fa-search" aria-hidden="true"></i>
    </button>
</form>
