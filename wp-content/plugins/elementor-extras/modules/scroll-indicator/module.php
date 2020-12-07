<?php
namespace ElementorExtras\Modules\ScrollIndicator;

// Extras for Elementor Classes
use ElementorExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\ScrollIndicator\Module
 *
 * @since  2.1.0
 */
class Module extends Module_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_name() {
		return 'scroll-indicator';
	}

	/**
	 * Get Widgets
	 * 
	 * Get the modules' widgets
	 *
	 * @since  2.1.0
	 * @return array
	 */
	public function get_widgets() {
		return [
			'Scroll_Indicator',
		];
	}
}
