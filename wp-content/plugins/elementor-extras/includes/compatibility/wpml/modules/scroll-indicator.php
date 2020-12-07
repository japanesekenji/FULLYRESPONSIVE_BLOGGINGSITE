<?php
namespace ElementorExtras\Compatibility\WPML;

use WPML_Elementor_Module_With_Items;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Scroll_Indicator
 *
 * Registers translatable module with items
 *
 * @since 2.1.0
 */
class Scroll_Indicator extends WPML_Elementor_Module_With_Items {

	/**
	 * @since 2.1.0
	 * @return string
	 */
	public function get_items_field() {
		return 'sections';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array(
			'title',
			'subtitle',
		);
	}

	/**
	 * @param string $field
	 * @since 2.1.0
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		if ( 'title' === $field ) {
			return esc_html__( 'Scroll Indicator: Title', 'elementor-extras' );
		}

		if ( 'subtitle' === $field ) {
			return esc_html__( 'Scroll Indicator: Subtitle', 'elementor-extras' );
		}

		return '';
	}

	/**
	 * @param string $field
	 * @since 2.1.0
	 *
	 * @return string
	 */
	protected function get_editor_type( $field ) {

		switch( $field ) {
			case 'title':
			case 'subtitle':
				return 'LINE';

			default:
				return '';
		 }
	}

}
