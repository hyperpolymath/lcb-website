<?php
/**
 * Front Page Template
 *
 * News-magazine layout: ticker → featured carousel → main grid + sidebar → missed posts
 *
 * @package Sinople
 * @since 2.0.0
 */

get_header();
?>

    <!-- Featured Section: sidebars + Swiper carousel -->
    <?php get_template_part( 'template-parts/content', 'featured' ); ?>

    <!-- Main Content + Sidebar -->
    <div class="container">
        <div class="content-area">
            <main id="main" class="site-main" role="main">
                <?php
                $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
                $main_query = new WP_Query( array(
                    'post_type'      => 'post',
                    'post_status'    => 'publish',
                    'posts_per_page' => get_option( 'posts_per_page' ),
                    'paged'          => $paged,
                ) );

                if ( $main_query->have_posts() ) :
                    ?>
                    <div class="card-grid">
                        <?php
                        while ( $main_query->have_posts() ) :
                            $main_query->the_post();
                            get_template_part( 'template-parts/content', 'card' );
                        endwhile;
                        ?>
                    </div>

                    <?php
                    $big = 999999999;
                    echo '<nav class="pagination" aria-label="' . esc_attr__( 'Posts pagination', 'sinople' ) . '">';
                    echo paginate_links( array(
                        'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                        'format'    => '?paged=%#%',
                        'current'   => max( 1, $paged ),
                        'total'     => $main_query->max_num_pages,
                        'prev_text' => '<i class="fa-solid fa-chevron-left" aria-hidden="true"></i> ' . esc_html__( 'Previous', 'sinople' ),
                        'next_text' => esc_html__( 'Next', 'sinople' ) . ' <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>',
                    ) );
                    echo '</nav>';
                    ?>

                <?php else : ?>
                    <p><?php esc_html_e( 'No posts found.', 'sinople' ); ?></p>
                <?php endif; wp_reset_postdata(); ?>
            </main>

            <?php get_sidebar(); ?>
        </div>
    </div>

    <!-- You May Have Missed -->
    <?php get_template_part( 'template-parts/footer/missed-posts' ); ?>

<?php get_footer();
