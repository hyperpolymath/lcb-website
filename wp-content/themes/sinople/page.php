<?php
/**
 * Page Template (default with sidebar)
 *
 * @package Sinople
 * @since 2.0.0
 */

get_header();
?>

<div class="container">
    <div class="content-area">
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

        <?php get_sidebar(); ?>
    </div>
</div>

<?php get_footer();
