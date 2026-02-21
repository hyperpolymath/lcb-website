<?php
/**
 * Single Post Template
 *
 * Article + sidebar + related posts + author box
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

                <article id="post-<?php the_ID(); ?>" <?php post_class( 'h-entry single-post' ); ?>>

                    <header class="entry-header">
                        <?php sinople_category_badge(); ?>
                        <h1 class="entry-title p-name"><?php the_title(); ?></h1>

                        <div class="entry-meta">
                            <?php sinople_posted_on(); ?>
                            <?php sinople_posted_by(); ?>
                            <?php sinople_reading_time(); ?>
                            <?php sinople_comment_count(); ?>
                        </div>
                    </header>

                    <?php if ( has_post_thumbnail() ) : ?>
                        <figure class="entry-thumbnail">
                            <?php the_post_thumbnail( 'sinople-featured', array(
                                'alt' => esc_attr( get_the_title() ),
                            ) ); ?>
                        </figure>
                    <?php endif; ?>

                    <div class="entry-content e-content">
                        <?php
                        the_content();
                        wp_link_pages( array(
                            'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'sinople' ),
                            'after'  => '</div>',
                        ) );
                        ?>
                    </div>

                    <footer class="entry-footer">
                        <?php sinople_entry_tags(); ?>
                    </footer>

                </article>

                <!-- Author Box -->
                <div class="author-box">
                    <div class="author-avatar">
                        <?php echo get_avatar( get_the_author_meta( 'ID' ), 80 ); ?>
                    </div>
                    <div class="author-info">
                        <h3 class="author-name">
                            <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
                                <?php the_author(); ?>
                            </a>
                        </h3>
                        <?php if ( get_the_author_meta( 'description' ) ) : ?>
                            <p class="author-bio"><?php echo esc_html( get_the_author_meta( 'description' ) ); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Related Posts -->
                <?php
                $categories = get_the_category();
                $related = array();
                if ( ! empty( $categories ) ) {
                    $related = get_posts( array(
                        'category__in'   => wp_list_pluck( $categories, 'term_id' ),
                        'post__not_in'   => array( get_the_ID() ),
                        'numberposts'    => 3,
                        'post_status'    => 'publish',
                    ) );
                }
                // Fallback to recent posts if no related in same category
                if ( empty( $related ) ) {
                    $related = get_posts( array(
                        'post__not_in'   => array( get_the_ID() ),
                        'numberposts'    => 3,
                        'post_status'    => 'publish',
                    ) );
                }

                    if ( ! empty( $related ) ) :
                        ?>
                        <section class="related-posts" aria-label="<?php esc_attr_e( 'Related posts', 'sinople' ); ?>">
                            <h2><?php esc_html_e( 'Related Posts', 'sinople' ); ?></h2>
                            <div class="row-3">
                                <?php foreach ( $related as $rp ) :
                                    setup_postdata( $rp );
                                    $thumb = get_the_post_thumbnail_url( $rp, 'sinople-card' );
                                    ?>
                                    <article class="post-card post-card-small">
                                        <?php if ( $thumb ) : ?>
                                            <div class="post-card-image">
                                                <a href="<?php echo esc_url( get_permalink( $rp ) ); ?>">
                                                    <img src="<?php echo esc_url( $thumb ); ?>"
                                                         alt="<?php echo esc_attr( get_the_title( $rp ) ); ?>"
                                                         loading="lazy" width="600" height="338">
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        <div class="post-card-body">
                                            <h3 class="post-card-title">
                                                <a href="<?php echo esc_url( get_permalink( $rp ) ); ?>">
                                                    <?php echo esc_html( get_the_title( $rp ) ); ?>
                                                </a>
                                            </h3>
                                            <div class="post-card-meta">
                                                <span><?php echo esc_html( get_the_date( '', $rp ) ); ?></span>
                                            </div>
                                        </div>
                                    </article>
                                <?php endforeach; wp_reset_postdata(); ?>
                            </div>
                        </section>
                    <?php endif; ?>

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

    <!-- You May Have Missed -->
    <?php get_template_part( 'template-parts/footer/missed-posts' ); ?>

<?php get_footer();
