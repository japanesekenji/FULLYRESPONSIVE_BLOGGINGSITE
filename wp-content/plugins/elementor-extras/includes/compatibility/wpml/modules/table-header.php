<?php
namespace ElementorExtras\Compatibility\WPML;

use WPML_Elementor_Module_With_Items;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Table
 *
 * Registers translatable module with items
 *
 * @since 2.2.2
 */
class Table_Header extends WPML_Elementor_Module_With_Items {

	/**
	 * @since 1.8.0
	 * @return string
	 */
	public function get_items_field() {
		return 'header_cells';
	}

	/**
	 * Retrieve the fields inside the repeater
	 * 
	 * @since 2.2.2
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'cell_text', 					// Header cell content
		);
	}

	/**
	 * Method for setting the title for each translatable field
	 *
	 * @since 2.2.2
	 *
	 * @param string    $field The name of the field
	 * @return string
	 */
	protected function get_title( $field ) {
		if ( 'cell_text' === $field ) {
			return esc_html__( 'Table: Header Cell Text', 'elementor-extras' );
		}

		return '';
	}

	/**
	 * Method for determining the editor type for each field
	 * @since 2.2.2
	 *
	 * @param  string    $field Name of the field
	 * @return string
	 */
	protected function get_editor_type( $field ) {

		switch( $field ) {
			case 'cell_text':
				return 'LINE';
	 
			default:
				return '';
		 }
	}

}
