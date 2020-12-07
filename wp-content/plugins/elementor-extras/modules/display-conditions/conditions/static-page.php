<?php
namespace ElementorExtras\Modules\DisplayConditions\Conditions;

// Extras for Elementor Classes
use ElementorExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions\Static_Page
 *
 * @since  2.2.0
 */
class Static_Page extends Condition {

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
		return 'static_page';
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
		return __( 'Static Page', 'elementor-extras' );
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
			'type' 			=> Controls_Manager::SELECT,
			'default' 		=> 'home',
			'label_block' 	=> true,
			'options' 		=> [
				'home'		=> __( 'Default Homepage', 'elementor-extras' ),
				'static'	=> __( 'Static Homepage', 'elementor-extras' ),
				'blog'		=> __( 'Blog Page', 'elementor-extras' ),
				'404'		=> __( '404 Page', 'elementor-extras' ),
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
	public function check( $name = null, $operator, $value ) {
		if ( 'home' === $value ) {
			return $this->compare( ( is_front_page() && is_home() ), true, $operator );
		} elseif ( 'static' === $value ) {
			return $this->compare( ( is_front_page() && ! is_home() ), true, $operator );
		} elseif ( 'blog' === $value ) {
			return $this->compare( ( ! is_front_page() && is_home() ), true, $operator );
		} elseif ( '404' === $value ) {
			return $this->compare( is_404(), true, $operator );
		}
	}
}
