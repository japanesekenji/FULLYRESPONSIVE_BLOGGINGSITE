<?php
namespace ElementorExtras\Modules\DisplayConditions\Conditions;

// Extras for Elementor Classes
use ElementorExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions\Acf_Choice
 *
 * @since  2.2.0
 */
class Acf_Choice extends Acf_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_name() {
		return 'acf_choice';
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
		return __( 'ACF Choice', 'elementor-extras' );
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
			'description'	=> __( 'Search ACF "Select", "Checkbox" and "Radio" fields by label.', 'elementor-extras' ),
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
			'field_type' => [
				'option',
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
			'type' 			=> Controls_Manager::TEXTAREA,
			'default' 		=> '',
			'placeholder'	=> __( 'Choices', 'elementor-extras' ),
			'description'	=> __( 'Enter each accepted choice on a separate line. You can specify the value ( red ) or both value and label ( red : Red ). Leave blank to check if the field is set.', 'elementor-extras' ),
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
	public function check( $key, $operator, $values ) {
		$show = false;

		$field_value = $this->get_field( $key );

		if ( $field_value ) {
			if ( ! $values || '' === trim( $values ) || empty( $values ) ) {
				return $this->compare( true, true, $operator );
			}

			$field_settings 	= $this->get_field_object( $key );
			$field_choices 		= $field_settings['choices'];
			$is_array_format 	= 'array' === $field_settings['return_format'];
			$is_radio 			= 'radio' === $field_settings['type'];
			$field_values 		= $this->acf_choice_parse_format( $field_value, $is_array_format, $is_radio );
			$check_values 		= acf_decode_choices( $values );

			$check_by_key 		= array_intersect_key( $field_values, $check_values );
			$check_by_value 	= array_intersect( $field_values, $check_values );

			$show = $check_by_key || $check_by_value || $this->acf_label_exists_as_value( $field_values, $field_choices, $check_values );
		}

		return $this->compare( $show, true, $operator );
	}

	/**
	 * Label Exists As Value
	 *
	 * Performs a cross check to see if a label is set as a condition
	 * and matched a field label even though the return format is set 
	 * as Value.
	 *
	 * @since 2.2.0
	 *
	 * @access public
	 * @param array  	$field_values  		The values saved for the field
	 * @param array  	$field_choices  	All the available choices for the field
	 * @param array  	$check_values  	The condition values enters in Elementor
	 * @return bool
	 */
	protected function acf_label_exists_as_value( $field_values, $field_choices, $check_values ) {
		foreach( $check_values as $index => $selected_value ) {
			if ( in_array( $index, $field_choices ) ) {
				$field_choice_key = array_search( $index, $field_choices );
				if ( in_array( $field_choice_key, $field_values ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Parse array format
	 *
	 * @since 2.2.0
	 *
	 * @access public
	 * @param array  	$values  	The array to parse
	 * @param bool  	$array  	If the return format is array
	 * @return array
	 */
	protected function acf_choice_parse_format( $values, $return_array = true, $radio = false ) {
		$return = [];

		if ( $radio ) {
			if ( $return_array ) {
				$return[ $values['value'] ] = $values['label'];
			} else {
				$return[ $values ] = $values;
			}
		} else {
			foreach( $values as $index => $value ) {
				if ( $return_array ) {
					$return[ $value['value'] ] = $value['label'];
				} else {
					$return[ $value ] = $value;
				}
			}
		}

		return $return;
	}
}
