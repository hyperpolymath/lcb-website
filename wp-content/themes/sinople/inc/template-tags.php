<?php
/**
 * Template Tags for Sinople Theme
 *
 * Helper functions for use in templates.
 *
 * @package Sinople
 * @since 2.0.0
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Print the posted-on date with microformat markup.
 */
function sinople_posted_on(): void {
    $time_string = '<time class="dt-published" datetime="%1$s">%2$s</time>';
    $time_string = sprintf(
        $time_string,
        esc_attr( get_the_date( DATE_W3C ) ),
        esc_html( get_the_date() )
    );

    printf(
        '<span class="posted-on"><i class="fa-regular fa-clock" aria-hidden="true"></i> %s</span>',
        $time_string
    );
}

/**
 * Print the author byline with microformat markup.
 */
function sinople_posted_by(): void {
    printf(
        '<span class="byline"><i class="fa-regular fa-user" aria-hidden="true"></i> <span class="p-author h-card"><a class="u-url" href="%1$s"><span class="p-name">%2$s</span></a></span></span>',
        esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
        esc_html( get_the_author() )
    );
}

/**
 * Print estimated reading time.
 *
 * @param int|null $post_id Post ID (defaults to current post).
 */
function sinople_reading_time( ?int $post_id = null ): void {
    $content = get_post_field( 'post_content', $post_id );
    $word_count = str_word_count( wp_strip_all_tags( $content ) );
    $minutes = max( 1, (int) ceil( $word_count / 250 ) );

    printf(
        '<span class="reading-time"><i class="fa-regular fa-hourglass-half" aria-hidden="true"></i> %s</span>',
        sprintf(
            esc_html( _n( '%d min read', '%d min read', $minutes, 'sinople' ) ),
            $minutes
        )
    );
}

/**
 * Print category list for a post.
 */
function sinople_entry_categories(): void {
    $categories = get_the_category_list( ', ' );
    if ( $categories ) {
        printf( '<span class="cat-links"><i class="fa-regular fa-folder" aria-hidden="true"></i> %s</span>', $categories );
    }
}

/**
 * Print tag list for a post.
 */
function sinople_entry_tags(): void {
    $tags = get_the_tag_list( '', ', ' );
    if ( $tags ) {
        printf( '<span class="tags-links"><i class="fa-solid fa-tags" aria-hidden="true"></i> %s</span>', $tags );
    }
}

/**
 * Print comment count link.
 */
function sinople_comment_count(): void {
    if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
        printf(
            '<span class="comments-link"><i class="fa-regular fa-comment" aria-hidden="true"></i> <a href="%s">%s</a></span>',
            esc_url( get_comments_link() ),
            esc_html( get_comments_number_text(
                __( '0 Comments', 'sinople' ),
                __( '1 Comment', 'sinople' ),
                __( '% Comments', 'sinople' )
            ) )
        );
    }
}

/**
 * Print the first category badge for a post card.
 */
function sinople_category_badge(): void {
    $categories = get_the_category();
    if ( ! empty( $categories ) ) {
        printf(
            '<a class="category-badge" href="%s">%s</a>',
            esc_url( get_category_link( $categories[0]->term_id ) ),
            esc_html( $categories[0]->name )
        );
    }
}
