<?php
namespace ElementorExtras\Modules\Search;

// Extras for Elementor Classes
use ElementorExtras\Utils;
use ElementorExtras\Base\Module_Base;
use ElementorExtras\Modules\Search\Conditions;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\Search\Module
 *
 * @since  2.1.0
 */
class Module extends Module_Base {

	/**
	 * Constructor
	 *
	 * Hook into Elementor to register the widgets
	 *
	 * @access public
	 * @since  2.1.0
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		
		add_filter( 'query_vars', [ $this, 'register_query_vars' ] );
		add_action( 'pre_get_posts', [ $this, 'pre_get_posts' ], 1 );

		if (  is_plugin_active( 'relevanssi/relevanssi.php' ) ) {
			add_filter( 'elementor_extras/widgets/posts/query', [ $this, 'add_relevanssi_compatibility' ], 10, 1 );
		}

		if ( is_elementor_pro_active() ) {
			add_action( 'elementor/theme/register_conditions', [ $this, 'register_conditions' ] );
		}
	}

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_name() {
		return 'search';
	}

	/**
	 * Get Widgets
	 * 
	 * Get the modules' widgets
	 *
	 * @since  2.1.0
	 * @return array
	 */
	public function get_widgets() {
		return [
			'Search_Form',
		];
	}

	/**
	 * Register Custom Query Vars
	 *
	 * @since  2.1.0
	 * @return array
	 * @link   https://codex.wordpress.org/Plugin_API/Filter_Reference/query_vars
	 */
	public function register_query_vars( $vars ) {
		$vars[] = 'ee_search_query';
		$vars[] = 'ee_search_id';

		return $vars;
	}

	/**
	 * Register Theme Builder Conditions
	 *
	 * @since  2.1.0
	 * @access public
	 * @param  Conditions_Manager $conditions_manager
	 */
	public function register_conditions( $conditions_manager ) {
		$woocommerce_condition = new Conditions\Search_Id();

		$conditions_manager->get_condition( 'search' )->register_sub_condition( $woocommerce_condition );
	}

	/**
	 * Pre Get Posts
	 *
	 * Filter search results query
	 *
	 * @since  2.1.0
	 * @return array
	 * @link   https://codex.wordpress.org/Plugin_API/Filter_Reference/query_vars
	 */
	function pre_get_posts( $query ) {
		
		if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ){
			return;
		}

		// Save query vars separately
		$query_vars = $query->query_vars;

		if ( ! array_key_exists( 'ee_search_query', $query_vars ) ) {
			return;
		}

		// Check if search query get var exists
		if ( $query_vars['ee_search_query'] ) {

			// Decode both url and json
			$search_query = json_decode( stripcslashes( $query_vars['ee_search_query'] ), JSON_UNESCAPED_SLASHES );

			if ( ! is_array( $search_query ) ) {
				return;
			}

			// Set post types
			if ( isset( $search_query['post_type'] ) ) {
				// Set the query var
				$query->set( 'post_type', $search_query['post_type'] );
			}

			// Set post authors
			if ( isset( $search_query['author'] ) ) {
				// Set the query var
				$query->set( 'author__in', $search_query['author'] );
			}

			// Get taxnomnies as names
			$taxonomies  = get_taxonomies( [ 'show_in_nav_menus' => true ] );
			$tax_queries = [];

			// Loop through all public taxonomies
			foreach ( $taxonomies as $taxonomy ) {

				if ( ! array_key_exists( $taxonomy, $search_query ) ) { // Taxnonomy appears in restrictions
					continue;
				}

				// If no taxnomy terms sent
				if ( ! $search_query[ $taxonomy ] ) {
					continue;
				}

				$terms = $search_query[ $taxonomy ];
				$operator = 'IN';

				if ( is_array( $search_query[ $taxonomy ] ) && in_array( 'all', $search_query[ $taxonomy ] ) ) {
					array_splice( $terms, array_search( 'all', $terms ), 1 );
				} else if ( 'all' === $terms ) {
					$terms = array_keys( Utils::get_terms_options( $taxonomy, 'slug', false ) );
				} else if ( 'any' === $terms ) {
					continue;
				}

				// Add to tax query array
				$tax_queries[] = array(
					'taxonomy' 	=> $taxonomy,
					'field' 	=> 'slug',
					'operator'	=> $operator,
					'terms' 	=> $terms,
				);
			}

			if ( count( $tax_queries ) > 1 ) {
				$tax_queries['relation'] = 'AND';
			}

			// Set the query var
			$query->set( 'tax_query', $tax_queries );
		}

		return $query;
	}

	/**
	 * Add Relevanssi compatibility
	 *
	 * Replaces the default query by running Relevanssi logic on it
	 * if the search form id is present in the query vars
	 *
	 * @since  2.2.2
	 * @return array
	 * @link   https://codex.wordpress.org/Plugin_API/Filter_Reference/query_vars
	 */
	public function add_relevanssi_compatibility( $query ) {
		if ( function_exists( 'relevanssi_do_query' ) && is_search() && get_query_var( 'ee_search_id' ) ) {
			$relevanssi_query = new \WP_Query();
			$relevanssi_query->parse_query( $query->query_vars );
			relevanssi_do_query( $relevanssi_query );

			return $relevanssi_query;
		}

		return $query;
	}
}
