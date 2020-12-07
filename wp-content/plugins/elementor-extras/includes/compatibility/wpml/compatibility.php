<?php
namespace ElementorExtras\Compatibility;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elemento Extras WPML Compatibility
 *
 * Registers translatable widgets
 *
 * @since 1.8.8
 */
class WPML {

	/**
	 * @since 1.8.8
	 * @var Object
	 */
	public static $instance = null;

	/**
	 * Returns the class instance
	 * 
	 * @since 1.8.8
	 *
	 * @return Object
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor for the class
	 *
	 * @since 1.8.8
	 *
	 * @return void
	 */
	public function __construct() {

		// WPML String Translation plugin exist check
		if ( is_wpml_string_translation_active() && class_exists( 'WPML_Elementor_Module_With_Items' ) ) {

			$this->includes();

			add_filter( 'wpml_elementor_widgets_to_translate', [ $this, 'add_translatable_nodes' ] );
		}
	}

	/**
	 * Include widget integration classes
	 *
	 * @since 1.8.8
	 *
	 * @return void
	 */
	public function includes() {
		elementor_extras_include( 'includes/compatibility/wpml/modules/calendar.php' );
		elementor_extras_include( 'includes/compatibility/wpml/modules/buttons.php' );
		elementor_extras_include( 'includes/compatibility/wpml/modules/google-map.php' );
		elementor_extras_include( 'includes/compatibility/wpml/modules/hotspots.php' );
		elementor_extras_include( 'includes/compatibility/wpml/modules/offcanvas.php' );
		elementor_extras_include( 'includes/compatibility/wpml/modules/timeline.php' );
		elementor_extras_include( 'includes/compatibility/wpml/modules/table.php' );
		elementor_extras_include( 'includes/compatibility/wpml/modules/table-header.php' );
		elementor_extras_include( 'includes/compatibility/wpml/modules/toggle-element.php' );
		elementor_extras_include( 'includes/compatibility/wpml/modules/switcher.php' );
		elementor_extras_include( 'includes/compatibility/wpml/modules/scroll-indicator.php' );
	}

	/**
	 * Adds additional translatable nodes to WPML
	 *
	 * @since 1.8.8
	 *
	 * @param  array   $nodes_to_translate WPML nodes to translate
	 * @return array   $nodes_to_translate Updated nodes
	 */
	public function add_translatable_nodes( $nodes_to_translate ) {

		$nodes_to_translate[ 'ee-breadcrumbs' ] = [
			'conditions' 		=> [ 'widgetType' => 'ee-breadcrumbs' ],
			'fields'     		=> [
				[
					'field'       => 'home_text',
					'type'        => esc_html__( 'Breadcrumbs: Home Text', 'elementor-extras' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'ee-slide-menu' ] = [
			'conditions' 		=> [ 'widgetType' => 'ee-slide-menu' ],
			'fields'     		=> [
				[
					'field'       => 'back_text',
					'type'        => esc_html__( 'Slide Menu: Back Text', 'elementor-extras' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'button-group' ] = [
			'conditions' 		=> [ 'widgetType' => 'button-group' ],
			'fields'     		=> [],
			'integration-class' => '\ElementorExtras\Compatibility\WPML\Buttons',
		];

		$nodes_to_translate[ 'ee-calendar' ] = [
			'conditions' 		=> [ 'widgetType' => 'ee-calendar' ],
			'fields'     		=> [],
			'integration-class' => '\ElementorExtras\Compatibility\WPML\Calendar',
		];

		$nodes_to_translate[ 'ee-calendar' ] = [
			'conditions' 		=> [ 'widgetType' => 'ee-calendar' ],
			'fields'     		=> [
				[
					'field'       => 'event_list_heading',
					'type'        => esc_html__( 'Calendar: Events List Heading', 'elementor-extras' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'circle-progress' ] = [
			'conditions' 		=> [ 'widgetType' => 'circle-progress' ],
			'fields'     		=> [
				[
					'field'       => 'suffix',
					'type'        => __( 'Circle Progress: Suffix', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'text',
					'type'        => __( 'Circle Progress: Content', 'elementor-extras' ),
					'editor_type' => 'VISUAL'
				],
			],
		];

		$nodes_to_translate[ 'heading-extended' ] = [
			'conditions' 		=> [ 'widgetType' => 'heading-extended' ],
			'fields'     		=> [
				[
					'field'       => 'title',
					'type'        => __( 'Heading Extra: Title', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
			],
		];

		$nodes_to_translate[ 'text-divider' ] = [
			'conditions' 		=> [ 'widgetType' => 'text-divider' ],
			'fields'     		=> [
				[
					'field'       => 'text',
					'type'        => __( 'Text Divider: Heading', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
			],
		];

		$nodes_to_translate[ 'ee-google-map' ] = [
			'conditions' 		=> [ 'widgetType' => 'ee-google-map' ],
			'fields'     		=> [
				[
					'field'       => 'all_text',
					'type'        => __( 'Google Map: All Locations Text', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
			],
			'integration-class' => '\ElementorExtras\Compatibility\WPML\Google_Map',
		];

		$nodes_to_translate[ 'hotspots' ] = [
			'conditions' 		=> [ 'widgetType' => 'hotspots' ],
			'fields'     		=> [],
			'integration-class' => '\ElementorExtras\Compatibility\WPML\Hotspots',
		];

		$nodes_to_translate[ 'ee-offcanvas' ] = [
			'conditions' 		=> [ 'widgetType' => 'ee-offcanvas' ],
			'fields'     		=> [
				[
					'field'       => 'trigger_text',
					'type'        => __( 'Offcanvas: Trigger Text', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'header_title_text',
					'type'        => __( 'Offcanvas: Header Title', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
			],
			'integration-class' => '\ElementorExtras\Compatibility\WPML\Offcanvas',
		];

		$nodes_to_translate[ 'ee-age-gate' ] = [
			'conditions' 		=> [ 'widgetType' => 'ee-age-gate' ],
			'fields'     		=> [
				[
					'field'       => 'denied',
					'type'        => __( 'Age Gate: Denied Message', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'title',
					'type'        => __( 'Age Gate: Title', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'description',
					'type'        => __( 'Age Gate: Description', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'button_text',
					'type'        => __( 'Age Gate: Button Label', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
			],
		];

		$nodes_to_translate[ 'ee-popup' ] = [
			'conditions' 		=> [ 'widgetType' => 'ee-popup' ],
			'fields'     		=> [
				[
					'field'       => 'popup_trigger_text',
					'type'        => __( 'Popup: Trigger Label', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'popup_title',
					'type'        => __( 'Popup: Title', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'popup_close_button_text',
					'type'        => __( 'Popup: Close Button Label', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'popup_content',
					'type'        => __( 'Popup: Content', 'elementor-extras' ),
					'editor_type' => 'VISUAL'
				],
			],
		];

		$nodes_to_translate[ 'image-comparison' ] = [
			'conditions' 		=> [ 'widgetType' => 'image-comparison' ],
			'fields'     		=> [
				[
					'field'       => 'original_label',
					'type'        => __( 'Image Comparison: Original Label', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'modified_label',
					'type'        => __( 'Image Comparison: Modified Label', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
			],
		];

		$nodes_to_translate[ 'posts-extra' ] = [
			'conditions' 		=> [ 'widgetType' => 'posts-extra' ],
			'fields'     		=> [
				[
					'field'       => 'classic_filters_all_text',
					'type'        => __( 'Posts Extra: Filter All Text', 'elementor-extras' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'post_read_more_text',
					'type'        => __( 'Posts Extra: Read More Text', 'elementor-extras' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'classic_infinite_scroll_loading_text',
					'type'        => __( 'Posts Extra: Infinite Scroll Loading Status Text', 'elementor-extras' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'classic_infinite_scroll_last_text',
					'type'        => __( 'Posts Extra: Infinite Scroll Last Text', 'elementor-extras' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'classic_infinite_scroll_error_text',
					'type'        => __( 'Posts Extra: Infinite Scroll Error Text', 'elementor-extras' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'classic_infinite_scroll_button_text',
					'type'        => __( 'Posts Extra: Infinite Scroll Button Text', 'elementor-extras' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'post_terms_prefix',
					'type'        => __( 'Posts Extra: Terms Prefix', 'elementor-extras' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'post_author_prefix',
					'type'        => __( 'Posts Extra: Author Prefix', 'elementor-extras' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'post_date_prefix',
					'type'        => __( 'Posts Extra: Date Prefix', 'elementor-extras' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'post_comments_suffix',
					'type'        => __( 'Posts Extra: Comments Suffix', 'elementor-extras' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'post_comments_prefix',
					'type'        => __( 'Posts Extra: Comments Prefix', 'elementor-extras' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'ee-search-form' ] = [
			'conditions' 		=> [ 'widgetType' => 'ee-search-form' ],
			'fields'     		=> [
				[
					'field'       => 'input_placeholder',
					'type'        => __( 'Search Form: Input Placeholder', 'elementor-extras' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'classic_button_label_text',
					'type'        => __( 'Search Form: Classic Skin Button Label Text', 'elementor-extras' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'classic_button_label_text',
					'type'        => __( 'Search Form: Classic Skin Button Label Text', 'elementor-extras' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'timeline' ] = [
			'conditions' 		=> [ 'widgetType' => 'timeline' ],
			'fields'     		=> [],
			'integration-class' => '\ElementorExtras\Compatibility\WPML\Timeline',
		];

		$nodes_to_translate[ 'ee-switcher' ] = [
			'conditions' 		=> [ 'widgetType' => 'ee-switcher' ],
			'fields'     		=> [],
			'integration-class' => '\ElementorExtras\Compatibility\WPML\Switcher',
		];

		$nodes_to_translate[ 'ee-scroll-indicator' ] = [
			'conditions' 		=> [ 'widgetType' => 'ee-scroll-indicator' ],
			'fields'     		=> [],
			'integration-class' => '\ElementorExtras\Compatibility\WPML\Scroll_Indicator',
		];

		$nodes_to_translate[ 'table' ] = [
			'conditions' 		=> [ 'widgetType' => 'table' ],
			'fields'     		=> [],
			'integration-class' => '\ElementorExtras\Compatibility\WPML\Table',
		];

		$nodes_to_translate[ 'ee-toggle-element' ] = [
			'conditions' 		=> [ 'widgetType' => 'ee-toggle-element' ],
			'fields'     		=> [],
			'integration-class' => '\ElementorExtras\Compatibility\WPML\Toggle_Element',
		];

		$nodes_to_translate[ 'unfold' ] = [
			'conditions' 		=> [ 'widgetType' => 'unfold' ],
			'fields'     		=> [
				[
					'field'       => 'content',
					'type'        => __( 'Unfold: Content', 'elementor-extras' ),
					'editor_type' => 'VISUAL'
				],
				[
					'field'       => 'text_closed',
					'type'        => __( 'Unfold: Open Button Label', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'text_open',
					'type'        => __( 'Unfold: Closed Button Label', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
			],
		];

		$nodes_to_translate[ 'devices' ] = [
			'conditions' 		=> [ 'widgetType' => 'devices' ],
			'fields'     		=> [
				[
					'field'       => 'video_url',
					'type'        => __( 'Devices: MP4 URL', 'elementor-extras' ),
					'editor_type' => 'VISUAL'
				],
				[
					'field'       => 'video_url_webm',
					'type'        => __( 'Devices: Webm URL', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'video_url_ogg',
					'type'        => __( 'Devices: OGG URL', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'video_url_webm',
					'type'        => __( 'Devices: M4V URL', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
			],
		];

		$nodes_to_translate[ 'html5-video' ] = [
			'conditions' 		=> [ 'widgetType' => 'html5-video' ],
			'fields'     		=> [
				[
					'field'       => 'video_url',
					'type'        => __( 'HTML5 Video: MP4 URL', 'elementor-extras' ),
					'editor_type' => 'VISUAL'
				],
				[
					'field'       => 'video_url_webm',
					'type'        => __( 'HTML5 Video: Webm URL', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'video_url_ogg',
					'type'        => __( 'HTML5 Video: OGG URL', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'video_url_webm',
					'type'        => __( 'HTML5 Video: M4V URL', 'elementor-extras' ),
					'editor_type' => 'LINE'
				],
			],
		];

		return $nodes_to_translate;
	}

	/**
	 * Returns the class instance.
	 *
	 * @since 1.8.8
	 *
	 * @return Object
	 */
	public static function get_instance() {
		
		if ( null == self::$instance )
			self::$instance = new self;

		return self::$instance;
	}
}