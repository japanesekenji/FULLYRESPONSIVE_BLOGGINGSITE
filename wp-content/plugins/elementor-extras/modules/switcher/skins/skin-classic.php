<?php
namespace ElementorExtras\Modules\Switcher\Skins;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\Switcher\Skins
 *
 * @since  1.9.0
 */
class Skin_Classic extends Skin_Base {

	/**
	 * Get ID
	 * 
	 * Gets the current skin ID
	 *
	 * @since  1.9.0
	 * @return string
	 */
	public function get_id() {
		return 'classic';
	}

	/**
	 * Get Title
	 * 
	 * Gets the current skin title
	 *
	 * @since  1.9.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Classic', 'elementor-extras' );
	}

	/**
	 * Register Controls Actions
	 * 
	 * Registers controls at specific points in the Controls Stack
	 *
	 * @since  1.9.0
	 * @return void
	 */
	protected function _register_controls_actions() {
		parent::_register_controls_actions();

		// add_action( 'elementor/element/ee-switcher/section_query/after_section_end', [ $this, 'register_parallax_controls' ] );
	}

	/**
	 * Register Layout Content Controls
	 *
	 * @since 1.9.0
	 * @return void
	 */
	public function register_layout_content_controls() {
		parent::register_layout_content_controls();

	}
}