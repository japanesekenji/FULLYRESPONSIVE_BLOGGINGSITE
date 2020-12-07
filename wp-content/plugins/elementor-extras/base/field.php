<?php
namespace ElementorExtras\Base;

// Extras for Elementor Classes
use ElementorExtras\Utils;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Base\Field
 *
 * @since  2.2.0
 */
abstract class Field {

	/**
	 * @var Module_Base
	 */
	protected static $_instances = [];

	/**
	 * Return the current module class name
	 *
	 * @access public
	 * @since 1.6.0
	 *
	 * @eturn string
	 */
	public static function class_name() {
		return get_called_class();
	}

	/**
	 * @return static
	 */
	public static function instance() {
		if ( empty( static::$_instances[ static::class_name() ] ) ) {
			static::$_instances[ static::class_name() ] = new static();
		}

		return static::$_instances[ static::class_name() ];
	}

	/**
	 * Checks if current condition is supported
	 * Defaults to true
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public static function is_supported() {
		return true;
	}

	/**
	 * Get Group
	 * 
	 * Get the group of the condition
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_group() {}

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
	 * Get Name
	 * 
	 * Get the title of the module
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_title() {}

	/**
	 * Render
	 *
	 * @since 2.2.0
	 *
	 * @access protected
	 *
	 * @param mixed  $key  	The key of the field
	 */
	public function render( $key ) {}
}
