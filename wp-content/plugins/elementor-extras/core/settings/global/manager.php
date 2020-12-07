<?php
namespace ElementorExtras\Core\Settings\General;

use Elementor\Controls_Manager;
use Elementor\Core\Settings\General\Manager as GeneralManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Manager extends GeneralManager {

	const PANEL_TAB_SETTINGS = 'settings';

	const META_KEY = '_elementor_extras_general_settings';

	/**
	 * @since 1.8.0
	 * @access public
	 */
	public function __construct() {
		parent::__construct();

		$this->add_panel_tabs();
	}

	/**
	 * @since 1.8.0
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return 'extras';
	}

	/**
	 * @since 1.8.0
	 * @access private
	 */
	private function add_panel_tabs() {
		Controls_Manager::add_tab( self::PANEL_TAB_SETTINGS, __( 'Settings', 'elementor-extras' ) );
	}

	/**
	 * Get saved settings.
	 *
	 * Retrieve the saved settings from the site options.
	 *
	 * @since 2.0.0
	 * @access protected
	 *
	 * @param int $id Post ID.
	 *
	 * @return array Saved settings.
	 */
	protected function get_saved_settings( $id ) {
		$model_controls = Model::get_controls_list();

		$settings = [];

		foreach ( $model_controls as $tab_name => $sections ) {

			foreach ( $sections as $section_name => $section_data ) {

				foreach ( $section_data['controls'] as $control_name => $control_data ) {
					$saved_setting = get_option( $control_name, null );

					if ( null !== $saved_setting ) {
						$settings[ $control_name ] = get_option( $control_name );
					}
				}
			}
		}

		return $settings;
	}

	/**
	 * Save settings to DB.
	 *
	 * Save general settings to the database, as site options.
	 *
	 * @since 2.0.0
	 * @access protected
	 *
	 * @param array $settings Settings.
	 * @param int   $id       Post ID.
	 */
	protected function save_settings_to_db( array $settings, $id ) {
		$model_controls = Model::get_controls_list();

		$one_list_settings = [];

		foreach ( $model_controls as $tab_name => $sections ) {

			foreach ( $sections as $section_name => $section_data ) {

				foreach ( $section_data['controls'] as $control_name => $control_data ) {
					if ( isset( $settings[ $control_name ] ) ) {
						$one_list_control_name = str_replace( 'elementor_', '', $control_name );

						$one_list_settings[ $one_list_control_name ] = $settings[ $control_name ];

						update_option( $control_name, $settings[ $control_name ] );
					} else {
						delete_option( $control_name );
					}
				}
			}
		}

		// Save all settings in one list for a future usage
		if ( ! empty( $one_list_settings ) ) {
			update_option( self::META_KEY, $one_list_settings );
		} else {
			delete_option( self::META_KEY );
		}
	}
}
