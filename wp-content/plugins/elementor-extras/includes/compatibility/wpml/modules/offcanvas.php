<?php
namespace ElementorExtras\Compatibility\WPML;

use WPML_Elementor_Module_With_Items;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Offcanvas
 *
 * Registers translatable module with items
 *
 * @since 2.0.0
 */
class Offcanvas extends WPML_Elementor_Module_With_Items {

	/**
	 * @since 2.0.0
	 * @return string
	 */
	public function get_items_field() {
		return 'content_boxes';
	}

	/**
	 * Retrieve the fields inside the repeater
	 * 
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'content',
		);
	}

	/**
	 * Method for setting the title for each translatable field
	 *
	 * @since 2.0.0
	 *
	 * @param string    $field The name of the field
	 * @return string
	 */
	protected function get_title( $field ) {
		if ( 'content' === $field ) {
			return esc_html__( 'Offcanvas: Content Box', 'elementor-extras' );
		}

		return '';
	}

	/**
	 * Method for determining the editor type for each field
	 * @since 2.0.0
	 *
	 * @param  string    $field Name of the field
	 * @return string
	 */
	protected function get_editor_type( $field ) {

		switch( $field ) {
			case 'content':
				return 'VISUAL';
			default:
				return '';
		 }
	}

}
