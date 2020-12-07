<?php
namespace ElementorExtras\Modules\DisplayConditions\Conditions;

// Extras for Elementor Classes
use ElementorExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions
 *
 * @since  2.2.0
 */
class Var_Base extends Condition {

	/**
	 * Get Group
	 * 
	 * Get the group of the condition
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_group() {
		return 'var';
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
		return [
			'type' 			=> Controls_Manager::TEXT,
			'default' 		=> '',
			'placeholder'	=> __( 'Name', 'elementor-extras' ),
			'label_block' 	=> true,
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
			'description'	=> __( 'Leave blank to accept any value.', 'elementor-extras' ),
			'placeholder'	=> __( 'Value', 'elementor-extras' ),
			'label_block' 	=> true,
		];
	}
}
