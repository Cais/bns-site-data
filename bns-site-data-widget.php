<?php
/*
Plugin Name: BNS Site Data Widget
Plugin URI: http://buynowshop.com/plugins/
Description: Show some basic site statistics.
Version: 0.1
Author: Edward Caissie
Author URI: http://edwardcaissie.com/
Textdomain: bns-sd
License: GNU General Public License v2
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/**
 * BNS Site Data WordPress plugin
 *
 * Show some basic stats about the site posts, pages, tags, etc.
 *
 * @package     BNS_Site_Data
 * @link        http://buynowshop.com/plugins/
 * @link        https://github.com/Cais/
 * @link        http://wordpress.org/extend/plugins/
 * @version     0.1
 * @author      Edward Caissie <edward.caissie@gmail.com>
 * @copyright   Copyright (c) 2012, Edward Caissie
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 2, as published by the
 * Free Software Foundation.
 *
 * You may NOT assume that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to:
 *
 *      Free Software Foundation, Inc.
 *      51 Franklin St, Fifth Floor
 *      Boston, MA  02110-1301  USA
 *
 * The license for this software can also likely be found here:
 * http://www.gnu.org/licenses/gpl-2.0.html
 */


/** Add function to the widgets_init hook. */
add_action( 'widgets_init', 'load_BNS_Site_Data_Widget' );

/** Function that registers our widget. */
function load_BNS_Site_Data_Widget() {
    register_widget( 'BNS_Site_Data_Widget' );
}


    /** Start Class Extension */
class BNS_Site_Data_Widget extends WP_Widget {

    /** Create Widget */
    function BNS_Site_Data_Widget() {
        /** Widget settings. */
        $widget_ops = array( 'classname' => 'bns-site-data', 'description' => __( 'Displays some site stuff.' ) );
        /** Widget control settings. */
        $control_ops = array( 'width' => 200, 'id_base' => 'bns-site-data' );
        /** Create the widget. */
        $this->WP_Widget( 'bns-site-data', 'BNS Site Data Widget', $widget_ops, $control_ops );
    }
    /** End: Create Widget */

}

// function BNS_Site_Data_Widget() {
function widget() {

    $data = array();
    $data['Posts']          = wp_count_posts( 'post' )->publish;
    $data['Pages']          = wp_count_posts( 'page' )->publish;
    $data['Categories']     = wp_count_terms( 'category' );
    $data['Tags']           = wp_count_terms( 'post_tag' );
    $data['Comments']       = wp_count_comments()->approved;
    $data['Attachments']    = wp_count_posts( 'attachment' )->inherit;

    $output = "\n";

    foreach ( $data as $label => $value )
        $output .= "\t" . number_format( $value ). " $label\n";

    $output .= "\n";

    return '<h2>Site Details</h2>' . '<pre>' . esc_html( $output ) . '</pre>';
}


// add_shortcode( 'bns_site_data_widget', 'BNS_Site_Data_Widget' );