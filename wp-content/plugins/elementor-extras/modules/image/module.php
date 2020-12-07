<?php
namespace ElementorExtras\Modules\Image;

// Extras for Elementor Classes
use ElementorExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\Image\Module
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
		return 'image';
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
			'Random_Image',
			'Image_Comparison',
		];
	}

	/**
	 * Get Image Caption
	 * 
	 * Get the attachment caption
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public static function get_image_caption( $attachment, $types = 'caption' ) {

		if ( ! $attachment ) {
			return;
		}

		if ( ! is_a( $attachment, 'WP_Post' ) ) {
			if ( is_numeric( $attachment ) ) {
				$attachment = get_post( $attachment );

				if ( ! $attachment ) return '';
			}
		}

		if ( empty( $types ) ) {
			return $attachment->post_excerpt;
		}

		$data = [];

		if ( $attachment->post_title ) {
			if ( is_array( $types ) ) {
				if ( in_array( 'title', $types ) ) {
					$data['title'] = $attachment->post_title;
				}
			} elseif ( 'title' === $types ) {
				return $attachment->post_title;
			}
		}

		if ( $attachment->post_excerpt ) {
			if ( is_array( $types ) ) {
				if ( in_array( 'caption', $types ) ) {
					$data['caption'] = $attachment->post_excerpt;
				}
			} elseif ( 'caption' === $types ) {
				return $attachment->post_excerpt;
			}
		}

		if ( $attachment->post_content ) {
			if ( is_array( $types ) ) {
				if ( in_array( 'description', $types ) ) {
					$data['description'] = $attachment->post_content;
				}
			} elseif ( 'description' === $types ) {
				return $attachment->post_content;
			}
		}

		if ( ! $data ) {
			return '';
		}

		return $data;
	}

	public static function render_image_title( $title ) {
		?><div class="ee-caption__title"><?php echo $title; ?></div><?php
	}

	public static function render_image_caption( $caption ) {
		?><div class="ee-caption__caption"><?php echo $caption; ?></div><?php
	}

	public static function render_image_description( $description ) {
		?><div class="ee-caption__description"><?php echo $description; ?></div><?php
	}
}
