<?php
namespace ElementorExtras\Modules\DisplayConditions\Conditions;

// Extras for Elementor Classes
use ElementorExtras\Utils;
use ElementorExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions\Post_Type
 *
 * @since  2.2.0
 */
class Post_Type extends Condition {

	/**
	 * Get Group
	 * 
	 * Get the group of the condition
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_group() {
		return 'single';
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
		return 'post_type';
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
		return __( 'Post Type', 'elementor-extras' );
	}

	/**
	 * Get Value Control
	 * 
	 * Get the settings for the value control
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_value_control() {
		return [
			'type' 			=> Controls_Manager::SELECT2,
			'default' 		=> '',
			'placeholder'	=> __( 'Any', 'elementor-extras' ),
			'description'	=> __( 'Leave blank or select all for any post type.', 'elementor-extras' ),
			'label_block' 	=> true,
			'multiple'		=> true,
			'options' 		=> Utils::get_public_post_types_options( true ),
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
	public function check( $name = null, $operator, $value ) {
		$show = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( is_singular( $_value ) ) {
					$show = true; break;
				}
			}
		} else { $show = is_singular( $value ); }

		return $this->compare( $show, true, $operator );
	}
}
