<?php
namespace ElementorExtras\Modules\Popup;

// Extras for Elementor Classes
use ElementorExtras\Base\Module_Base;
use ElementorExtras\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\Popup\Module
 *
 * @since  2.0.0
 */
class Module extends Module_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_name() {
		return 'popup';
	}

	/**
	 * Get Widgets
	 * 
	 * Get the modules' widgets
	 *
	 * @since  2.0.0
	 * @return array
	 */
	public function get_widgets() {
		return [
			'Popup',
			'Age_Gate',
		];
	}

	/**
	 * Get Animation Options
	 *
	 * @since  2.0.0
	 * @return array
	 */
	public static function get_animation_options() {
		return [
			'' 				=> __( 'None', 'elementor-extras' ),
			'zoom-in' 		=> __( 'Zoom In', 'elementor-extras' ),
			'zoom-out' 		=> __( 'Zoom Out', 'elementor-extras' ),
			'slide-right' 	=> __( 'Slide Right', 'elementor-extras' ),
			'slide-left' 	=> __( 'Slide Left', 'elementor-extras' ),
			'slide-top' 	=> __( 'Slide Top', 'elementor-extras' ),
			'slide-bottom' 	=> __( 'Slide Bottom', 'elementor-extras' ),
			'unfold-horizontal' => __( 'Unfold Horizontal', 'elementor-extras' ),
			'unfold-vertical' => __( 'Unfold Vertical', 'elementor-extras' ),
		];
	}
}
