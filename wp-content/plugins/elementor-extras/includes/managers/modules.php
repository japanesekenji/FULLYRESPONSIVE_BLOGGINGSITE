<?php
namespace ElementorExtras;

use ElementorExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Modules_Manager {

	/**
	 * Registered modules.
	 *
	 * Holds the list of all the registered modules.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @var array
	 */
	public $_modules = [];

	/**
	 * Get modules names.
	 *
	 * Retrieve the modules names.
	 *
	 * @since 2.2.30
	 * @access public
	 *
	 * @return string[] Modules names.
	 */
	public function get_modules_names() {
		return [
			'posts',
			'gallery',
			'scroll-indicator',
			'hotspots',
			'switcher',
			'devices',
			'calendar',
			'navigation',
			'search',
			'toggle',
			'map',
			'unfold',
			'media-player',
			'circle-progress',
			'image',
			'popup',
			'buttons',
			'svg',
			'heading',
			'table',
			'breadcrumbs',
			'templates-control',
			'query-control',
			'display-conditions',
		];
	}

	/**
	 * @since 0.1.0
	 */
	public function register_modules() {

		foreach ( $this->get_modules_names() as $module_name ) {

			$class_name = str_replace( '-', ' ', $module_name );

			$class_name = str_replace( ' ', '', ucwords( $class_name ) );

			$class_name = __NAMESPACE__ . '\\Modules\\' . $class_name . '\Module';

			if ( ! $class_name::is_supported() ) {
				continue;
			} else {
				$this->_modules[ $module_name ] = $class_name::instance();
			}
		}
	}

	/**
	 * @param string $module_name
	 *
	 * @return Module_Base|Module_Base[]
	 */
	public function get_modules( $module_name = null ) {
		if ( $module_name ) {
			if ( isset( $this->_modules[ $module_name ] ) ) {
				return $this->_modules[ $module_name ];
			}
			return null;
		}

		return $this->_modules;
	}

	private function require_files() {
		require( ELEMENTOR_EXTRAS_PATH . 'base/module.php' );
	}

	public function __construct() {
		$this->require_files();
		$this->register_modules();
	}
}
