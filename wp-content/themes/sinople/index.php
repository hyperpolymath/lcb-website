<?php
/**
 * Main template file
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
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'h-entry' ); ?>>
                <header class="entry-header">
                    <h2 class="entry-title p-name"><a href="<?php the_permalink(); ?>" class="u-url"><?php the_title(); ?></a></h2>
                </header>
                <div class="entry-content e-content">
                    <?php the_excerpt(); ?>
                </div>
            </article>
        <?php endwhile; endif; ?>
    </main>

    <?php
    if ( ! $show_quick_links ) {
        get_sidebar();
    }
    ?>
</div>

<?php get_footer();
