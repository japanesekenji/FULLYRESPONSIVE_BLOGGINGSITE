<?php

namespace ElementorExtras\Extensions;

// Elementor Extras classes
use ElementorExtras\Utils;
use ElementorExtras\Modules\DisplayConditions\Module;
use ElementorExtras\Base\Extension_Base;
use ElementorExtras\Controls\Control_Query as QueryControl;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Conditions Extension
 *
 * Adds display conditions to elements
 *
 * @since 2.0.0
 */
class Extension_Display_Conditions extends Extension_Base {

	/**
	 * Is Common Extension
	 *
	 * Defines if the current extension is common for all element types or not
	 *
	 * @since 2.0.0
	 * @access protected
	 *
	 * @var bool
	 */
	protected $is_common = true;

	/**
	 * A list of scripts that the widgets is depended in
	 *
	 * @since 2.0.0
	 **/
	public function get_script_depends() {
		return [];
	}

	/**
	 * The description of the current extension
	 *
	 * @since 2.-.0
	 **/
	public static function get_description() {
		return __( 'Adds display conditions to widgets and sections allowing you to show them depending on authentication, roles, date and time of day.', 'elementor-extras' );
	}

	/**
	 * Is disabled by default
	 *
	 * Return wether or not the extension should be disabled by default,
	 * prior to user actually saving a value in the admin page
	 *
	 * @access public
	 * @since 2.0.0
	 * @return bool
	 */
	public static function is_default_disabled() {
		return true;
	}

	/**
	 * Add common sections
	 *
	 * @since 2.0.0
	 *
	 * @access protected
	 */
	protected function add_common_sections_actions() {

		// Activate sections for widgets
		add_action( 'elementor/element/common/section_custom_css/after_section_end', function( $element, $args ) {
			$this->add_common_sections( $element, $args );
		}, 10, 2 );

		// Activate sections for sections
		add_action( 'elementor/element/section/section_custom_css/after_section_end', function( $element, $args ) {
			$this->add_common_sections( $element, $args );
		}, 10, 2 );

		// Activate sections for widgets if elementor pro
		add_action( 'elementor/element/common/section_custom_css_pro/after_section_end', function( $element, $args ) {
			$this->add_common_sections( $element, $args );
		}, 10, 2 );

	}

	/**
	 * Add Actions
	 *
	 * @since 2.0.0
	 *
	 * @access protected
	 */
	protected function add_actions() {
		$module = \ElementorExtras\ElementorExtrasPlugin::instance()->modules_manager->get_modules( 'display-conditions' );
		$module->add_actions();
	}
}