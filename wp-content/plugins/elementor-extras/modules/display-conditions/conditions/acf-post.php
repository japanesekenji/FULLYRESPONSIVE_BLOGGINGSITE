<?php
namespace ElementorExtras\Modules\DisplayConditions\Conditions;

// Extras for Elementor Classes
use ElementorExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions\Acf_Post
 *
 * @since  2.2.0
 */
class Acf_Post extends Acf_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_name() {
		return 'acf_post';
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
		return __( 'ACF Post', 'elementor-extras' );
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
			'description'	=> __( 'Search ACF "Post Object" and "Relationship" fields by label.', 'elementor-extras' ),
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
			'field_type'	=> [
				'post',
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
		return [
			'type' 			=> 'ee-query',
			'default' 		=> '',
			'placeholder'	=> __( 'Search Posts', 'elementor-extras' ),
			'description'	=> __( 'Select multiple posts to match for any of them. Leave blank to check if the field is set.', 'elementor-extras' ),
			'label_block' 	=> true,
			'multiple'		=> true,
			'query_type'	=> 'posts',
			'object_type'	=> 'any',
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
	public function check( $key, $operator, $values ) {
		$show = false;

		$field_value = $this->get_field( $key );

		if ( $field_value ) {
			if ( ! $values || '' === $values || empty( $values ) ) {
				return $this->compare( true, true, $operator );
			}

			$values 		= (array)$values;
			$field_post_ids = $this->parse_field_values( $field_value );
			$value_post_ids = array_map('intval', $values );

			$show = ! empty( array_intersect( $field_post_ids, $value_post_ids ) );
		}

		return $this->compare( $show, true, $operator );
	}

	/**
	 * Parse field values
	 *
	 * Depending on the type of field and return formats
	 * this function returns an array with the post IDs set in
	 * the field settings
	 *
	 * @since 2.2.0
	 *
	 * @access public
	 *
	 * @param 	string  	$posts  			The posts saved in the field
	 * @return 	array   	$return_values  	The array of post IDs
	 */
	public function parse_field_values( $posts ) {
		$return_values = [];

		if ( is_array( $posts ) ) {
			foreach ( $posts as $post ) {
				$return_values[] = ( is_a( $post, 'WP_Post' ) ) ? $post->ID : $post;
			}
		} else {
			$return_values[] = ( is_a( $posts, 'WP_Post' ) ) ? $posts->ID : $posts;
		}

		return $return_values;
	}
}
