<?php
/**
 * Featured section: sidebars + Swiper carousel
 *
 * @package Sinople
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$featured_count = sinople_get_featured_count();
$featured_posts = get_posts( array(
    'numberposts'      => $featured_count,
    'post_status'      => 'publish',
    'suppress_filters' => false,
    'meta_key'         => '_thumbnail_id', // Only posts with featured images
) );

// Fallback if not enough posts with images
if ( count( $featured_posts ) < 2 ) {
    $featured_posts = get_posts( array(
        'numberposts'      => $featured_count,
        'post_status'      => 'publish',
        'suppress_filters' => false,
    ) );
}

if ( empty( $featured_posts ) ) {
    return;
}

// Split: main carousel gets first posts, sidebars get the rest
$carousel_posts = array_slice( $featured_posts, 0, min( 5, count( $featured_posts ) ) );
$sidebar_posts = array_slice( $featured_posts, 0, 4 );
?>

<section class="featured-section" aria-label="<?php esc_attr_e( 'Featured stories', 'sinople' ); ?>">
    <div class="container">
        <div class="featured-grid">

            <!-- Left sidebar -->
            <div class="featured-sidebar-left">
                <?php if ( is_active_sidebar( 'featured-left' ) ) : ?>
                    <?php dynamic_sidebar( 'featured-left' ); ?>
                <?php else : ?>
                    <?php
                    foreach ( array_slice( $sidebar_posts, 0, 4 ) as $sp ) :
                        $sp_thumb = get_the_post_thumbnail_url( $sp, 'sinople-ticker' );
                        ?>
                        <div class="featured-sidebar-post">
                            <?php if ( $sp_thumb ) : ?>
                                <div class="featured-sidebar-post-image">
                                    <img src="<?php echo esc_url( $sp_thumb ); ?>"
                                         alt="<?php echo esc_attr( get_the_title( $sp ) ); ?>"
                                         loading="lazy" width="80" height="80">
                                </div>
                            <?php endif; ?>
                            <div class="featured-sidebar-post-content">
                                <h3 class="featured-sidebar-post-title">
                                    <a href="<?php echo esc_url( get_permalink( $sp ) ); ?>">
                                        <?php echo esc_html( get_the_title( $sp ) ); ?>
                                    </a>
                                </h3>
                                <span class="featured-sidebar-post-date">
                                    <?php echo esc_html( get_the_date( '', $sp ) ); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Swiper Carousel -->
            <div class="featured-carousel">
                <div class="swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ( $carousel_posts as $cp ) :
                            $thumb = get_the_post_thumbnail_url( $cp, 'sinople-featured' );
                            $cats = get_the_category( $cp->ID );
                            ?>
                            <div class="swiper-slide">
                                <?php if ( $thumb ) : ?>
                                    <img src="<?php echo esc_url( $thumb ); ?>"
                                         alt="<?php echo esc_attr( get_the_title( $cp ) ); ?>"
                                         loading="lazy" width="1200" height="675">
                                <?php else : ?>
                                    <div style="background: var(--color-primary-pale); width: 100%; height: 100%;"></div>
                                <?php endif; ?>
                                <div class="featured-slide-overlay">
                                    <?php if ( ! empty( $cats ) ) : ?>
                                        <a class="featured-slide-category"
                                           href="<?php echo esc_url( get_category_link( $cats[0]->term_id ) ); ?>">
                                            <?php echo esc_html( $cats[0]->name ); ?>
                                        </a>
                                    <?php endif; ?>
                                    <h2 class="featured-slide-title">
                                        <a href="<?php echo esc_url( get_permalink( $cp ) ); ?>">
                                            <?php echo esc_html( get_the_title( $cp ) ); ?>
                                        </a>
                                    </h2>
                                    <div class="featured-slide-meta">
                                        <?php echo esc_html( get_the_date( '', $cp ) ); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </div>

            <!-- Right sidebar -->
            <div class="featured-sidebar-right">
                <?php if ( is_active_sidebar( 'featured-right' ) ) : ?>
                    <?php dynamic_sidebar( 'featured-right' ); ?>
                <?php else : ?>
                    <?php
                    foreach ( array_slice( $sidebar_posts, 0, 4 ) as $sp ) :
                        $sp_thumb = get_the_post_thumbnail_url( $sp, 'sinople-ticker' );
                        ?>
                        <div class="featured-sidebar-post">
                            <?php if ( $sp_thumb ) : ?>
                                <div class="featured-sidebar-post-image">
                                    <img src="<?php echo esc_url( $sp_thumb ); ?>"
                                         alt="<?php echo esc_attr( get_the_title( $sp ) ); ?>"
                                         loading="lazy" width="80" height="80">
                                </div>
                            <?php endif; ?>
                            <div class="featured-sidebar-post-content">
                                <h3 class="featured-sidebar-post-title">
                                    <a href="<?php echo esc_url( get_permalink( $sp ) ); ?>">
                                        <?php echo esc_html( get_the_title( $sp ) ); ?>
                                    </a>
                                </h3>
                                <span class="featured-sidebar-post-date">
                                    <?php echo esc_html( get_the_date( '', $sp ) ); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>
