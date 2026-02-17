    <footer id="colophon" class="site-footer" role="contentinfo">
        <div class="footer-meta">
            <p>&copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?></p>

            <?php $policy_links = function_exists( 'sinople_get_policy_links' ) ? sinople_get_policy_links() : array(); ?>
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
        </div>
    </footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
