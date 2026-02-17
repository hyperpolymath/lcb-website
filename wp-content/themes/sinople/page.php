<?php
/**
 * Template for displaying pages
 * @package Sinople
 */
get_header(); ?>

<?php $show_quick_links = function_exists( 'sinople_should_render_quick_links' ) && sinople_should_render_quick_links(); ?>
<div class="site-content<?php echo $show_quick_links ? ' front-layout has-quick-links' : ''; ?>">
    <?php
    if ( $show_quick_links && function_exists( 'sinople_render_quick_links' ) ) {
        sinople_render_quick_links();
    }
    ?>
    <main id="main" class="site-main" role="main">
        <?php while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                </header>
                <div class="entry-content">
                    <?php
                    the_content();
                    wp_link_pages( array(
                        'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'sinople' ),
                        'after'  => '</div>',
                    ) );
                    ?>
                </div>
            </article>
            <?php
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;
            ?>
        <?php endwhile; ?>
    </main>
</div>

<?php get_footer();
