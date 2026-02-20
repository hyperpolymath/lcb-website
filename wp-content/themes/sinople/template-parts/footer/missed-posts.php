<?php
/**
 * "You May Have Missed" — 4-column grid of recent posts
 *
 * @package Sinople
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$count = sinople_get_missed_posts_count();
$missed = get_posts( array(
    'numberposts'      => $count,
    'post_status'      => 'publish',
    'suppress_filters' => false,
) );

if ( empty( $missed ) ) {
    return;
}
?>

<section class="missed-section" aria-label="<?php esc_attr_e( 'You may have missed', 'sinople' ); ?>">
    <div class="container">
        <h2 class="section-title">
            <i class="fa-solid fa-newspaper" aria-hidden="true"></i>
            <?php esc_html_e( 'You May Have Missed', 'sinople' ); ?>
        </h2>

        <div class="missed-grid">
            <?php foreach ( $missed as $mp ) :
                $thumb = get_the_post_thumbnail_url( $mp, 'sinople-card' );
                $cats = get_the_category( $mp->ID );
                ?>
                <article class="post-card post-card-small">
                    <?php if ( $thumb ) : ?>
                        <div class="post-card-image">
                            <a href="<?php echo esc_url( get_permalink( $mp ) ); ?>">
                                <img src="<?php echo esc_url( $thumb ); ?>"
                                     alt="<?php echo esc_attr( get_the_title( $mp ) ); ?>"
                                     loading="lazy" width="600" height="338">
                            </a>
                            <?php if ( ! empty( $cats ) ) : ?>
                                <a class="category-badge"
                                   href="<?php echo esc_url( get_category_link( $cats[0]->term_id ) ); ?>">
                                    <?php echo esc_html( $cats[0]->name ); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="post-card-body">
                        <h3 class="post-card-title">
                            <a href="<?php echo esc_url( get_permalink( $mp ) ); ?>">
                                <?php echo esc_html( get_the_title( $mp ) ); ?>
                            </a>
                        </h3>
                        <div class="post-card-meta">
                            <span><i class="fa-regular fa-clock" aria-hidden="true"></i> <?php echo esc_html( get_the_date( '', $mp ) ); ?></span>
                        </div>
                    </div>
                </article>
            <?php endforeach; wp_reset_postdata(); ?>
        </div>
    </div>
</section>
