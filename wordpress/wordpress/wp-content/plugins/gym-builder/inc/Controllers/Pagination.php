<?php
/**
 * @package GymBuilder
 */
namespace GymBuilder\Inc\Controllers;

use \GymBuilder\Inc\Controllers\Helpers\Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
class Pagination{

    /**
     * @return int
     */
    public static function get_page_number() {

        global $paged;

        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } else if (get_query_var('page')) {
            $paged = get_query_var('page');
        } else {
            $paged = 1;
        }

        return absint($paged);

    }

    public static function pagination() {
			
        $range = 1;
        $showItems = ($range * 2) + 1;
        $paged = self::get_page_number();

        if (!isset($pages)) {
            global $wp_query;
            $pages = $wp_query->max_num_pages;

            if (!$pages) {
                $pages = 1;
            }
        }

        Functions::get_template( "global/pagination", compact('paged', 'showItems', 'pages') );
    }

    public static function custom_query_pagination( $wp_query = null) {
			
        if( ! $wp_query ){
            global $wp_query;
        }
        
        /** Stop execution if there's only 1 page */
        if( $wp_query->max_num_pages <= 1 ){
            return;
        }			
        
        $paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
        $max   = intval( $wp_query->max_num_pages );

        /**	Add current page to the array */
        if ( $paged >= 1 )
            $links[] = $paged;

        /**	Add the pages around the current page to the array */
        if ( $paged >= 3 ) {
            $links[] = $paged - 1;
            $links[] = $paged - 2;
        }

        if ( ( $paged + 2 ) <= $max ) {
            $links[] = $paged + 2;
            $links[] = $paged + 1;
        }

        $html = null;
        $html .= '<div class="pagination-area"><ul>' . "\n";

        /**	Previous Post Link */
        if ( get_previous_posts_link() )
            $html .=sprintf( '<li>%s</li>' . "\n", get_previous_posts_link( '&laquo;' ) );

        /**	Link to first page, plus ellipses if necessary */
        if ( ! in_array( 1, $links ) ) {
            $class = 1 == $paged ? ' class="active"' : '';

            $html .=sprintf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );

            if ( ! in_array( 2, $links ) )
                $html .= '<li>...</li>';
        }

        /**	Link to current page, plus 2 pages in either direction if necessary */
        sort( $links );
        foreach ( (array) $links as $link ) {
            $class = $paged == $link ? ' class="active"' : '';
            $html .=sprintf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
        }

        /**	Link to last page, plus ellipses if necessary */
        if ( ! in_array( $max, $links ) ) {
            if ( ! in_array( $max - 1, $links ) )
                $html .= '<li>...</li>' . "\n";

            $class = $paged == $max ? ' class="active"' : '';
            $html .=sprintf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
        }

        /**	Next Post Link */
        if ( get_next_posts_link() )
            $html .=sprintf( '<li>%s</li>' . "\n", get_next_posts_link( '&raquo;' ) );

        $html .= '</ul></div>' . "\n";

        return $html;
       
    }
    public static function get_more_btn_html( $url, $more_btn_text ){
        $html = null;
        $html .='<div class="gym-builder-more-btn">';
        $html .= sprintf('<a href="%s" target="_blank">%s</a>',esc_url($url),esc_html($more_btn_text));
        $html .='</div>';
        return $html;
    }
}