<?php
namespace ElementorExtras\Modules\Search\Conditions;

use Elementor\Controls_Manager;
use ElementorPro\Modules\ThemeBuilder as ThemeBuilder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Search_Id extends ThemeBuilder\Conditions\Condition_Base {

	public static function get_type() {
		return 'archive';
	}

	public static function get_priority() {
		return 71;
	}

	public function get_name() {
		return 'ee-search-id';
	}

	public function get_label() {
		return __( 'Extras Search Results', 'elementor-extras' );
	}

	public function check( $args = null ) {
		return is_search() && get_query_var('ee_search_id') === $args['id'];
	}

	protected function _register_controls() {
		$this->add_control(
			'search_form_id',
			[
				'section' 		=> 'settings',
				'type' 			=> Controls_Manager::TEXT,
				'placeholder'	=> __( 'Search ID', 'elementor-extras' )
			]
		);
	}
}
