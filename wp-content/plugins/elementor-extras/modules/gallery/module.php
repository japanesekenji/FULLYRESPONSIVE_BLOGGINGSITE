<?php
namespace ElementorExtras\Modules\Gallery;

// Extras for Elementor Classes
use ElementorExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\Gallery\Module
 *
 * @since  1.6.0
 */
class Module extends Module_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  1.6.0
	 * @return string
	 */
	public function get_name() {
		return 'gallery';
	}

	/**
	 * Get Widgets
	 * 
	 * Get the modules' widgets
	 *
	 * @since  1.6.0
	 * @return array
	 */
	public function get_widgets() {
		return [
			'Gallery',
			'Gallery_Slider',
		];
	}

	/**
	 * Get Link URL
	 * 
	 * Get the attachment link url from the settings
	 *
	 * @since  1.6.0
	 * @return array
	 */
	public static function get_link_url( $attachment, $instance ) {
		if ( 'none' === $instance['link_to'] ) {
			return false;
		}

		if ( 'custom' === $instance['link_to'] ) {
			if ( empty( $instance['link']['url'] ) ) {
				return false;
			}
			return $instance['link'];
		}

		return [
			'url' => wp_get_attachment_url( $attachment['id'] ),
		];
	}

	/**
	 * Get Image Info
	 * 
	 * Get image information as array
	 *
	 * @since  1.6.0
	 * @return array
	 */
	public static function get_image_info( $image_id, $image_url = '', $image_size = '' ) {

		if ( ! $image_id )
			return false;

		$info = [];

		if ( ! empty( $image_id ) ) { // Existing attachment

			$attachment = get_post( $image_id );

			if ( ! $attachment )
				return;

			$info['id']			= $image_id;
			$info['url']		= $image_url;
			$info['image'] 		= wp_get_attachment_image( $attachment->ID, $image_size, true );
			$info['caption'] 	= $attachment->post_excerpt;

		} else { // Placeholder image, most likely

			if ( empty( $image_url ) )
				return;

			$info['id']			= false;
			$info['url']		= $image_url;
			$info['image'] 		= '<img src="' . $image_url . '" />';
			$info['caption'] 	= '';
		}

		return $info;

	}
}
