<?php
/**
 * Archive Template
 *
 * Card grid + sidebar layout for category, tag, date, author archives.
 *
 * @package Sinople
 * @since 2.0.0
 */

get_header();
?>

<div class="container">
    <div class="content-area">
        <main id="main" class="site-main" role="main">

            <header class="page-header">
                <h1 class="page-title"><?php the_archive_title(); ?></h1>
                <?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
            </header>

            <?php if ( have_posts() ) : ?>
                <div class="card-grid">
                    <?php while ( have_posts() ) : the_post(); ?>
                        <?php get_template_part( 'template-parts/content', 'card' ); ?>
                    <?php endwhile; ?>
                </div>

                <?php the_posts_pagination( array(
                    'prev_text' => '<i class="fa-solid fa-chevron-left" aria-hidden="true"></i> ' . esc_html__( 'Previous', 'sinople' ),
                    'next_text' => esc_html__( 'Next', 'sinople' ) . ' <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>',
                ) ); ?>

            <?php else : ?>
                <p><?php esc_html_e( 'No content found.', 'sinople' ); ?></p>
            <?php endif; ?>

        </main>

        <?php get_sidebar(); ?>
    </div>
</div>

    <!-- You May Have Missed -->
    <?php get_template_part( 'template-parts/footer/missed-posts' ); ?>

<?php get_footer();
