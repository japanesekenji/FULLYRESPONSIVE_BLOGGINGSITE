<?php
namespace ElementorExtras\Modules\DisplayConditions\Conditions;

// Extras for Elementor Classes
use ElementorExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions\Edd_Cart
 *
 * @since  2.2.0
 */
class Edd_Cart extends Condition {

	/**
	 * Checks if current condition is supported
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public static function is_supported() {
		return class_exists( 'Easy_Digital_Downloads', false );
	}

	/**
	 * Get Group
	 * 
	 * Get the group of the condition
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_group() {
		return 'edd';
	}

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_name() {
		return 'edd_cart';
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
		return __( 'Cart', 'elementor-extras' );
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
			'type' 			=> Controls_Manager::SELECT,
			'default' 		=> 'empty',
			'label_block' 	=> true,
			'options' 		=> [
				'empty'		=> __( 'Empty', 'elementor-extras' ),
			],
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
	public function check( $name, $operator, $value ) {
		if ( ! function_exists( 'edd_get_cart_contents' ) )
			return false;

		$show = empty( edd_get_cart_contents() );

		return $this->compare( $show, true, $operator );
	}
}
