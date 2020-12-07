<?php
namespace ElementorExtras\Modules\DisplayConditions\Conditions;

// Extras for Elementor Classes
use ElementorExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions\Browser
 *
 * @since  2.2.0
 */
class Browser extends Condition {

	/**
	 * Get Group
	 * 
	 * Get the group of the condition
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_group() {
		return 'visitor';
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
		return 'browser';
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
		return __( 'Browser', 'elementor-extras' );
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
			'default' 		=> 'ie',
			'label_block' 	=> true,
			'options' 		=> [
				'ie'			=> 'Internet Explorer',
				'firefox'		=> 'Mozilla Firefox',
				'chrome'		=> 'Google Chrome',
				'opera_mini'	=> 'Opera Mini',
				'opera'			=> 'Opera',
				'safari'		=> 'Safari',
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
		$browsers = [
			'ie'			=> [
				'MSIE',
				'Trident',
			],
			'firefox'		=> 'Firefox',
			'chrome'		=> 'Chrome',
			'opera_mini'	=> 'Opera Mini',
			'opera'			=> 'Opera',
			'safari'		=> 'Safari',
		];

		$show = false;

		if ( 'ie' === $value ) {
			if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], $browsers[ $value ][0] ) || false !== strpos( $_SERVER['HTTP_USER_AGENT'], $browsers[ $value ][1] ) ) {
				$show = true;
			}
		} else {
			if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], $browsers[ $value ] ) ) {
				$show = true;

				// Additional check for Chrome that returns Safari
				if ( 'safari' === $value || 'firefox' === $value ) {
					if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], 'Chrome' ) ) {
						$show = false;
					}
				}
			}
		}
		

		return $this->compare( $show, true, $operator );
	}
}
