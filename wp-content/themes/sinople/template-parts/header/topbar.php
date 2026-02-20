<?php
/**
 * Top bar: date + breaking news ticker + social icons
 *
 * @package Sinople
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$show_ticker = sinople_show_ticker();
$social_links = sinople_get_social_links();
?>
<div class="topbar" role="complementary" aria-label="<?php esc_attr_e( 'Site information bar', 'sinople' ); ?>">
    <div class="container">
        <span class="topbar-date">
            <i class="fa-regular fa-calendar" aria-hidden="true"></i>
            <?php echo esc_html( date_i18n( get_option( 'date_format' ) ) ); ?>
        </span>

        <?php if ( $show_ticker ) : ?>
            <div class="topbar-ticker" aria-label="<?php esc_attr_e( 'Latest news', 'sinople' ); ?>">
                <span class="topbar-ticker-label">
                    <i class="fa-solid fa-fire" aria-hidden="true"></i>
                    <?php echo esc_html( sinople_get_ticker_label() ); ?>
                </span>
                <span class="topbar-ticker-items">
                    <?php
                    $ticker_posts = get_posts( array(
                        'numberposts'      => sinople_get_ticker_count(),
                        'post_status'      => 'publish',
                        'suppress_filters' => false,
                    ) );
                    $count = count( $ticker_posts );
                    foreach ( $ticker_posts as $i => $tp ) :
                        ?>
                        <a href="<?php echo esc_url( get_permalink( $tp ) ); ?>">
                            <?php echo esc_html( get_the_title( $tp ) ); ?>
                        </a>
                        <?php if ( $i < $count - 1 ) : ?>
                            <span class="ticker-sep" aria-hidden="true">/</span>
                        <?php endif; ?>
                    <?php endforeach; wp_reset_postdata(); ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if ( ! empty( $social_links ) ) : ?>
            <ul class="topbar-social" aria-label="<?php esc_attr_e( 'Social media links', 'sinople' ); ?>">
                <?php foreach ( $social_links as $slug => $url ) : ?>
                    <li>
                        <a href="<?php echo esc_url( $url ); ?>"
                           target="_blank"
                           rel="noopener noreferrer me"
                           aria-label="<?php echo esc_attr( ucfirst( $slug ) ); ?>">
                            <i class="<?php echo esc_attr( sinople_social_icon( $slug ) ); ?>" aria-hidden="true"></i>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
