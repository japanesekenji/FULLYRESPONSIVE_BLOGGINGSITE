<?php
namespace ElementorExtras\Modules\DisplayConditions\Conditions;

// Extras for Elementor Classes
use ElementorExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions\Acf_Text
 *
 * @since  2.2.0
 */
class Acf_Text extends Acf_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_name() {
		return 'acf_text';
	}

	/**
	 * Get Title
	 * 
	 * Get the title of the module
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_title() {
		return __( 'ACF Textual', 'elementor-extras' );
	}

	/**
	 * Get Name Control
	 * 
	 * Get the settings for the name control
	 *
	 * @since  2.2.0
	 * @return array
	 */
	public function get_name_control() {
		return wp_parse_args( [
			'description'	=> __( 'Search ACF Textual ( text, textarea, wysiwyg, number, range, email, url and password ) fields by label. Leave blank to check if the field is set.', 'elementor-extras' ),
			'placeholder'	=> __( 'Search Fields', 'elementor-extras' ),
		], $this->get_name_control_options() );
	}

	/**
	 * Additional query control options
	 *
	 * @since  2.2.33
	 * @return bool
	 */
	public function get_additional_query_options() {
		return [
			'field_type'	=> [
				'textual',
			],
		];
	}

	/**
	 * Get Value Control
	 * 
	 * Get the settings for the value control
	 *
	 * @since  2.2.0
	 * @return array
	 */
	public function get_value_control() {
		return [
			'type' 			=> Controls_Manager::TEXT,
			'default' 		=> '',
			'placeholder'	=> __( 'Value', 'elementor-extras' ),
			'label_block' 	=> true,
		];
	}

	/**
	 * Check condition
	 *
	 * @since 2.2.0
	 *
	 * @access public
	 *
	 * @param string  	$name  		The control name to check
	 * @param string 	$operator  	Comparison operator
	 * @param mixed  	$value  	The control value to check
	 */
	public function check( $key, $operator, $value ) {
		$show = false;

		$field_value = $this->get_field( $key );

		if ( $field_value ) {
			if ( '' === trim( $value ) ) {
				return $this->compare( true, true, $operator );
			}

			$field_settings = $this->get_field_object( $key );

			switch ( $field_settings['type'] ) {
				default:
					$show = $value === $field_value;
					break;
			}
		}

		return $this->compare( $show, true, $operator );
	}
}
