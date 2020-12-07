<?php
namespace ElementorExtras\Modules\QueryControl\Types;

// Extras for Elementor Classes
use ElementorExtras\Utils;
use ElementorExtras\Modules\QueryControl\Types\Meta_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\QueryControl\Types\Acf
 *
 * @since  2.2.0
 */
class Acf extends Meta_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_name() {
		return 'acf';
	}

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_title() {
		return __( 'ACF', 'elementor-extras' );
	}

	/**
	 * Gets autocomplete values
	 *
	 * @since  2.2.0
	 * @return array
	 */
	public function get_autocomplete_values( array $data ) {
		$results 			= [];
		$options 			= $data['query_options'];
		$options_pages 		= Utils::get_acf_options_pages_ids();

		$query_params = [
			'post_type' 		=> 'acf-field',
			'post_status'		=> 'publish',
			'search_title_name' => $data['q'],
			'posts_per_page' 	=> -1,
		];

		if ( empty( $options['include_option'] ) || ! $options['include_option'] ) {
			$query_params['post_parent__not_in'] = $options_pages;
		}

		add_filter( 'posts_where', ['\ElementorExtras\Utils', 'posts_where_by_title_name'], 10, 2 );

		$query = new \WP_Query( $query_params );

		remove_filter( 'posts_where', ['\ElementorExtras\Utils', 'posts_where_by_title_name'], 10, 2 );

		$query_results = $this->get_query_results( $query, $options );

		foreach ( $query_results as $query_result ) {
			$results[] = [
				'id' => $query_result[0],
				'text' => $query_result[1],
			];
		}

		return $results;
	}

	/**
	 * Gets control values titles
	 *
	 * @since  2.2.0
	 * @return array
	 */
	public function get_value_titles( array $request ) {
		$values 			= (array)$request['id'];
		$keys 				= [];
		$results 			= [];
		$options 			= $request['query_options'];

		if ( ! empty( $options['include_option'] ) && true == $options['include_option'] ) {
			foreach ( $values as $value ) {
				list( $name, $key ) = explode( ':', $value );

				$keys[] = $key;
			}
		} else {
			$keys = $values;
		}

		$query = new \WP_Query( [
			'post_type' 		=> 'acf-field',
			'post_name__in' 	=> $keys,
			'posts_per_page' 	=> -1,
		] );

		$query_results = $this->get_query_results( $query, $options );

		foreach ( $query_results as $query_result ) {
			$results[ $query_result[0] ] = $query_result[1];
		}

		return $results;
	}

	protected function get_query_results( $query, $options ) {
		$results 			= [];
		$show_type 			= ! empty( $options['show_type'] ) && $options['show_type'];
		$show_field_type 	= ! empty( $options['show_field_type'] ) && $options['show_field_type'];
		$show_group 		= ! empty( $options['show_group'] ) && $options['show_group'];

		foreach ( $query->posts as $post ) {
			$field_settings 	= unserialize( $post->post_content );
			$field_type 		= $field_settings['type'];

			if ( ! $this->is_valid_field_type( $options['field_type'], $field_type ) ) {
				continue;
			}

			$display 			= $post->post_title;
			$display_type 		= $show_type ? $this->get_title() : '';
			$display_field_type = $show_field_type ? $this->get_acf_field_type_label( $field_type ) : '';
			$display_group 		= $show_group ? '(' . get_the_title( $post->post_parent ) . ')' : '';
			$display 			= $show_type || $show_field_type ? ': ' . $display : $display;

			$result_key 		= $this->get_results_field_key( $post, $options );
			$result_label 		= sprintf( '%1$s %2$s %3$s %4$s', $display_type, $display_field_type, $display, $display_group );

			$results[] = [ $result_key, $result_label ];
		}

		return $results;
	}

	/**
	 * Get key for query control results
	 *
	 * @since  2.2.33
	 * @param $post WP_Post The ACF field post
	 * @param $option array The control query options
	 * @return string
	 */
	protected  function get_results_field_key( $post, $options ) {
		if ( ! $post ) {
			return [];
		}

		$key = $post->post_name;
		$options_pages = Utils::get_acf_options_pages_ids();

		if ( ! empty( $options['include_option'] ) && true == $options['include_option'] ) {
			if ( in_array( $post->post_parent, $options_pages ) ) {
				$key = 'option:' . $key;
			} else {
				$key = $post->post_excerpt . ':' . $key;
			}
		}

		return $key;
	}

	/**
	 * Gets the acf control type label by field type
	 *
	 * @since  2.2.0
	 * @return array
	 */
	public function get_acf_field_type_label( $field_type ) {
		if ( ! function_exists( 'acf_get_field_type' ) )
			return;

		$field_type_object = acf_get_field_type( $field_type );

		if ( $field_type_object )
			return $field_type_object->label;

		return false;
	}

	/**
	 * Returns array of acf field types organized
	 * by category
	 *
	 * @since  2.2.5
	 * @return array
	 */
	public function get_field_types() {
		return [
			'textual' => [
				'text',
				'textarea',
				'number',
				'range',
				'email',
				'url',
				'password',
				'wysiwyg',
			],
			'date' => [
				'date_picker',
				'date_time_picker',
			],
			'option' => [
				'select',
				'checkbox',
				'radio',
			],
			'boolean' => [
				'true_false',
			],
			'post' => [
				'post_object',
				'relationship',
			],
			'taxonomy' => [
				'taxonomy',
			],
		];
	}
}
