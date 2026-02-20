<?php
/**
 * Block Patterns for Sinople Theme.
 *
 * Registers Gutenberg block patterns for common NUJ content types.
 *
 * SPDX-License-Identifier: GPL-2.0-or-later
 *
 * @package Sinople
 */

/**
 * Register block pattern category and patterns.
 */
function sinople_register_block_patterns() {
    register_block_pattern_category( 'sinople-nuj', array(
        'label' => esc_html__( 'NUJ Content', 'sinople' ),
    ) );

    // 1. Event Listing
    register_block_pattern( 'sinople/event-listing', array(
        'title'       => esc_html__( 'Event Listing', 'sinople' ),
        'description' => esc_html__( 'An event announcement with date, time, location, and registration details.', 'sinople' ),
        'categories'  => array( 'sinople-nuj' ),
        'keywords'    => array( 'event', 'meeting', 'agm', 'training' ),
        'content'     => '<!-- wp:group {"className":"sinople-event-listing","style":{"border":{"left":{"color":"var(--color-primary)","width":"4px"}},"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|30","right":"var:preset|spacing|20"}}}} -->
<div class="wp-block-group sinople-event-listing" style="border-left-color:var(--color-primary);border-left-width:4px;padding-top:var(--preset--spacing--20);padding-right:var(--preset--spacing--20);padding-bottom:var(--preset--spacing--20);padding-left:var(--preset--spacing--30)">

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Event Title</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontWeight":"600"}}} -->
<p style="font-weight:600"><strong>Date:</strong> Thursday 24 April 2026 at 7:00pm<br><strong>Venue:</strong> NUJ Head Office, 72 Acton Street, London WC1X 9NB<br><strong>Type:</strong> In person &amp; online (hybrid)</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Brief description of the event, what attendees can expect, and why they should attend.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"vivid-green-cyan","className":"sinople-btn"} -->
<div class="wp-block-button sinople-btn"><a class="wp-block-button__link has-vivid-green-cyan-background-color has-background wp-element-button">Register Now</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

</div>
<!-- /wp:group -->',
    ) );

    // 2. Campaign Highlight
    register_block_pattern( 'sinople/campaign-highlight', array(
        'title'       => esc_html__( 'Campaign Highlight', 'sinople' ),
        'description' => esc_html__( 'A highlighted campaign or policy position with call-to-action links.', 'sinople' ),
        'categories'  => array( 'sinople-nuj' ),
        'keywords'    => array( 'campaign', 'policy', 'action', 'rights' ),
        'content'     => '<!-- wp:group {"className":"sinople-campaign","style":{"color":{"background":"var(--color-primary-pale)"},"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"8px"}}} -->
<div class="wp-block-group sinople-campaign" style="background-color:var(--color-primary-pale);border-radius:8px;padding-top:var(--preset--spacing--30);padding-right:var(--preset--spacing--30);padding-bottom:var(--preset--spacing--30);padding-left:var(--preset--spacing--30)">

<!-- wp:heading {"level":3,"style":{"color":{"text":"var(--color-primary)"}}} -->
<h3 class="wp-block-heading" style="color:var(--color-primary)">Campaign Title</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The NUJ is campaigning for fair treatment of journalists. Here is what we are asking for and how you can support the campaign.</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list">
<li><strong>Key demand one</strong> — Brief explanation of what this means</li>
<li><strong>Key demand two</strong> — Brief explanation of what this means</li>
<li><strong>Key demand three</strong> — Brief explanation of what this means</li>
</ul>
<!-- /wp:list -->

<!-- wp:paragraph {"style":{"typography":{"fontWeight":"600"}}} -->
<p style="font-weight:600">Take action: <a href="#">Write to your MP</a> | <a href="#">Sign the petition</a> | <a href="#">Share on social media</a></p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->',
    ) );

    // 3. Call to Action
    register_block_pattern( 'sinople/call-to-action', array(
        'title'       => esc_html__( 'Call to Action', 'sinople' ),
        'description' => esc_html__( 'A prominent call-to-action block for joining the NUJ, reporting issues, or contacting the branch.', 'sinople' ),
        'categories'  => array( 'sinople-nuj' ),
        'keywords'    => array( 'cta', 'join', 'contact', 'action' ),
        'content'     => '<!-- wp:group {"className":"sinople-cta","style":{"color":{"background":"var(--color-primary)","text":"#ffffff"},"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"8px"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group sinople-cta" style="color:#ffffff;background-color:var(--color-primary);border-radius:8px;padding-top:var(--preset--spacing--40);padding-right:var(--preset--spacing--40);padding-bottom:var(--preset--spacing--40);padding-left:var(--preset--spacing--40)">

<!-- wp:heading {"textAlign":"center","level":3,"style":{"color":{"text":"#ffffff"}}} -->
<h3 class="wp-block-heading has-text-align-center" style="color:#ffffff">Join the NUJ Today</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">The NUJ is the voice for journalists across the UK and Ireland. Join us to protect your rights, access training, and be part of a community that fights for fair pay and press freedom.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"style":{"color":{"background":"#ffffff","text":"var(--color-primary)"}}} -->
<div class="wp-block-button"><a class="wp-block-button__link has-text-color has-background wp-element-button" style="color:var(--color-primary);background-color:#ffffff">Join Now</a></div>
<!-- /wp:button -->
<!-- wp:button {"className":"is-style-outline","style":{"color":{"text":"#ffffff"},"border":{"color":"#ffffff"}}} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link has-text-color has-border-color wp-element-button" style="color:#ffffff;border-color:#ffffff">Learn More</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

</div>
<!-- /wp:group -->',
    ) );

    // 4. Member Notice
    register_block_pattern( 'sinople/member-notice', array(
        'title'       => esc_html__( 'Member Notice', 'sinople' ),
        'description' => esc_html__( 'An important notice or advisory for NUJ members with steps to follow.', 'sinople' ),
        'categories'  => array( 'sinople-nuj' ),
        'keywords'    => array( 'notice', 'advisory', 'member', 'alert' ),
        'content'     => '<!-- wp:group {"className":"sinople-member-notice","style":{"color":{"background":"#fff8e1"},"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|30","right":"var:preset|spacing|20"}},"border":{"left":{"color":"#f9a825","width":"4px"},"radius":"4px"}}} -->
<div class="wp-block-group sinople-member-notice" style="border-left-color:#f9a825;border-left-width:4px;border-radius:4px;background-color:#fff8e1;padding-top:var(--preset--spacing--20);padding-right:var(--preset--spacing--20);padding-bottom:var(--preset--spacing--20);padding-left:var(--preset--spacing--30)">

<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading">Notice for Members</h4>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Important information that NUJ members should be aware of. This could be a change in policy, a deadline, or advice about a developing situation.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":5} -->
<h5 class="wp-block-heading">What to do</h5>
<!-- /wp:heading -->

<!-- wp:list {"ordered":true} -->
<ol class="wp-block-list">
<li>First step members should take</li>
<li>Second step members should take</li>
<li>Third step — contact the branch if you need support</li>
</ol>
<!-- /wp:list -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
<p style="font-size:14px"><em>For further advice, contact the branch secretary or your chapel representative.</em></p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->',
    ) );
}
add_action( 'init', 'sinople_register_block_patterns' );
