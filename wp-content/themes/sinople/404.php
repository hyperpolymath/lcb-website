<?php
/**
 * 404 Not Found Template
 *
 * @package Sinople
 * @since 2.0.0
 */

get_header();
?>

<div class="container">
    <div class="content-area no-sidebar">
        <main id="main" class="site-main" role="main">

            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e( '404: Page Not Found', 'sinople' ); ?></h1>
            </header>

            <div class="page-content">
                <p><?php esc_html_e( 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'sinople' ); ?></p>

                <h2><?php esc_html_e( 'Try searching:', 'sinople' ); ?></h2>
                <?php get_search_form(); ?>

                <h2><?php esc_html_e( 'Recent Posts:', 'sinople' ); ?></h2>
                <div class="card-grid">
                    <?php
                    $recent = get_posts( array(
                        'post_type'   => 'post',
                        'numberposts' => 4,
                        'post_status' => 'publish',
                    ) );
                    foreach ( $recent as $post ) :
                        setup_postdata( $post );
                        get_template_part( 'template-parts/content', 'card' );
                    endforeach;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>

        </main>
    </div>
</div>

<?php get_footer();
