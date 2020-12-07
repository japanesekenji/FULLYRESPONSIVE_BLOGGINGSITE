<?php
namespace ElementorExtras\Modules\QueryControl\Types;

// Extras for Elementor Classes
use ElementorExtras\Utils;
use ElementorExtras\Modules\QueryControl\Types\Type_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\QueryControl\Types\Meta_Base
 *
 * @since  2.2.5
 */
class Meta_Base extends Type_Base {

	/**
	 * Returns array of component field types organized
	 * based on categories
	 *
	 * @since  2.2.5
	 * @return array
	 */
	public function get_field_types() {
		return [];
	}

	/**
	 * Checks if given control field types match
	 * component field types
	 *
	 * @since  2.2.5
	 * @param  array $valid_types 	Sets of valid control field types
	 * @param  array $types 		Component field type to check against
	 * @return bool
	 */
	protected function is_valid_field_type( $valid_types, $type ) {
		if ( ! $valid_types || ! $type ) {
			return false;
		}

		$field_types = $this->get_field_types();

		if ( is_array( $valid_types ) ) {
			foreach ( $valid_types as $valid_type ) {

				if ( is_array( $field_types[ $valid_type ] ) ) {
					if ( in_array( $type, $field_types[ $valid_type ] ) ) {
						return true;
					}
				} else {
					if ( $type === $field_types[ $valid_type ] ) {
						return true;
					}
				}
			}
		} else if ( in_array( $type, $field_types[ $valid_types ] ) ) {
			return true;
		}

		return false;
	}
}
