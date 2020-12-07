<?php
namespace ElementorExtras\Modules\QueryControl\Types;

// Extras for Elementor Classes
use ElementorExtras\Modules\QueryControl\Types\Type_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\QueryControl\Types\Terms
 *
 * @since  2.2.0
 */
class Terms extends Type_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_name() {
		return 'terms';
	}

	/**
	 * Gets autocomplete values
	 *
	 * @since  2.2.0
	 * @return array
	 */
	public function get_autocomplete_values( array $data ) {
		$results = [];

		$taxonomies = get_object_taxonomies('');

		$query_params = [
			'taxonomy' 		=> $taxonomies,
			'search' 		=> $data['q'],
			'hide_empty' 	=> false,
		];

		$terms = get_terms( $query_params );

		foreach ( $terms as $term ) {
			$taxonomy = get_taxonomy( $term->taxonomy );

			$results[] = [
				'id' 	=> $term->term_id,
				'text' 	=> $taxonomy->labels->singular_name . ': ' . $term->name,
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
		$ids = (array) $request['id'];
		$results = [];

		$query_params = [
			'include' 		=> $ids,
		];

		$terms = get_terms( $query_params );

		foreach ( $terms as $term ) {
			$results[ $term->term_id ] = $term->name;
		}

		return $results;
	}
}
