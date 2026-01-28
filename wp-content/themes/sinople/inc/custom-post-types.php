<?php
/**
 * Custom Post Types for Sinople Theme
 *
 * Registers Constructs and Entanglements custom post types
 * for semantic web functionality.
 *
 * @package Sinople
 * @since 1.0.0
 */

declare(strict_types=1);

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Construct Custom Post Type
 *
 * Constructs represent abstract concepts, entities, or ideas
 * within the Sinople semantic universe.
 */
function sinople_register_construct_cpt() {
    $labels = array(
        'name'                  => _x( 'Constructs', 'Post type general name', 'sinople' ),
        'singular_name'         => _x( 'Construct', 'Post type singular name', 'sinople' ),
        'menu_name'             => _x( 'Constructs', 'Admin Menu text', 'sinople' ),
        'name_admin_bar'        => _x( 'Construct', 'Add New on Toolbar', 'sinople' ),
        'add_new'               => __( 'Add New', 'sinople' ),
        'add_new_item'          => __( 'Add New Construct', 'sinople' ),
        'new_item'              => __( 'New Construct', 'sinople' ),
        'edit_item'             => __( 'Edit Construct', 'sinople' ),
        'view_item'             => __( 'View Construct', 'sinople' ),
        'all_items'             => __( 'All Constructs', 'sinople' ),
        'search_items'          => __( 'Search Constructs', 'sinople' ),
        'parent_item_colon'     => __( 'Parent Constructs:', 'sinople' ),
        'not_found'             => __( 'No constructs found.', 'sinople' ),
        'not_found_in_trash'    => __( 'No constructs found in Trash.', 'sinople' ),
        'featured_image'        => _x( 'Construct Image', 'Overrides the "Featured Image" phrase', 'sinople' ),
        'set_featured_image'    => _x( 'Set construct image', 'Overrides the "Set featured image" phrase', 'sinople' ),
        'remove_featured_image' => _x( 'Remove construct image', 'Overrides the "Remove featured image" phrase', 'sinople' ),
        'use_featured_image'    => _x( 'Use as construct image', 'Overrides the "Use as featured image" phrase', 'sinople' ),
        'archives'              => _x( 'Construct archives', 'The post type archive label', 'sinople' ),
        'insert_into_item'      => _x( 'Insert into construct', 'Overrides the "Insert into post" phrase', 'sinople' ),
        'uploaded_to_this_item' => _x( 'Uploaded to this construct', 'Overrides the "Uploaded to this post" phrase', 'sinople' ),
        'filter_items_list'     => _x( 'Filter constructs list', 'Screen reader text for the filter links', 'sinople' ),
        'items_list_navigation' => _x( 'Constructs list navigation', 'Screen reader text for the pagination', 'sinople' ),
        'items_list'            => _x( 'Constructs list', 'Screen reader text for the items list', 'sinople' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'constructs' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-networking',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions' ),
        'show_in_rest'       => true,
        'rest_base'          => 'constructs',
        'description'        => __( 'Abstract concepts, entities, or ideas within the Sinople semantic universe', 'sinople' ),
    );

    register_post_type( 'sinople_construct', $args );
}
add_action( 'init', 'sinople_register_construct_cpt' );

/**
 * Register Entanglement Custom Post Type
 *
 * Entanglements represent relationships, connections, or interactions
 * between two or more constructs.
 */
function sinople_register_entanglement_cpt() {
    $labels = array(
        'name'                  => _x( 'Entanglements', 'Post type general name', 'sinople' ),
        'singular_name'         => _x( 'Entanglement', 'Post type singular name', 'sinople' ),
        'menu_name'             => _x( 'Entanglements', 'Admin Menu text', 'sinople' ),
        'name_admin_bar'        => _x( 'Entanglement', 'Add New on Toolbar', 'sinople' ),
        'add_new'               => __( 'Add New', 'sinople' ),
        'add_new_item'          => __( 'Add New Entanglement', 'sinople' ),
        'new_item'              => __( 'New Entanglement', 'sinople' ),
        'edit_item'             => __( 'Edit Entanglement', 'sinople' ),
        'view_item'             => __( 'View Entanglement', 'sinople' ),
        'all_items'             => __( 'All Entanglements', 'sinople' ),
        'search_items'          => __( 'Search Entanglements', 'sinople' ),
        'not_found'             => __( 'No entanglements found.', 'sinople' ),
        'not_found_in_trash'    => __( 'No entanglements found in Trash.', 'sinople' ),
        'archives'              => _x( 'Entanglement archives', 'The post type archive label', 'sinople' ),
        'filter_items_list'     => _x( 'Filter entanglements list', 'Screen reader text', 'sinople' ),
        'items_list_navigation' => _x( 'Entanglements list navigation', 'Screen reader text', 'sinople' ),
        'items_list'            => _x( 'Entanglements list', 'Screen reader text', 'sinople' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'entanglements' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 21,
        'menu_icon'          => 'dashicons-admin-links',
        'supports'           => array( 'title', 'editor', 'custom-fields', 'revisions' ),
        'show_in_rest'       => true,
        'rest_base'          => 'entanglements',
        'description'        => __( 'Relationships and connections between constructs', 'sinople' ),
    );

    register_post_type( 'sinople_entanglement', $args );
}
add_action( 'init', 'sinople_register_entanglement_cpt' );

/**
 * Add custom meta boxes for Constructs
 */
function sinople_construct_meta_boxes() {
    add_meta_box(
        'sinople_construct_meta',
        __( 'Construct Metadata', 'sinople' ),
        'sinople_construct_meta_callback',
        'sinople_construct',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'sinople_construct_meta_boxes' );

/**
 * Construct meta box callback
 */
function sinople_construct_meta_callback( $post ) {
    wp_nonce_field( 'sinople_construct_meta', 'sinople_construct_meta_nonce' );

    $gloss = get_post_meta( $post->ID, '_sinople_gloss', true );
    $complexity = get_post_meta( $post->ID, '_sinople_complexity', true );
    $construct_type = get_post_meta( $post->ID, '_sinople_construct_type', true );
    $rdf_iri = get_post_meta( $post->ID, '_sinople_rdf_iri', true );
    ?>
    <div class="sinople-meta-fields">
        <p>
            <label for="sinople_gloss"><?php esc_html_e( 'Gloss (Short Explanation):', 'sinople' ); ?></label><br>
            <textarea id="sinople_gloss" name="sinople_gloss" rows="3" style="width:100%;"><?php echo esc_textarea( $gloss ); ?></textarea>
            <span class="description"><?php esc_html_e( 'A brief annotation or explanation of this construct', 'sinople' ); ?></span>
        </p>
        <p>
            <label for="sinople_complexity"><?php esc_html_e( 'Complexity (0-10):', 'sinople' ); ?></label><br>
            <input type="number" id="sinople_complexity" name="sinople_complexity" value="<?php echo esc_attr( $complexity ); ?>" min="0" max="10" style="width:100px;">
            <span class="description"><?php esc_html_e( 'Subjective complexity rating', 'sinople' ); ?></span>
        </p>
        <p>
            <label for="sinople_construct_type"><?php esc_html_e( 'Construct Type:', 'sinople' ); ?></label><br>
            <select id="sinople_construct_type" name="sinople_construct_type" style="width:200px;">
                <option value=""><?php esc_html_e( 'Select type', 'sinople' ); ?></option>
                <option value="philosophical" <?php selected( $construct_type, 'philosophical' ); ?>><?php esc_html_e( 'Philosophical', 'sinople' ); ?></option>
                <option value="scientific" <?php selected( $construct_type, 'scientific' ); ?>><?php esc_html_e( 'Scientific', 'sinople' ); ?></option>
                <option value="mathematical" <?php selected( $construct_type, 'mathematical' ); ?>><?php esc_html_e( 'Mathematical', 'sinople' ); ?></option>
                <option value="linguistic" <?php selected( $construct_type, 'linguistic' ); ?>><?php esc_html_e( 'Linguistic', 'sinople' ); ?></option>
                <option value="social" <?php selected( $construct_type, 'social' ); ?>><?php esc_html_e( 'Social', 'sinople' ); ?></option>
                <option value="other" <?php selected( $construct_type, 'other' ); ?>><?php esc_html_e( 'Other', 'sinople' ); ?></option>
            </select>
        </p>
        <p>
            <label for="sinople_rdf_iri"><?php esc_html_e( 'RDF IRI:', 'sinople' ); ?></label><br>
            <input type="url" id="sinople_rdf_iri" name="sinople_rdf_iri" value="<?php echo esc_url( $rdf_iri ); ?>" style="width:100%;" placeholder="http://sinople.org/constructs/example">
            <span class="description"><?php esc_html_e( 'Unique IRI for this construct in RDF graph', 'sinople' ); ?></span>
        </p>
    </div>
    <?php
}

/**
 * Save construct meta data
 */
function sinople_save_construct_meta( $post_id ) {
    // Check nonce
    if ( ! isset( $_POST['sinople_construct_meta_nonce'] ) || ! wp_verify_nonce( $_POST['sinople_construct_meta_nonce'], 'sinople_construct_meta' ) ) {
        return;
    }

    // Check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check permissions
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Save gloss
    if ( isset( $_POST['sinople_gloss'] ) ) {
        update_post_meta( $post_id, '_sinople_gloss', sanitize_textarea_field( $_POST['sinople_gloss'] ) );
    }

    // Save complexity
    if ( isset( $_POST['sinople_complexity'] ) ) {
        $complexity = intval( $_POST['sinople_complexity'] );
        if ( $complexity >= 0 && $complexity <= 10 ) {
            update_post_meta( $post_id, '_sinople_complexity', $complexity );
        }
    }

    // Save construct type
    if ( isset( $_POST['sinople_construct_type'] ) ) {
        update_post_meta( $post_id, '_sinople_construct_type', sanitize_text_field( $_POST['sinople_construct_type'] ) );
    }

    // Save RDF IRI
    if ( isset( $_POST['sinople_rdf_iri'] ) ) {
        update_post_meta( $post_id, '_sinople_rdf_iri', esc_url_raw( $_POST['sinople_rdf_iri'] ) );
    }
}
add_action( 'save_post_sinople_construct', 'sinople_save_construct_meta' );

/**
 * Add custom meta boxes for Entanglements
 */
function sinople_entanglement_meta_boxes() {
    add_meta_box(
        'sinople_entanglement_meta',
        __( 'Entanglement Metadata', 'sinople' ),
        'sinople_entanglement_meta_callback',
        'sinople_entanglement',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'sinople_entanglement_meta_boxes' );

/**
 * Entanglement meta box callback
 */
function sinople_entanglement_meta_callback( $post ) {
    wp_nonce_field( 'sinople_entanglement_meta', 'sinople_entanglement_meta_nonce' );

    $source = get_post_meta( $post->ID, '_sinople_source', true );
    $target = get_post_meta( $post->ID, '_sinople_target', true );
    $relationship_type = get_post_meta( $post->ID, '_sinople_relationship_type', true );
    $strength = get_post_meta( $post->ID, '_sinople_strength', true );
    $bidirectional = get_post_meta( $post->ID, '_sinople_bidirectional', true );

    // Get all constructs for dropdowns
    $constructs = get_posts( array(
        'post_type'      => 'sinople_construct',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ) );
    ?>
    <div class="sinople-meta-fields">
        <p>
            <label for="sinople_source"><?php esc_html_e( 'Source Construct:', 'sinople' ); ?></label><br>
            <select id="sinople_source" name="sinople_source" style="width:100%;">
                <option value=""><?php esc_html_e( 'Select source construct', 'sinople' ); ?></option>
                <?php foreach ( $constructs as $construct ) : ?>
                    <option value="<?php echo esc_attr( $construct->ID ); ?>" <?php selected( $source, $construct->ID ); ?>>
                        <?php echo esc_html( $construct->post_title ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="sinople_target"><?php esc_html_e( 'Target Construct:', 'sinople' ); ?></label><br>
            <select id="sinople_target" name="sinople_target" style="width:100%;">
                <option value=""><?php esc_html_e( 'Select target construct', 'sinople' ); ?></option>
                <?php foreach ( $constructs as $construct ) : ?>
                    <option value="<?php echo esc_attr( $construct->ID ); ?>" <?php selected( $target, $construct->ID ); ?>>
                        <?php echo esc_html( $construct->post_title ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="sinople_relationship_type"><?php esc_html_e( 'Relationship Type:', 'sinople' ); ?></label><br>
            <input type="text" id="sinople_relationship_type" name="sinople_relationship_type" value="<?php echo esc_attr( $relationship_type ); ?>" style="width:100%;" placeholder="<?php esc_attr_e( 'e.g., depends on, contradicts, complements', 'sinople' ); ?>">
        </p>
        <p>
            <label for="sinople_strength"><?php esc_html_e( 'Strength (0.0-1.0):', 'sinople' ); ?></label><br>
            <input type="number" id="sinople_strength" name="sinople_strength" value="<?php echo esc_attr( $strength ); ?>" min="0" max="1" step="0.01" style="width:100px;">
            <span class="description"><?php esc_html_e( 'Strength of the relationship', 'sinople' ); ?></span>
        </p>
        <p>
            <label for="sinople_bidirectional">
                <input type="checkbox" id="sinople_bidirectional" name="sinople_bidirectional" value="1" <?php checked( $bidirectional, '1' ); ?>>
                <?php esc_html_e( 'Bidirectional relationship', 'sinople' ); ?>
            </label>
        </p>
    </div>
    <?php
}

/**
 * Save entanglement meta data
 */
function sinople_save_entanglement_meta( $post_id ) {
    // Check nonce
    if ( ! isset( $_POST['sinople_entanglement_meta_nonce'] ) || ! wp_verify_nonce( $_POST['sinople_entanglement_meta_nonce'], 'sinople_entanglement_meta' ) ) {
        return;
    }

    // Check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check permissions
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Save source
    if ( isset( $_POST['sinople_source'] ) ) {
        update_post_meta( $post_id, '_sinople_source', intval( $_POST['sinople_source'] ) );
    }

    // Save target
    if ( isset( $_POST['sinople_target'] ) ) {
        update_post_meta( $post_id, '_sinople_target', intval( $_POST['sinople_target'] ) );
    }

    // Save relationship type
    if ( isset( $_POST['sinople_relationship_type'] ) ) {
        update_post_meta( $post_id, '_sinople_relationship_type', sanitize_text_field( $_POST['sinople_relationship_type'] ) );
    }

    // Save strength
    if ( isset( $_POST['sinople_strength'] ) ) {
        $strength = floatval( $_POST['sinople_strength'] );
        if ( $strength >= 0 && $strength <= 1 ) {
            update_post_meta( $post_id, '_sinople_strength', $strength );
        }
    }

    // Save bidirectional
    $bidirectional = isset( $_POST['sinople_bidirectional'] ) ? '1' : '0';
    update_post_meta( $post_id, '_sinople_bidirectional', $bidirectional );
}
add_action( 'save_post_sinople_entanglement', 'sinople_save_entanglement_meta' );

/**
 * Add custom columns to Constructs list
 */
function sinople_construct_columns( $columns ) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['construct_type'] = __( 'Type', 'sinople' );
    $new_columns['complexity'] = __( 'Complexity', 'sinople' );
    $new_columns['date'] = $columns['date'];
    return $new_columns;
}
add_filter( 'manage_sinople_construct_posts_columns', 'sinople_construct_columns' );

/**
 * Populate custom columns for Constructs
 */
function sinople_construct_custom_column( $column, $post_id ) {
    switch ( $column ) {
        case 'construct_type':
            $type = get_post_meta( $post_id, '_sinople_construct_type', true );
            echo $type ? esc_html( ucfirst( $type ) ) : '—';
            break;
        case 'complexity':
            $complexity = get_post_meta( $post_id, '_sinople_complexity', true );
            echo $complexity !== '' ? esc_html( $complexity ) . '/10' : '—';
            break;
    }
}
add_action( 'manage_sinople_construct_posts_custom_column', 'sinople_construct_custom_column', 10, 2 );

/**
 * Make custom columns sortable
 */
function sinople_construct_sortable_columns( $columns ) {
    $columns['construct_type'] = 'construct_type';
    $columns['complexity'] = 'complexity';
    return $columns;
}
add_filter( 'manage_edit-sinople_construct_sortable_columns', 'sinople_construct_sortable_columns' );
