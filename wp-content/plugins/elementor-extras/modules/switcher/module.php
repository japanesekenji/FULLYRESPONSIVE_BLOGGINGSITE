<?php
namespace ElementorExtras\Modules\Switcher;

// Extras for Elementor Classes
use ElementorExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\Switcher\Module
 *
 * @since  1.9.0
 */
class Module extends Module_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  1.9.0
	 * @return string
	 */
	public function get_name() {
		return 'switcher';
	}

	/**
	 * Get Widgets
	 * 
	 * Get the modules' widgets
	 *
	 * @since  1.9.0
	 * @return array
	 */
	public function get_widgets() {
		return [
			'Switcher',
		];
	}
}
