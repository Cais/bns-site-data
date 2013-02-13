<?php
/*
Plugin Name: BNS Site Data
Plugin URI: http://buynowshop.com/plugins/
Description: Show some basic site statistics.
Version: 0.3
Author: Edward Caissie
Author URI: http://edwardcaissie.com/
Text Domain: bns-sd
License: GNU General Public License v2
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/**
 * BNS Site Data WordPress plugin
 * Display various site statistics (read: counts) such as: posts, pages,
 * categories, tags, comments, and attachments. Each site statistic can be
 * toggled via a checkbox in the widget option panel.
 *
 * @package     BNS_Site_Data
 * @link        http://buynowshop.com/plugins/bns-site-data
 * @link        https://github.com/Cais/bns-site-data
 * @link        http://wordpress.org/extend/plugins/bns-site-data
 * @version     0.3
 * @author      Edward Caissie <edward.caissie@gmail.com>
 * @copyright   Copyright (c) 2012-2013, Edward Caissie
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
 *
 * @version 0.3
 * @date    February 13, 2013
 * Moved all code into class structure
 * Added code block termination comments
 */

class BNS_Site_Data_Widget extends WP_Widget {

    /**
     * Constructor / BNS Site Date Widget
     *
     * @package BNS_Site_Data
     * @since   0.1
     *
     * @uses    WP_Widget (class)
     * @uses    add_action
     */
    function BNS_Site_Data_Widget() {
        /** Widget settings. */
        $widget_ops = array( 'classname' => 'bns-site-data', 'description' => __( 'Displays some site stuff.', 'bns-sd' ) );
        /** Widget control settings. */
        $control_ops = array( 'width' => 200, 'id_base' => 'bns-site-data' );
        /** Create the widget. */
        $this->WP_Widget( 'bns-site-data', 'BNS Site Data', $widget_ops, $control_ops );

        /** End: Enqueue Plugin Scripts and Styles */
        add_action( 'wp_enqueue_scripts', array( $this, 'scripts_and_styles' ) );

        /**
         * Once the widget is registered it can be added/loaded during the
         * widget initialization via an action hook.
         */
        add_action( 'widgets_init', array( $this, 'load_BNS_Site_Data_Widget' ) );

        /** Add Shortcode */
        add_shortcode( 'bns_site_data', array( $this, 'BNS_Site_Data_Shortcode' ) );

    } /** End function - bns site data widget */


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
        if ( $title ) {
            /** @noinspection PhpUndefinedVariableInspection - IDE ONLY comment */
            echo $before_title . $title . $after_title;
        } /** End if - title */

        /**
         * Initialize the data array; and, only add the values based on the
         * widget option panel settings.
         */
        $data = array();
        if ( $posts ) {
            $data['Posts']          = wp_count_posts( 'post' )->publish;
        } /** End if - posts */
        if ( $pages ) {
            $data['Pages']          = wp_count_posts( 'page' )->publish;
        } /** End if - pages */
        if ( $cats ) {
            $data['Categories']     = wp_count_terms( 'category' );
        } /** End if - categories */
        if ( $tags ) {
            $data['Tags']           = wp_count_terms( 'post_tag' );
        } /** End if - tags */
        if ( $comments ) {
            $data['Comments']       = wp_count_comments()->approved;
        } /** End if - comments */
        if ( $attachments ) {
            $data['Attachments']    = wp_count_posts( 'attachment' )->inherit;
        } /** End if - attachments */

        /** @var $output - initialize widget content as an unordered list */
        $output = '<ul class="bns-site-data-list">';

        /**
         * Read the data array and add the values that exist as list items.
         * @internal dynamic filter hooks are available for each label; can you
         * say Mallory-Everest?!
         */
        foreach ( $data as $label => $value ) {
            $output .= apply_filters(
                'bns_site_data_' . strtolower( $label ),
                '<li class="bns-site-data-' . strtolower( $label ) . '">' . number_format( $value ) . ' ' . $label . '</li>'
            );
        } /** End for - data as label value */

        /** Close the list */
        $output .= '</ul>';

        /** Write the list to the screen */
        echo $output;

        /** @var $after_widget (defined by themes). */
        echo $after_widget;
    } /** End function - widget method override */


    /**
     * Overrides update method from WP_Widget class
     * Update a particular instance of the widget.
     *
     * This function should check that $new_instance is set correctly. The newly
     * calculated value of $instance should be returned. If "false" is returned,
     * the instance won't be saved/updated.
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

    } /** End function - update override */


    /**
     * Overrides form method from WP_Widget class
     * This function displays the widget option panel form used to update the
     * widget settings.
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
            'title'         => __( 'Site Data', 'bns-sd' ),
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
    } /** End function - form override */


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
     *
     * @version 0.1.1
     * @date    September 19, 2012
     * Correct error with undefined function
     *
     * @version 0.2
     * @date    November 26, 2012
     * Add custom script (end-user supplied) file call
     * Renamed `BNS_Site_Data_Scripts_and_Styles` to `scripts_and_styles`
     */
    function scripts_and_styles() {
        /** @var $bns_sd_data - holds plugin data */
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        $bns_sd_data = get_plugin_data( __FILE__ );

        /** Enqueue Scripts */
        wp_enqueue_script( 'BNS-Site-Data-Scripts', plugin_dir_url( __FILE__ ) . 'bns-site-data-scripts.js', array( 'jquery' ), $bns_sd_data['Version'], 'true' );
        /** Check if custom script file is readable (exists) */
        if ( is_readable( plugin_dir_path( __FILE__ ) . 'bns-site-data-custom-scripts.js' ) ) {
            wp_enqueue_script( 'BNS-Site-Data-Custom-Style', plugin_dir_url( __FILE__ ) . 'bns-site-data-custom-scripts.js', array( 'jquery' ), $bns_sd_data['Version'], 'true' );
        } /** End if - is readable */

        /** Enqueue Style Sheets */
        wp_enqueue_style( 'BNS-Site-Data-Style', plugin_dir_url( __FILE__ ) . 'bns-site-data-style.css', array(), $bns_sd_data['Version'], 'screen' );
        /** Check if custom stylesheet is readable (exists) */
        if ( is_readable( plugin_dir_path( __FILE__ ) . 'bns-site-data-custom-style.css' ) ) {
            wp_enqueue_style( 'BNS-Site-Data-Custom-Style', plugin_dir_url( __FILE__ ) . 'bns-site-data-custom-style.css', array(), $bns_sd_data['Version'], 'screen' );
        } /** End if - is readable */

    } /** End function - scripts and styles */


    /**
     * Load BNS Site Data Widget
     * We need to take the widget code (read: the class BNS_Site_Data_Widget that
     * extends the WP_Widget class) and register it as a widget.
     *
     * @package BNS_Site_Data
     * @since   0.1
     *
     * @uses    register_widget
     */
    function load_BNS_Site_Data_Widget() {
        register_widget( 'BNS_Site_Data_Widget' );
    } /** End function - load widget */


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
                 * Override the widget arguments and set to null. This will set
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
        /** Get the current output buffer contents and delete current output buffer. */
        /** @var $bns_site_data_output string */
        $bns_site_data_output = ob_get_clean();

        /** Return the output buffer data for use with add_shortcode output */
        return $bns_site_data_output;

    } /** End function - bns site data shortcode */


} /** End class - bns site data */


/** @var $bnssd - instantiate the class */
$bnssd = new BNS_Site_Data_Widget();