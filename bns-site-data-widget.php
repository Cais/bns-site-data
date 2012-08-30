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

    /**
     * Overrides widget method from WP_Widget class
     * This is where the work is done
     *
     * @package BNS_Site_Data
     * @since   0.1
     *
     * @param   array $args - before_widget, after_widget, before_title, after_title
     * @param   array $instance - widget variables
     *
     * @internal $args vars are either drawn from the theme register_sidebar
     * definition, or are drawn from the defaults in WordPress core.
     *
     * @uses    apply_filters
     * @uses    wp_count_comments
     * @uses    wp_count_posts
     * @uses    wp_count_terms
     */
    function widget( $args, $instance ) {
        extract( $args );
        /** User-selected settings. */
        $title          = apply_filters( 'widget_title', $instance['title'] );
        $posts          = $instance['posts'];
        $pages          = $instance['pages'];
        $cats           = $instance['cats'];
        $tags           = $instance['tags'];
        $comments       = $instance['comments'];
        $attachments    = $instance['attachments'];


        /** Before widget (defined by themes). */
        /** @var $before_widget string - defined by theme */
        echo $before_widget;

        /** Widget title */
        if ( $title )
            /** @noinspection PhpUndefinedVariableInspection - IDE ONLY comment */
            echo $before_title . $title . $after_title;

        /** Choose which details are shown based on widget settings. */
        $data = array();
        if ( $posts )
            $data['Posts']          = wp_count_posts( 'post' )->publish;
        if ( $pages )
            $data['Pages']          = wp_count_posts( 'page' )->publish;
        if ( $cats )
            $data['Categories']     = wp_count_terms( 'category' );
        if ( $tags )
            $data['Tags']           = wp_count_terms( 'post_tag' );
        if ( $comments )
            $data['Comments']       = wp_count_comments()->approved;
        if ( $attachments )
            $data['Attachments']    = wp_count_posts( 'attachment' )->inherit;

        /** @var $output - initialize widget content holder into a list */
        $output = "<ul>";

        /** Add the values that exist as list items */
        foreach ( $data as $label => $value )
            $output .= "<li>" . number_format( $value ) . ' ' . $label . "</li>";

        /** Close the list */
        $output .= "</ul>";

        /** Write the list to the screen */
        echo $output;

        /** @var $after_widget (defined by themes). */
        echo $after_widget;
    }
    /** End: widget method override */

    /**
     * Overrides update method from WP_Widget class
     *
     * @package BNS_Site_Data
     * @since   0.1
     *
     * @param   array $new_instance
     * @param   array $old_instance
     *
     * @return  array
     */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        /** Strip tags (if needed) and update the widget settings. */
        $instance['title']          = strip_tags( $new_instance['title'] );
        $instance['posts']          = $new_instance['posts'];
        $instance['pages']          = $new_instance['pages'];
        $instance['cats']           = $new_instance['cats'];
        $instance['tags']           = $new_instance['tags'];
        $instance['comments']       = $new_instance['comments'];
        $instance['attachments']    = $new_instance['attachments'];

        return $instance;
    }
    /** End: update override */

    /**
     * Overrides form method from WP_Widget class
     *
     * @package BNS_Site_Data
     * @since   0.1
     *
     * @param   array $instance
     *
     * @uses    __
     * @uses    _e
     * @uses    checked
     * @uses    get_field_id
     * @uses    wp_parse_args
     *
     * @return  string|void
     */
    function form( $instance ) {
        /** Set default widget settings. */
        $defaults = array(
            'title'         => __( 'Site Data' ),
            'posts'         => true,
            'pages'         => true,
            'cats'          => true,
            'tags'          => true,
            'comments'      => true,
            'attachments'   => true,
        );
        $instance = wp_parse_args( (array) $instance, $defaults ); ?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked( (bool) $instance['posts'], true ); ?> id="<?php echo $this->get_field_id( 'posts' ); ?>" name="<?php echo $this->get_field_name( 'posts' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'posts' ); ?>"><?php _e( 'Show your posts count?' ); ?></label>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked( (bool) $instance['pages'], true ); ?> id="<?php echo $this->get_field_id( 'pages' ); ?>" name="<?php echo $this->get_field_name( 'pages' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'pages' ); ?>"><?php _e( 'Show your pages count?' ); ?></label>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked( (bool) $instance['cats'], true ); ?> id="<?php echo $this->get_field_id( 'cats' ); ?>" name="<?php echo $this->get_field_name( 'cats' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'cats' ); ?>"><?php _e( 'Show your categories count?' ); ?></label>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked( (bool) $instance['tags'], true ); ?> id="<?php echo $this->get_field_id( 'tags' ); ?>" name="<?php echo $this->get_field_name( 'tags' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'tags' ); ?>"><?php _e( 'Show your tags count?' ); ?></label>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked( (bool) $instance['comments'], true ); ?> id="<?php echo $this->get_field_id( 'comments' ); ?>" name="<?php echo $this->get_field_name( 'comments' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'comments' ); ?>"><?php _e( 'Show your comments count?' ); ?></label>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked( (bool) $instance['attachments'], true ); ?> id="<?php echo $this->get_field_id( 'attachments' ); ?>" name="<?php echo $this->get_field_name( 'attachments' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'attachments' ); ?>"><?php _e( 'Show your attachments count?' ); ?></label>
        </p>

    <?php
    }
    /** End: form override */

}
/** End: Class Extension */


/** Start Shortcode */
function BNS_Site_Data_Widget_Shortcode() {

    /** @var $data - array of site details */
    $data = array(
        'posts'       => wp_count_posts( 'post' )->publish,
        'pages'       => wp_count_posts( 'page' )->publish,
        'categories'  => wp_count_terms( 'category' ),
        'tags'        => wp_count_terms( 'post_tag' ),
        'comments'    => wp_count_comments()->approved,
        'attachments' => wp_count_posts( 'attachment' )->inherit,
    );

    /** @var $output - initialize output with a new line */
    $output = "\n";

    /** Append to output each of the site details */
    foreach ( $data as $label => $value )
        $output .= "\t" . number_format( $value ). " $label\n";

    /** Add a title and return the output for use in the shortcode */
    return '<h2>Site Details</h2>' . '<pre>' . $output . '</pre>';
}
add_shortcode( 'bns_site_data_widget', 'BNS_Site_Data_Widget_Shortcode' );
/** End: Shortcode */