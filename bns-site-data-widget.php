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

function BNS_Site_Data_Widget() {

    $data = array();
    $data['posts'] = wp_count_posts( 'post' )->publish;
    $data['pages'] = wp_count_posts( 'page' )->publish;
    $data['categories'] = wp_count_terms( 'category' );
    $data['tags'] = wp_count_terms( 'post_tag' );
    $data['comments'] = wp_count_comments()->approved;
    $data['attachments'] = wp_count_posts( 'attachment' )->inherit;

    $output = "\n";

    foreach ( $data as $label => $value )
        $output .= "\t" . number_format( $value ). " $label\n";

    $output .= "\n";

    return '<h2>Site Details</h2>' . '<pre>' . esc_html( $output ) . '</pre>';
}
add_shortcode( 'bns_site_data_widget', 'BNS_Site_Data_Widget' );