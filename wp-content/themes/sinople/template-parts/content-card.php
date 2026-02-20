<?php
/**
 * Post card template part (for grids)
 *
 * @package Sinople
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card h-entry' ); ?>>
    <?php if ( has_post_thumbnail() ) : ?>
        <div class="post-card-image">
            <a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                <?php the_post_thumbnail( 'sinople-card', array(
                    'loading' => 'lazy',
                    'alt'     => esc_attr( get_the_title() ),
                ) ); ?>
            </a>
            <?php sinople_category_badge(); ?>
        </div>
    <?php endif; ?>

    <div class="post-card-body">
        <h3 class="post-card-title p-name">
            <a href="<?php the_permalink(); ?>" class="u-url" rel="bookmark">
                <?php the_title(); ?>
            </a>
        </h3>

        <p class="post-card-excerpt p-summary">
            <?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?>
        </p>

        <div class="post-card-meta">
            <?php sinople_posted_on(); ?>
            <?php sinople_posted_by(); ?>
            <?php sinople_reading_time(); ?>
        </div>
    </div>
</article>
