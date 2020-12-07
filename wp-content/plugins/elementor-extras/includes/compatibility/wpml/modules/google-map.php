<?php
namespace ElementorExtras\Compatibility\WPML;

use WPML_Elementor_Module_With_Items;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Google_Map
 *
 * Registers translatable module with items
 *
 * @since 2.0.0
 */
class Google_Map extends WPML_Elementor_Module_With_Items {

	/**
	 * @since 2.0.0
	 * @return string
	 */
	public function get_items_field() {
		return 'pins';
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
			'name',
		);
	}

	/**
	 * Method for setting the title for each translatable field
	 *
	 * @since 1.8.8
	 *
	 * @param string    $field The name of the field
	 * @return string
	 */
	protected function get_title( $field ) {
		if ( 'name' === $field ) {
			return esc_html__( 'Google Map: Pin Name', 'elementor-extras' );
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
			case 'name':
				return 'LINE';
	 
			default:
				return '';
		 }
	}

}
