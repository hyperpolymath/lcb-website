<?php
/**
 * Template part for displaying posts (list/single view)
 *
 * @package Sinople
 * @since 2.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'h-entry' ); ?>>
    <header class="entry-header">
        <?php
        if ( is_singular() ) :
            the_title( '<h1 class="entry-title p-name">', '</h1>' );
        else :
            the_title( '<h2 class="entry-title p-name"><a href="' . esc_url( get_permalink() ) . '" class="u-url" rel="bookmark">', '</a></h2>' );
        endif;
        ?>

        <div class="entry-meta">
            <?php sinople_posted_on(); ?>
            <?php sinople_posted_by(); ?>
        </div>
    </header>

    <div class="entry-content e-content">
        <?php
        if ( is_singular() ) :
            the_content();
        else :
            the_excerpt();
        endif;
        ?>
    </div>

    <footer class="entry-footer">
        <?php sinople_entry_categories(); ?>
        <?php sinople_entry_tags(); ?>
    </footer>
</article>
