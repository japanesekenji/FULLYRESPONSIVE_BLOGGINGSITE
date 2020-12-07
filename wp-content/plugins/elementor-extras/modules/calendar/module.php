<?php
namespace ElementorExtras\Modules\Calendar;

// Extras for Elementor Classes
use ElementorExtras\Base\Module_Base;
use ElementorExtras\Modules\CustomFields\Module as CustomFieldsModule;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\Calendar\Module
 *
 * @since  2.0.0
 */
class Module extends Module_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_name() {
		return 'calendar';
	}

	/**
	 * Get Widgets
	 * 
	 * Get the modules' widgets
	 *
	 * @since  2.0.0
	 * @return array
	 */
	public function get_widgets() {
		return [
			'Calendar',
		];
	}

	/**
	 * Get Post Type Fields
	 * 
	 * Loops through all posts of post type
	 * and calls the appropriate fields type to fetch
	 * all available custom fields
	 *
	 * @since  2.0.0
	 * @param  post_type|string 	The post type
	 * @param  fields_type|string 	The fields type. Can be 'acf', 'toolset' or 'pods'
	 * @return array 				The meta fields
	 */
	public static function get_post_type_fields( $post_type = 'post', $fields_type = 'acf' ) {

		// Return the fields for this cpt
		$meta_fields = [];
		$customfields = new CustomFieldsModule();

		// Fetch all posts of this type
		$the_query = new \WP_Query( [
			'post_type' => $post_type,
		] );

		if ( $the_query->have_posts() ) {

			while ( $the_query->have_posts() ) { $the_query->the_post();
				
				$component = $customfields->get_component( $fields_type );
				$fields = $component->get_fields( get_the_ID() );

				if ( $fields ) {
					foreach( $fields as $name => $value ) {
						$meta_fields[ $name ] = $value;
					}
				}
			}
			wp_reset_postdata();
		}

		return $meta_fields;
	}
}
