<?php
/**
 * Custom Nav Menu Walker for Sinople Theme
 *
 * Adds dropdown markup with ARIA attributes for accessible navigation.
 *
 * @package Sinople
 * @since 2.0.0
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Sinople Dropdown Nav Walker
 */
class Sinople_Nav_Walker extends Walker_Nav_Menu {

    /**
     * Starts a sub-menu list.
     */
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "\n{$indent}<ul class=\"sub-menu\" role=\"menu\">\n";
    }

    /**
     * Start element output.
     */
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        $has_children = in_array( 'menu-item-has-children', $classes, true );
        if ( $has_children ) {
            $classes[] = 'has-dropdown';
        }

        $class_names = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        $id_attr = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
        $id_attr = $id_attr ? ' id="' . esc_attr( $id_attr ) . '"' : '';

        $output .= $indent . '<li' . $id_attr . $class_names . ' role="none">';

        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target ) ? $item->target : '';
        $atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
        $atts['href']   = ! empty( $item->url ) ? $item->url : '';
        $atts['role']   = 'menuitem';

        if ( ! empty( $item->current ) ) {
            $atts['aria-current'] = 'page';
        }

        if ( $has_children ) {
            $atts['aria-haspopup'] = 'true';
            $atts['aria-expanded'] = 'false';
        }

        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title = apply_filters( 'the_title', $item->title, $item->ID );
        $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

        $item_output = '';
        if ( $args ) {
            $item_output = $args->before ?? '';
            $item_output .= '<a' . $attributes . '>';
            $item_output .= ( $args->link_before ?? '' ) . $title . ( $args->link_after ?? '' );
            $item_output .= '</a>';
            $item_output .= $args->after ?? '';
        }

        if ( $has_children && $depth === 0 ) {
            $item_output .= '<button class="dropdown-toggle" aria-label="' .
                esc_attr( sprintf( __( 'Open submenu for %s', 'sinople' ), wp_strip_all_tags( $title ) ) ) .
                '" aria-expanded="false"><i class="fa-solid fa-chevron-down" aria-hidden="true"></i></button>';
        }

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}
