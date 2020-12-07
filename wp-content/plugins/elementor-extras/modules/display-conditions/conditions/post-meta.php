<?php
namespace ElementorExtras\Modules\DisplayConditions\Conditions;

// Extras for Elementor Classes
use ElementorExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions\Condition
 *
 * @since  2.2.27
 */
class Post_Meta extends Condition {

	/**
	 * Get Group
	 * 
	 * Get the group of the condition
	 *
	 * @since  2.2.27
	 * @return string
	 */
	public function get_group() {
		return 'single';
	}

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.2.27
	 * @return string
	 */
	public function get_name() {
		return 'post_meta';
	}

	/**
	 * Get Title
	 * 
	 * Get the title of the module
	 *
	 * @since  2.2.27
	 * @return string
	 */
	public function get_title() {
		return __( 'Post Meta', 'elementor-extras' );
	}

	/**
	 * Get Name Control
	 * 
	 * Get the settings for the name control
	 *
	 * @since  2.2.27
	 * @return array
	 */
	public function get_name_control() {
		return [
			'type' 			=> Controls_Manager::TEXT,
			'default' 		=> '',
			'placeholder'	=> __('meta_key', 'elementor-extras'),
			'label_block' 	=> true,
		];
	}

	/**
	 * Get Value Control
	 * 
	 * Get the settings for the value control
	 *
	 * @since  2.2.27
	 * @return array
	 */
	public function get_value_control() {
		return [
			'type' 			=> Controls_Manager::TEXT,
			'default' 		=> '',
			'placeholder'	=> __('meta_value', 'elementor-extras'),
			'description'	=> __('Leave empty to check if the current post has any meta value for the selected key.', 'elementor-extras'),
			'label_block' 	=> true,
		];
	}

	/**
	 * Check condition
	 *
	 * @since 2.2.27
	 *
	 * @access public
	 *
	 * @param string  	$name  		The control name to check
	 * @param string 	$operator  	Comparison operator
	 * @param mixed  	$value  	The control value to check
	 */
	public function check( $key, $operator, $value ) {
		$show = false;

		if ( $key ) {
			$meta_values = get_post_meta( get_the_ID(), $key ); // Returns false if no matching meta key is set

			if ( ( ! $value || '' === trim( $value ) || empty( $value ) ) && $meta_values ) {
				return $this->compare( true, true, $operator );
			}

			foreach ( $meta_values as $meta_value ) {
				if ( $value === $meta_value ) {
					$show = true;
					break;
				}
			}
		} else {
			$show = true;
		}

		return $this->compare( $show, true, $operator );
	}
}
