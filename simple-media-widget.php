<?php
/*
Plugin Name: Simple Media Widget
Description: A widget for adding images.
Author: Sheri Bigelow
Author URI: http://designsimply.com
Version: 1.0.0
License: GNU General Public License v2.0 or later
License URI: http://www.opensource.org/licenses/gpl-license.php
*/

/**
 * Don't call this file directly.
 */
if ( ! class_exists( 'WP' ) ) {
	die();
}

class Simple_Media_Widget extends WP_Widget {
	private static $instance = null;

	/**
	 * Filter media view strings.
	 *
	 * `insertMediaTitle` is the title of the modal.
	 * `insertIntoPost` is the button at the bottom right.
	 */
	public function filter_media_view_strings( $strings ) {
		$strings[ 'insertMediaTitle' ] = 'Select an Image';
		$strings[ 'insertIntoPost' ] = 'Select';

		// @todo: Add Insert from URL support later.
		unset( $strings['insertFromUrlTitle'] );

		// @todo: Add gallery support later.
		unset( $strings['createGalleryTitle'] );
		unset( $strings['mediaLibraryTitle'] );

		return $strings;
	}

	/**
	 * Filter media view settings.
	 *
	 * `mimeTypes` includes the file types selectable in the libary.
	 */
	public function filter_media_view_settings( $settings ) {
		$settings[ 'mimeTypes' ] = array( 'image' => $settings[ 'mimeTypes' ][ 'image'] );

		return $settings;
	}

	/**
	 * Register the widget with WordPress.
     *
     * @since 0.1.0
	 */
	public function __construct() {
		parent::__construct(
			'simple_media_widget',
			__( 'Media Widget' ),
			array( 'description' => __( 'Add an image to a widget area.' ) )
		);

		add_filter( 'media_view_strings', array( $this, 'filter_media_view_strings' ) );
		add_filter( 'media_view_settings', array( $this, 'filter_media_view_settings' ) );
		add_filter( 'simple_media_widget', 'shortcode_unautop');
		add_filter( 'simple_media_widget', 'do_shortcode' );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance Saved setting from the database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );

		echo $before_widget . "\n";

		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}

		if ( ! empty( $instance['description'] ) ) {
			echo '<p class="align' . $instance['align'] . '">' . $instance['description'] . '</p>';
		}

		if ( 'file' == $instance['linkTo'] ) {
			$selectedLink = $instance['url'];
		} else if ( 'post' == $instance['linkTo'] ) {
			$selectedLink = $instance['link'];
		} else if ( 'custom' == $instance['linkTo'] && ! empty( $instance['linkTo'] ) ) {
			$selectedLink = $instance['linkUrl'];
		} else {
			$selectedLink = '';
		}

		// Build the image output
		$image_output = '';
		if ( ! empty( $selectedLink ) ) {
				$image_output .= '<a href="' . $selectedLink . '"';
			if ( 'on' == $instance['target'] ) {
				$image_output .= ' target="_blank"';
			}
			$image_output .= '>';
		}

		if ( ! empty( $instance['id'] ) ) {
			$image_output .= wp_get_attachment_image( $instance['id'], $instance['size'], false, array(
				'id'    => 'simple-media-widget-image-preview',
				'class' => 'align' . $instance['align'],
				'title' => $instance['title'],
			) );
		}

		if ( ! empty( $selectedLink ) ) {
			$image_output .= '</a>';
		}

		// Build the caption output
		$caption_output = '';
		if ( ! empty( $instance['caption'] ) ) {
			$caption_output .= '[caption id="attachment_' . $instance['id'] . '" align="align' . $instance['align'] . '" width="' . $instance['width'] . '"]';
			$caption_output .= $image_output;
			$caption_output .= ' ' . $instance['caption'] . '[/caption]';
			echo do_shortcode( $caption_output );
		} else {
			echo $image_output;
		}

		echo "\n" . $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['id']    = intval( $new_instance['id'] );
		$instance['width'] = intval( $new_instance['width'] );
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['alt']   = sanitize_text_field( $new_instance['alt'] );
		if ( current_user_can('unfiltered_html') ) {
			$instance['caption'] =     wp_kses_post ( $new_instance['caption'] );
			$instance['description'] = wp_kses_post ( $new_instance['description'] );
		} else {
			$instance['caption'] =     wp_filter_post_kses( $new_instance['caption'] );
			$instance['description'] = wp_filter_post_kses( $new_instance['description'] );
		}
		$instance['linkUrl'] = filter_var( $new_instance['linkUrl'], FILTER_VALIDATE_URL );
		// Everything else
		$instance['url']    = filter_var( $new_instance['url'], FILTER_VALIDATE_URL );
		$instance['link']   = filter_var( $new_instance['link'], FILTER_VALIDATE_URL );
		$instance['align']  = sanitize_text_field( $new_instance['align'] );
		$instance['size']   = sanitize_text_field( $new_instance['size'] );
		$instance['linkTo'] = sanitize_text_field( $new_instance['linkTo'] );
		$instance['target'] = sanitize_text_field( $new_instance['target'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$defaults = array(
			'id' => '',
			'url' => '',
			'link' => '',
			'title' => '',
			'caption' => '',
			'alt' => '',
			'description' => '',
			'align' => '',
			'size' => '',
			'linkTo' => '',
			'linkUrl' => '',
			'width' => '',
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		$widget_id = $this->id;

		include dirname( __FILE__ ) . '/templates/form.php';
	}

	/**
	 * Register the stylesheet for handling the widget in the back-end.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_admin_styles() {
		wp_register_style( 'simple-media-admin-styles', plugins_url( '/css/admin.css', __FILE__  ),
			array( 'media-views' ), '130323' );
		wp_enqueue_style( 'simple-media-admin-styles' );
	}

	/**
	 * Register the scripts for handling the widget in the back-end.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_admin_scripts() {
		global $pagenow;

		// Bail if we are not in the widgets or customize screens.
		if ( ! ( 'widgets.php' == $pagenow || 'customize.php' == $pagenow ) ) {
			return;
		}

		// Load the required media files for the media manager.
		wp_enqueue_media();

		// Register, localize and enqueue custom JS.
		wp_register_script( 'simple-media-admin', plugins_url( '/js/admin.js', __FILE__ ),
			array( 'jquery', 'media-models', 'media-views' ), '150320', true );
		wp_localize_script( 'simple-media-admin', 'simple_media_l10n',
			array(
				'title'  => __( 'Select an Image', 'simple-media-widget' ),
				'button' => __( 'Insert Image', 'simple-media-widget' )
			)
		);
		wp_enqueue_script( 'simple-media-admin' );
	}

}

function simple_media_widget_init() {
	register_widget( 'Simple_Media_Widget' );
}

add_action( 'widgets_init', 'simple_media_widget_init' );
