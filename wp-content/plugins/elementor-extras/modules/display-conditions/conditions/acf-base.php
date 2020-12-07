<?php
namespace ElementorExtras\Modules\DisplayConditions\Conditions;

// Extras for Elementor Classes
use ElementorExtras\Base\Condition;
use ElementorExtras\Utils;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions\Acf_Base
 *
 * @since  2.2.0
 */
class Acf_Base extends Condition {

	protected $query_options_defaults = [
		'show_type' => false,
		'show_field_type' => true,
		'include_option' => true,
		'show_group' => true,
	];

	public function get_name_control_options() {
		return [
			'type' 			=> 'ee-query',
			'post_type' 	=> '',
			'options' 		=> [],
			'query_type' 	=> 'acf',
			'label_block' 	=> true,
			'multiple' 		=> false,
			'query_options'	=> $this->get_query_options(),
		];
	}

	public function get_query_options() {
		return wp_parse_args( $this->get_additional_query_options(), $this->query_options_defaults );
	}

	/**
	 * Additional query control options
	 *
	 * @since  2.2.33
	 * @return bool
	 */
	protected function get_additional_query_options() {
		return [];
	}

	/**
	 * Checks if current condition is supported
	 *
	 * @since  2.2.0
	 * @return bool
	 */
	public static function is_supported() {
		return class_exists( '\acf' );
	}

	/**
	 * Get Group
	 * 
	 * Get the group of the condition
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_group() {
		return 'acf';
	}

	/**
	 * Get Field Post
	 * 
	 * Retrieve the ACF field post object by id
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_field_post( $post_id ) {
		global $post;

		$field_post = get_posts( [
			'post__in'		=> [ $post_id ],
			'post_type'   	=> 'acf-field',
  			'post_status' 	=> 'publish',
			'numberposts' 	=> 1,
		] );

		if ( $field_post[0] ) {
			return $field_post;
		}

		return false;
	}

	/**
	 * Get Condition Data for Field
	 * 
	 * Returns either a name + key pair or either of the two
	 *
	 * @since  	2.2.0
	 * @param 	$key 	string 	The saved condition value. Either 'field_xxxxx' or 'option:field_xxxxx' or 'field_name:field_xxxx'
	 * @param 	$part 	bool 	Used to return only name or key. False for both.
	 *
	 * @return 	string
	 */
	protected function get_condition_data( $key, $part = false ) {
		if ( ! $key ) {
			return false;
		}

		if ( false === strpos( $key, ':' ) ) { // Fallback for 2.2.32

			$field_name = false;
			$field_key 	= $key;

		} else {
			list( $field_name, $field_key ) = explode( ':', $key );
		}

		switch ( $part ) {
			case 'name':
				return $field_name;
				break;

			case 'key':
				return $field_key;
				break;
			
			default:
				return [ $field_name, $field_key ];
				break;
		}
	}

	/**
	 * Wrapper for ACF's get_field
	 * 
	 * Parses the saved condition value and returns the ACF field value
	 *
	 * @since  	2.2.0
	 * @param 	$key 	string 	The saved condition value
	 *
	 * @return 	string
	 */
	protected function get_field( $key, $post_id = false, $format_value = true ) {
		if ( ! $key ) {
			return false;
		}

		$condition_data = $this->get_condition_data( $key );

		if ( ! $condition_data ) {
			return;
		}

		$field_name 	= $condition_data[0];
		$field_key 		= $condition_data[1];

		$options_ids 	= Utils::get_acf_options_pages_ids();
		$field_object 	= get_field_object( $field_key );

		if ( 'option' == $field_name && in_array( $field_object['parent'], $options_ids ) ) {
			$post_id = 'option';
		}

		return get_field( $field_key, $post_id, $format_value );
	}

	/**
	 * Wrapper for ACF's get_field_object
	 *
	 * @since  	2.2.0
	 * @param 	$key 	string 	The saved condition value
	 *
	 * @return 	string
	 */
	protected function get_field_object( $key ) {
		list( $field_name, $field_key ) = $this->get_condition_data( $key );

		return get_field_object( $field_key, $field_name );
	}

	/**
	 * Get Name Control Defaults
	 * 
	 * Get the settings for the name control
	 *
	 * @since  2.2.0
	 * @return array
	 */
	public function get_name_control_defaults() {
		return ;
	}
}
