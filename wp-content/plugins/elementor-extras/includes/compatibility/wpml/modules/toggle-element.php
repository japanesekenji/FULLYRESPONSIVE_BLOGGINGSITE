<?php
namespace ElementorExtras\Compatibility\WPML;

use WPML_Elementor_Module_With_Items;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Toggle_Element
 *
 * Registers translatable module with items
 *
 * @since 2.0.0
 */
class Toggle_Element extends WPML_Elementor_Module_With_Items {

	/**
	 * @since 2.0.0
	 * @return string
	 */
	public function get_items_field() {
		return 'elements';
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
			'text',
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
		if ( 'text' === $field ) {
			return esc_html__( 'Toggle Element: Label', 'elementor-extras' );
		}

		if ( 'content' === $field ) {
			return esc_html__( 'Toggle Element: Content', 'elementor-extras' );
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
			case 'text':
				return 'LINE';

			case 'content':
				return 'VISUAL';
	 
			default:
				return '';
		 }
	}

}
