<?php
/*
Plugin Name: BNS Site Data
Plugin URI: http://buynowshop.com/plugins/
Description: Show some basic site statistics.
Version: 0.4.3
Author: Edward Caissie
Author URI: http://edwardcaissie.com/
Text Domain: bns-site-data
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
 * @link        https://wordpress.org/plugins/bns-site-data
 * @version     0.4.3
 * @author      Edward Caissie <edward.caissie@gmail.com>
 * @copyright   Copyright (c) 2012-2015, Edward Caissie
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
 * @version 0.4.3
 * @date    July 2015
 */
class BNS_Site_Data_Widget extends WP_Widget {

	/**
	 * Constructor / BNS Site Date Widget
	 *
	 * @package BNS_Site_Data
	 * @since   0.1
	 *
	 * @uses    BNS_Site_Data_Widget::WP_Widget (factory)
	 * @uses    BNS_Site_Data_Widget::BNS_Site_Data_Shortcode
	 * @uses    BNS_Site_Data_Widget::load_BNS_Site_Data_Widget
	 * @uses    BNS_Site_Data_Widget::scripts_and_styles
	 * @uses    __
	 * @uses    add_action
	 * @uses    add_shortcode
	 *
	 * @version 0.4
	 * @date    December 29, 2014
	 * Renamed function to `__construct` from `BNS_Site_Data_Widget`
	 */
	function __construct() {

		/**
		 * Check installed WordPress version for compatibility
		 *
		 * @internal    Version 3.6 in reference to `shortcode_atts` filter option
		 * @link        https://developer.wordpress.org/reference/functions/shortcode_atts/
		 */
		global $wp_version;
		$exit_message = sprintf( __( 'BNS Site Data requires WordPress version 3.6 or newer. %1$s', 'bns-site-data' ), '<a href="http://codex.wordpress.org/Upgrading_WordPress">' . __( 'Please Update!', 'bns-site-data' ) . '</a>' );
		$exit_message .= '<br />';
		$exit_message .= sprintf( __( 'In reference to the shortcode default attributes filter. See %1$s.', 'bns-site-data' ), '<a href="https://developer.wordpress.org/reference/functions/shortcode_atts/">' . __( 'this link', 'bns-site-data' ) . '</a>' );
		if ( version_compare( $wp_version, "3.6", "<" ) ) {
			exit( $exit_message );
		}
		/** End if = version compare */

		/** Widget settings. */
		$widget_ops = array(
			'classname'   => 'bns-site-data',
			'description' => __( 'Displays some site stuff.', 'bns-site-data' )
		);

		/** Widget control settings. */
		$control_ops = array( 'width' => 200, 'id_base' => 'bns-site-data' );

		/** Create the widget. */
		parent::__construct( 'bns-site-data', 'BNS Site Data', $widget_ops, $control_ops );

		/** End: Enqueue Plugin Scripts and Styles */
		add_action( 'wp_enqueue_scripts', array(
			$this,
			'scripts_and_styles'
		) );

		/**
		 * Once the widget is registered it can be added/loaded during the
		 * widget initialization via an action hook.
		 */
		add_action( 'widgets_init', array(
			$this,
			'load_BNS_Site_Data_Widget'
		) );

		/** Add Shortcode */
		add_shortcode( 'bns_site_data', array(
			$this,
			'BNS_Site_Data_Shortcode'
		) );

		add_action( 'plugins_loaded', array(
			$this,
			'bnssd_load_plugin_textdomain'
		) );

	} /** End function - bns site data widget */


	/**
	 * Overrides widget method from WP_Widget class
	 *
	 * This is where the work is done
	 *
	 * @package  BNS_Site_Data
	 * @since    0.1
	 *
	 * @param   array $args     - before_widget, after_widget, before_title, after_title
	 * @param   array $instance - widget variables
	 *
	 * @internal $args vars are either drawn from the theme register_sidebar
	 * definition, or are drawn from the defaults in WordPress core.
	 *
	 * @uses     _n
	 * @uses     apply_filters
	 * @uses     wp_count_comments
	 * @uses     wp_count_posts
	 * @uses     wp_count_terms
	 *
	 * @version  0.4
	 * @date     December 29, 2014
	 * Improved i18n implementation on output labels
	 *
	 * @version  0.4.2
	 * @date     January 1, 2015
	 * Ensure `$value` is being used as an integer in i18n implementation
	 */
	function widget( $args, $instance ) {

		extract( $args );
		/** User-selected settings. */
		$title       = apply_filters( 'widget_title', $instance['title'] );
		$posts       = $instance['posts'];
		$pages       = $instance['pages'];
		$cats        = $instance['cats'];
		$tags        = $instance['tags'];
		$comments    = $instance['comments'];
		$attachments = $instance['attachments'];

		/** Before widget (defined by themes). */
		/** @var $before_widget string - defined by theme */
		echo $before_widget;

		/** Widget title */
		if ( $title ) {
			/** @noinspection PhpUndefinedVariableInspection - IDE ONLY comment */
			echo $before_title . $title . $after_title;
		}
		/** End if - title */

		/**
		 * Initialize the data array; and, only add the values based on the
		 * widget option panel settings.
		 */
		$data = array();
		if ( $posts ) {
			$data['Posts'] = wp_count_posts( 'post' )->publish;
		}
		/** End if - posts */
		if ( $pages ) {
			$data['Pages'] = wp_count_posts( 'page' )->publish;
		}
		/** End if - pages */
		if ( $cats ) {
			$data['Categories'] = wp_count_terms( 'category' );
		}
		/** End if - categories */
		if ( $tags ) {
			$data['Tags'] = wp_count_terms( 'post_tag' );
		}
		/** End if - tags */
		if ( $comments ) {
			$data['Comments'] = wp_count_comments()->approved;
		}
		/** End if - comments */
		if ( $attachments ) {
			$data['Attachments'] = wp_count_posts( 'attachment' )->inherit;
		}
		/** End if - attachments */

		/** @var $output - initialize widget content as an unordered list */
		$output = '<ul class="bns-site-data-list">';

		/**
		 * Read the data array and add the values that exist as list items.
		 * @internal dynamic filter hooks are available for each label; can you
		 * say Mallory-Everest?!
		 */
		foreach ( $data as $label => $value ) {

			$display_label = null;

			/** Use conditional checks to ensure a translatable label is used */
			if ( 'Posts' == $label ) {
				$display_label = _n( 'Post', 'Posts', intval( $value ), 'bns-site-data' );
			} elseif ( 'Pages' == $label ) {
				$display_label = _n( 'Page', 'Pages', intval( $value ), 'bns-site-data' );
			} elseif ( 'Categories' == $label ) {
				$display_label = _n( 'Category', 'Categories', intval( $value ), 'bns-site-data' );
			} elseif ( 'Tags' == $label ) {
				$display_label = _n( 'Tag', 'Tags', intval( $value ), 'bns-site-data' );
			} elseif ( 'Comments' == $label ) {
				$display_label = _n( 'Comment', 'Comments', intval( $value ), 'bns-site-data' );
			} elseif ( 'Attachments' == $label ) {
				$display_label = _n( 'Attachment', 'Attachments', intval( $value ), 'bns-site-data' );
			}
			/** End if - label setting conditionals */

			$output .= apply_filters(
				'bns_site_data_' . strtolower( $label ),
				sprintf( '<li class="bns-site-data-' . strtolower( $label ) . '">%1$s %2$s</li>', number_format( $value ), $display_label )
			);

		}
		/** End foreach - data as label value */

		/** Close the list */
		$output .= '</ul>';

		/** Write the list to the screen */
		echo $output;

		/** @var $after_widget (defined by themes). */
		echo $after_widget;

	} /** End function - widget method override */


	/**
	 * Overrides update method from WP_Widget class
	 *
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
		$instance['title']       = strip_tags( $new_instance['title'] );
		$instance['posts']       = $new_instance['posts'];
		$instance['pages']       = $new_instance['pages'];
		$instance['cats']        = $new_instance['cats'];
		$instance['tags']        = $new_instance['tags'];
		$instance['comments']    = $new_instance['comments'];
		$instance['attachments'] = $new_instance['attachments'];

		return $instance;

	} /** End function - update override */


	/**
	 * Overrides form method from WP_Widget class
	 *
	 * This function displays the widget option panel form used to update the
	 * widget settings.
	 *
	 * @package BNS_Site_Data
	 * @since   0.1
	 *
	 * @param   array $instance
	 *
	 * @uses    BNS_Site_Data_Widget::get_field_id
	 * @uses    BNS_Site_Data_Widget::get_field_name
	 * @uses    __
	 * @uses    _e
	 * @uses    checked
	 * @uses    wp_parse_args
	 *
	 * @return  string|void
	 */
	function form( $instance ) {

		/** Set default widget settings. */
		$defaults = array(
			'title'       => __( 'Site Data', 'bns-site-data' ),
			'posts'       => true,
			'pages'       => true,
			'cats'        => true,
			'tags'        => true,
			'comments'    => true,
			'attachments' => true,
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'bns-site-data' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>"
			       value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['posts'], true ); ?>
			       id="<?php echo $this->get_field_id( 'posts' ); ?>"
			       name="<?php echo $this->get_field_name( 'posts' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'posts' ); ?>"><?php _e( 'Show your posts count?', 'bns-site-data' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['pages'], true ); ?>
			       id="<?php echo $this->get_field_id( 'pages' ); ?>"
			       name="<?php echo $this->get_field_name( 'pages' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'pages' ); ?>"><?php _e( 'Show your pages count?', 'bns-site-data' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['cats'], true ); ?>
			       id="<?php echo $this->get_field_id( 'cats' ); ?>" name="<?php echo $this->get_field_name( 'cats' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'cats' ); ?>"><?php _e( 'Show your categories count?', 'bns-site-data' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['tags'], true ); ?>
			       id="<?php echo $this->get_field_id( 'tags' ); ?>" name="<?php echo $this->get_field_name( 'tags' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'tags' ); ?>"><?php _e( 'Show your tags count?', 'bns-site-data' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['comments'], true ); ?>
			       id="<?php echo $this->get_field_id( 'comments' ); ?>"
			       name="<?php echo $this->get_field_name( 'comments' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'comments' ); ?>"><?php _e( 'Show your comments count?', 'bns-site-data' ); ?></label>
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['attachments'], true ); ?>
			       id="<?php echo $this->get_field_id( 'attachments' ); ?>"
			       name="<?php echo $this->get_field_name( 'attachments' ); ?>" />
			<label
				for="<?php echo $this->get_field_id( 'attachments' ); ?>"><?php _e( 'Show your attachments count?', 'bns-site-data' ); ?></label>
		</p>

	<?php
	} /** End function - form override */


	/**
	 * Enqueue Plugin Scripts and Styles
	 *
	 * Adds plugin scripts and stylesheet; allows for custom stylesheet to be added
	 * by end-user. These stylesheets will only affect public facing output.
	 *
	 * @package  BNS_Site_Data
	 * @since    0.1
	 *
	 * @uses     get_plugin_data
	 * @uses     plugin_dir_path
	 * @uses     plugin_dir_url
	 * @uses     wp_enqueue_script
	 * @uses     wp_enqueue_style
	 *
	 * @internal jQuery is enqueued as a dependency
	 * @internal Used with action hook: wp_enqueue_scripts
	 *
	 * @version  0.1.1
	 * @date     September 19, 2012
	 * Correct error with undefined function
	 *
	 * @version  0.2
	 * @date     November 26, 2012
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
		}
		/** End if - is readable */

		/** Enqueue Style Sheets */
		wp_enqueue_style( 'BNS-Site-Data-Style', plugin_dir_url( __FILE__ ) . 'bns-site-data-style.css', array(), $bns_sd_data['Version'], 'screen' );
		/** Check if custom stylesheet is readable (exists) */
		if ( is_readable( plugin_dir_path( __FILE__ ) . 'bns-site-data-custom-style.css' ) ) {
			wp_enqueue_style( 'BNS-Site-Data-Custom-Style', plugin_dir_url( __FILE__ ) . 'bns-site-data-custom-style.css', array(), $bns_sd_data['Version'], 'screen' );
		}
		/** End if - is readable */

	} /** End function - scripts and styles */


	/**
	 * Load BNS Site Data Widget
	 *
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
	 *
	 * Adds shortcode functionality by using the PHP output buffer methods to
	 * capture `the_widget` output and return the data to be displayed via the use
	 * of the `bns_site_data` shortcode.
	 *
	 * @package  BNS_Site_Data
	 * @since    0.1
	 *
	 * @uses     __
	 * @uses     shortcode_atts
	 * @uses     the_widget
	 *
	 * @internal used with add_shortcode
	 *
	 * @version  0.3.2
	 * @date     September 7, 2013
	 * Added third parameter to `shortcode_atts` for automatic filter creation
	 *
	 * @param $atts
	 *
	 * @return string
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
						'title'       => __( '', 'bns-site-data' ),
						'posts'       => true,
						'pages'       => true,
						'cats'        => true,
						'tags'        => true,
						'comments'    => true,
						'attachments' => true,
					),
					$atts, 'bns_site_data'
				),
				/**
				 * Override the widget arguments and set to null. This will set
				 * theme related widget definitions to null for aesthetic purposes.
				 */
				$args = array(
					'before_widget' => '',
					'before_title'  => '',
					'after_title'   => '',
					'after_widget'  => ''
				) ); ?>
		</div><!-- .bns-site-data-shortcode -->
		<?php
		/** Get the current output buffer contents and delete current output buffer. */
		/** @var $bns_site_data_output string */
		$bns_site_data_output = ob_get_clean();

		/** Return the output buffer data for use with add_shortcode output */

		return $bns_site_data_output;

	}
	/** End function - bns site data shortcode */


	/**
	 * Load Plugin Text Domain
	 *
	 * @package BNS_Site_Data
	 * @since   0.4.1
	 *
	 * @uses    load_plugin_textdomain
	 */
	function bnssd_load_plugin_textdomain() {

		load_plugin_textdomain( 'bns-site-data' );

	}
	/** End function - load plugin textdomain */

}

/** End class - bns site data */


/** @var $bnssd - instantiate the class */
$bnssd = new BNS_Site_Data_Widget();


/**
 * BNS SMF Feeds In Plugin Update Message
 *
 * @package BNS_Site_Data
 * @since   0.4
 *
 * @uses    get_transient
 * @uses    is_wp_error
 * @uses    set_transient
 * @uses    wp_kses_post
 * @uses    wp_remote_get
 *
 * @param $args
 */
function BNS_Site_Data_in_plugin_update_message( $args ) {

	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	$bnssd_data = get_plugin_data( __FILE__ );

	$transient_name = 'bnssd_upgrade_notice_' . $args['Version'];
	if ( false === ( $upgrade_notice = get_transient( $transient_name ) ) ) {

		/** @var string $response - get the readme.txt file from WordPress */
		$response = wp_remote_get( 'https://plugins.svn.wordpress.org/bns-site-data/trunk/readme.txt' );

		if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
			$matches = null;
		}
		$regexp         = '~==\s*Changelog\s*==\s*=\s*(.*)\s*=(.*)(=\s*' . preg_quote( $bnssd_data['Version'] ) . '\s*=|$)~Uis';
		$upgrade_notice = '';

		if ( preg_match( $regexp, $response['body'], $matches ) ) {
			$version = trim( $matches[1] );
			$notices = (array) preg_split( '~[\r\n]+~', trim( $matches[2] ) );

			if ( version_compare( $bnssd_data['Version'], $version, '<' ) ) {

				/** @var string $upgrade_notice - start building message (inline styles) */
				$upgrade_notice = '<style type="text/css">
							.bnssd_plugin_upgrade_notice { padding-top: 20px; }
							.bnssd_plugin_upgrade_notice ul { width: 50%; list-style: disc; margin-left: 20px; margin-top: 0; }
							.bnssd_plugin_upgrade_notice li { margin: 0; }
						</style>';

				/** @var string $upgrade_notice - start building message (begin block) */
				$upgrade_notice .= '<div class="bnssd_plugin_upgrade_notice">';

				$ul = false;

				foreach ( $notices as $index => $line ) {

					if ( preg_match( '~^=\s*(.*)\s*=$~i', $line ) ) {

						if ( $ul ) {
							$upgrade_notice .= '</ul><div style="clear: left;"></div>';
						}
						/** End if - unordered list created */

						$upgrade_notice .= '<hr/>';

					}
					/** End if - non-blank line */

					/** @var string $return_value - body of message */
					$return_value = '';

					if ( preg_match( '~^\s*\*\s*~', $line ) ) {

						if ( ! $ul ) {
							$return_value = '<ul">';
							$ul           = true;
						}
						/** End if - unordered list not started */

						$line = preg_replace( '~^\s*\*\s*~', '', htmlspecialchars( $line ) );
						$return_value .= '<li style=" ' . ( $index % 2 == 0 ? 'clear: left;' : '' ) . '">' . $line . '</li>';

					} else {

						if ( $ul ) {
							$return_value = '</ul><div style="clear: left;"></div>';
							$return_value .= '<p>' . $line . '</p>';
							$ul = false;
						} else {
							$return_value .= '<p>' . $line . '</p>';
						}
						/** End if - unordered list started */

					}
					/** End if - non-blank line */

					$upgrade_notice .= wp_kses_post( preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $return_value ) );

				}
				/** End foreach - line parsing */

				$upgrade_notice .= '</div>';

			}
			/** End if - version compare */

		}
		/** End if - response message exists */

		/** Set transient - minimize calls to WordPress */
		set_transient( $transient_name, $upgrade_notice, DAY_IN_SECONDS );

	}
	/** End if - transient check */

	echo $upgrade_notice;

}

/** End function - in plugin update message */


/** Add Plugin Update Message */
add_action( 'in_plugin_update_message-' . plugin_basename( __FILE__ ), 'BNS_Site_Data_in_plugin_update_message' );