<?php
namespace ElementorExtras\Modules\DisplayConditions\Conditions;

// Extras for Elementor Classes
use ElementorExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions\Date_Archive
 *
 * @since  2.2.0
 */
class Date_Archive extends Condition {

	/**
	 * Get Group
	 * 
	 * Get the group of the condition
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_group() {
		return 'archive';
	}

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_name() {
		return 'date_archive';
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
		return __( 'Date', 'elementor-extras' );
	}

	/**
	 * Get Value Control
	 * 
	 * Get the settings for the value control
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_value_control() {
		return [
			'type' 			=> Controls_Manager::SELECT2,
			'default' 		=> '',
			'placeholder'	=> __( 'Any', 'elementor-extras' ),
			'description'	=> __( 'Leave blank or select all for any date based archive.', 'elementor-extras' ),
			'multiple'		=> true,
			'label_block' 	=> true,
			'options' 		=> [
				'day'		=> __( 'Day', 'elementor-extras' ),
				'month'		=> __( 'Month', 'elementor-extras' ),
				'year'		=> __( 'Year', 'elementor-extras' ),
			],
		];
	}

	/**
	 * Checks a given date type against the current page template
	 *
	 * @since 2.2.0
	 *
	 * @access protected
	 *
	 * @param string  $type  The type of date archive to check against
	 */
	protected function check_date_archive_type( $type ) {
		if ( 'day' === $type ) { // Day
			return is_day();
		} elseif ( 'month' === $type ) { // Month
			return is_month();
		} elseif ( 'year' === $type ) { // Year
			return is_year();
		}

		return false;
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
	public function check( $name = null, $operator, $value ) {
		$show = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( $this->check_date_archive_type( $_value ) ) {
					$show = true; break;
				}
			}
		} else { $show = is_date( $value ); }

		return $this->compare( $show, true, $operator );
	}
}
