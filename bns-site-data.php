<?php
/*
Plugin Name: BNS Site Data
Plugin URI: http://buynowshop.com/plugins/
Description: Show some basic site statistics.
Version: 0.1
Author: Edward Caissie
Author URI: http://edwardcaissie.com/
Text Domain: bns-sd
License: GNU General Public License v2
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/**
 * BNS Site Data WordPress plugin
 *
 * Display various site statistics (read: counts) such as: posts, pages,
 * categories, tags, comments, and attachments. Each site statistic can be
 * toggled via a checkbox in the widget option panel.
 *
 * @package     BNS_Site_Data
 * @link        http://buynowshop.com/plugins/bns-site-data
 * @link        https://github.com/Cais/bns-site-data
 * @link        http://wordpress.org/extend/plugins/bns-site-data
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

/**
 * Enqueue Plugin Scripts and Styles
 * Adds plugin scripts and stylesheet; allows for custom stylesheet to be added
 * by end-user. These stylesheets will only affect public facing output.
 *
 * @package BNS_Site_Data
 * @since   0.1
 *
 * @uses    get_plugin_data
 * @uses    plugin_dir_path
 * @uses    plugin_dir_url
 * @uses    wp_enqueue_script
 * @uses    wp_enqueue_style
 *
 * @internal jQuery is enqueued as a dependency
 * @internal Used with action hook: wp_enqueue_scripts
 */
function BNS_Site_Data_Scripts_and_Styles() {
    /** @var $bns_sd_data - holds plugin data */
    $bns_sd_data = get_plugin_data( __FILE__ );
    /** Enqueue Scripts */
    wp_enqueue_script( 'BNS-Site-Data-Scripts', plugin_dir_url( __FILE__ ) . 'bns-site-data-scripts.js', array( 'jquery' ), $bns_sd_data['Version'], 'true' );
    /** Enqueue Style Sheets */
    wp_enqueue_style( 'BNS-Site-Data-Style', plugin_dir_url( __FILE__ ) . 'bns-site-data-style.css', array(), $bns_sd_data['Version'], 'screen' );
    /** Check if custom stylesheet is readable (exists) */
    if ( is_readable( plugin_dir_path( __FILE__ ) . 'bns-site-data-custom-style.css' ) ) {
        wp_enqueue_style( 'BNS-Site-Data-Custom-Style', plugin_dir_url( __FILE__ ) . 'bns-site-data-custom-style.css', array(), $bns_sd_data['Version'], 'screen' );
    }
}
add_action( 'wp_enqueue_scripts', 'BNS_Site_Data_Scripts_and_Styles' );
/** End: Enqueue Plugin Scripts and Styles */


/** Start Class Extension */
class BNS_Site_Data_Widget extends WP_Widget {

    /** Create Widget */
    function BNS_Site_Data_Widget() {
        /** Widget settings. */
        $widget_ops = array( 'classname' => 'bns-site-data', 'description' => __( 'Displays some site stuff.', 'bns-sd' ) );
        /** Widget control settings. */
        $control_ops = array( 'width' => 200, 'id_base' => 'bns-site-data' );
        /** Create the widget. */
        $this->WP_Widget( 'bns-site-data', 'BNS Site Data', $widget_ops, $control_ops );
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

        /**
         * Initialize the data array; and, only add the values based on the
         * widget option panel settings.
         */
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

        /** @var $output - initialize widget content output as an unordered list */
        $output = '<ul class="bns-site-data-list">';

        /** Read the data array and add the values that exist as list items */
        foreach ( $data as $label => $value )
            $output .= '<li class="bns-site-data-' . strtolower( $label ) . '">' . number_format( $value ) . ' ' . $label . '</li>';

        /** Close the list */
        $output .= '</ul>';

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
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'bns-sd' ); ?></label>
            <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked( (bool) $instance['posts'], true ); ?> id="<?php echo $this->get_field_id( 'posts' ); ?>" name="<?php echo $this->get_field_name( 'posts' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'posts' ); ?>"><?php _e( 'Show your posts count?', 'bns-sd' ); ?></label>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked( (bool) $instance['pages'], true ); ?> id="<?php echo $this->get_field_id( 'pages' ); ?>" name="<?php echo $this->get_field_name( 'pages' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'pages' ); ?>"><?php _e( 'Show your pages count?', 'bns-sd' ); ?></label>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked( (bool) $instance['cats'], true ); ?> id="<?php echo $this->get_field_id( 'cats' ); ?>" name="<?php echo $this->get_field_name( 'cats' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'cats' ); ?>"><?php _e( 'Show your categories count?', 'bns-sd' ); ?></label>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked( (bool) $instance['tags'], true ); ?> id="<?php echo $this->get_field_id( 'tags' ); ?>" name="<?php echo $this->get_field_name( 'tags' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'tags' ); ?>"><?php _e( 'Show your tags count?', 'bns-sd' ); ?></label>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked( (bool) $instance['comments'], true ); ?> id="<?php echo $this->get_field_id( 'comments' ); ?>" name="<?php echo $this->get_field_name( 'comments' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'comments' ); ?>"><?php _e( 'Show your comments count?', 'bns-sd' ); ?></label>
        </p>

        <p>
            <input class="checkbox" type="checkbox" <?php checked( (bool) $instance['attachments'], true ); ?> id="<?php echo $this->get_field_id( 'attachments' ); ?>" name="<?php echo $this->get_field_name( 'attachments' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'attachments' ); ?>"><?php _e( 'Show your attachments count?', 'bns-sd' ); ?></label>
        </p>

    <?php
    }
    /** End: form override */

}
/** End: Class Extension */

/** Add the plugin to the available widgets */
function load_BNS_Site_Data_Widget() {
    register_widget( 'BNS_Site_Data_Widget' );
}

/** Load the plugin during widget initialization */
add_action( 'widgets_init', 'load_BNS_Site_Data_Widget' );

/**
 * BNS Site Data Shortcode
 * Adds shortcode functionality by using the PHP output buffer methods to
 * capture `the_widget` output and return the data to be displayed via the use
 * of the `bns_site_data` shortcode.
 *
 * @package BNS_Site_Data
 * @since   0.1
 *
 * @uses    the_widget
 * @uses    shortcode_atts
 *
 * @internal used with add_shortcode
 */
function BNS_Site_Data_Shortcode( $atts ) {

    /** Start output buffer capture */
    ob_start(); ?>
        <div class="bns-site-data-shortcode">
            <?php
            /**
             * Use 'the_widget' as the main output function to be captured
             * @link http://codex.wordpress.org/Function_Reference/the_widget
             */
            the_widget(
            /** The widget name as defined in the class extension */
                'BNS_Site_Data_Widget',
                /**
                 * The default options (as the shortcode attributes array) to be
                 * used with the widget
                 */
                $instance = shortcode_atts(
                    array(
                        /** Set title to null for aesthetic reasons */
                        'title'         => __( '', 'bns-sd' ),
                        'posts'         => true,
                        'pages'         => true,
                        'cats'          => true,
                        'tags'          => true,
                        'comments'      => true,
                        'attachments'   => true,
                    ),
                    $atts
                ),
                /**
                 * Override the widget arguments and set to null. This will set the
                 * theme related widget definitions to null for aesthetic purposes.
                 */
                $args = array (
                    'before_widget'   => '',
                    'before_title'    => '',
                    'after_title'     => '',
                    'after_widget'    => ''
                ) ); ?>
        </div><!-- .bns-site-data-shortcode -->
    <?php
    /** End the output buffer capture and save captured data into variable */
    $bns_site_data_output = ob_get_contents();
    /** Stop output buffer capture and clear properly */
    ob_end_clean();

    /** Return the output buffer data for use with add_shortcode output */
    return $bns_site_data_output;
}
add_shortcode( 'bns-site-data', 'BNS_Site_Data_Shortcode' );
/** End: Shortcode */