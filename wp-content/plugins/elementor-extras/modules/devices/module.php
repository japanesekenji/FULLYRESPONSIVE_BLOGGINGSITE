<?php
namespace ElementorExtras\Modules\Devices;

// Extras for Elementor Classes
use ElementorExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\Buttons\Module
 *
 * @since  1.6.0
 */
class Module extends Module_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  1.6.0
	 * @return string
	 */
	public function get_name() {
		return 'devices';
	}

	/**
	 * Get Widgets
	 * 
	 * Get the modules' widgets
	 *
	 * @since  1.6.0
	 * @return array
	 */
	public function get_widgets() {
		return [
			'Devices',
		];
	}
}
