<?php
namespace ElementorExtras\Modules\QueryControl\Types;

// Extras for Elementor Classes
use ElementorExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\QueryControl\Types\Base
 *
 * @since  2.2.0
 */
class Type_Base extends Module_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_name() {}

	/**
	 * Gets autocomplete values
	 *
	 * @since  2.2.0
	 * @return array
	 */
	protected function get_autocomplete_values( array $data ) {}

	/**
	 * Gets control values titles
	 *
	 * @since  2.2.0
	 * @return array
	 */
	protected function get_value_titles( array $request ) {}
}
