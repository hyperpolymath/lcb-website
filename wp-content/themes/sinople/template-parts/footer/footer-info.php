<?php
/**
 * Footer info bar: copyright + policy links + social
 *
 * @package Sinople
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$policy_links = function_exists( 'sinople_get_policy_links' ) ? sinople_get_policy_links() : array();
$social_links = sinople_get_social_links();
?>

<div class="footer-info container">
    <div class="footer-copyright">
        &copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>.
        <?php esc_html_e( 'All rights reserved.', 'sinople' ); ?>
    </div>

    <?php if ( ! empty( $policy_links ) ) : ?>
        <nav class="footer-policy-nav" aria-label="<?php esc_attr_e( 'Policy links', 'sinople' ); ?>">
            <ul class="footer-policy-links">
                <?php foreach ( $policy_links as $link ) : ?>
                    <li>
                        <a href="<?php echo esc_url( $link['url'] ); ?>">
                            <?php echo esc_html( $link['label'] ); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    <?php endif; ?>

    <?php if ( ! empty( $social_links ) ) : ?>
        <ul class="footer-social" aria-label="<?php esc_attr_e( 'Social media links', 'sinople' ); ?>">
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
