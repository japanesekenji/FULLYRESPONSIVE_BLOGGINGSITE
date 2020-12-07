<?php
namespace ElementorExtras\Modules\DisplayConditions\Conditions;

// Extras for Elementor Classes
use ElementorExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions\Acf_Date_Time
 *
 * @since  2.2.0
 */
class Acf_Date_Time extends Acf_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_name() {
		return 'acf_date_time';
	}

	/**
	 * Get Title
	 * 
	 * Get the title of the module
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_title() {
		return __( 'ACF Date / Time', 'elementor-extras' );
	}

	/**
	 * Get Name Control
	 * 
	 * Get the settings for the name control
	 *
	 * @since  2.2.0
	 * @return array
	 */
	public function get_name_control() {
		return wp_parse_args( [
			'description'	=> __( 'Search ACF "Date" and "Date Time" fields by label.', 'elementor-extras' ),
			'placeholder'	=> __( 'Search Fields', 'elementor-extras' ),
		], $this->get_name_control_options() );
	}

	/**
	 * Additional query control options
	 *
	 * @since  2.2.33
	 * @return bool
	 */
	public function get_additional_query_options() {
		return [
			'field_type' => [
				'date',
			],
		];
	}

	/**
	 * Get Value Control
	 * 
	 * Get the settings for the value control
	 *
	 * @since  2.2.0
	 * @return array
	 */
	public function get_value_control() {
		$default_date_start = date( 'Y-m-d', strtotime( '-3 day' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );

		return [
			'label'		=> __( 'Before', 'elementor-extras' ),
			'type' 		=> \Elementor\Controls_Manager::DATE_TIME,
			'picker_options' => [
				'enableTime' => true,
			],
			'label_block'	=> true,
			'default' 		=> $default_date_start,
		];
	}

	/**
	 * Check condition
	 *
	 * @since 2.2.0
	 *
	 * @access public
	 *
	 * @param string  	$name  		The control name to check
	 * @param string 	$operator  	Comparison operator
	 * @param mixed  	$value  	The control value to check
	 */
	public function check( $key, $operator, $value ) {
		$show = false;

		global $post;

		$field_settings = $this->get_field_object( $key );

		if ( $field_settings ) {
			
			$field_format 	= $field_settings['return_format'];
			$field_db_value = $this->get_field( $key, false, false );

			// ACF saves values in these formats in the database
			// We use the db values to bypass days and months translations
			// not supported by PHP's DateTime
			$field_db_format = 'date_time_picker' === $field_settings['type'] ? 'Y-m-d H:i:s' : 'Ymd';

			// Create date based on saved format
			$date = \DateTime::createFromFormat( $field_db_format, $field_db_value );

			// Make sure it's a valid date
			if ( ! $date ) { return; }

			// Convert to timestamps
			$field_value_ts = strtotime( $date->format( $field_format ) ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
			$value_ts 		= strtotime( $value ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );

			// Set display condition
			$show = $field_value_ts < $value_ts;
		}

		return $this->compare( $show, true, $operator );
	}
}
