<?php
namespace ElementorExtras\Modules\DisplayConditions\Conditions;

// Extras for Elementor Classes
use ElementorExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions\Term_Archive
 *
 * @since  2.2.0
 */
class Term_Archive extends Condition {

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
		return 'term_archive';
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
		return __( 'Term', 'elementor-extras' );
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
			'description'	=> __( 'Leave blank or select all for any term.', 'elementor-extras' ),
			'type' 			=> 'ee-query',
			'post_type' 	=> '',
			'options' 		=> [],
			'label_block' 	=> true,
			'multiple' 		=> true,
			'query_type' 	=> 'terms',
			'include_type' 	=> true,
		];
	}

	/**
	 * Checks a given taxonomy term against the current page template
	 *
	 * @since 2.1.2
	 *
	 * @access protected
	 *
	 * @param string  $taxonomy  The taxonomy to check against
	 */
	protected function check_term_archive_type( $term ) {
		if ( is_category( $term ) ) {
			return true;
		} else if ( is_tag( $term ) ) {
			return true;
		} else if ( is_tax() ) {
			if ( is_tax( get_queried_object()->taxonomy, $term ) ) {
				return true;
			}
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
				$show = $this->check_term_archive_type( $_value );
				if ( $show ) break;
			}
		} else { $show = $this->check_term_archive_type( $value ); }

		return $this->compare( $show, true, $operator );
	}
}
