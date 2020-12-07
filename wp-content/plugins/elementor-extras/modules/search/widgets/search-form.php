<?php
namespace ElementorExtras\Modules\Search\Widgets;

// Extras for Elementor Classes
use ElementorExtras\Utils;
use ElementorExtras\Base\Extras_Widget;
use ElementorExtras\Modules\Search\Skins;
use ElementorExtras\Modules\Search\Module as Module;
use ElementorExtras\Group_Control_Transition;

// Elementor Classes
use Elementor\Utils as ElementorUtils;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Search
 *
 * @since 2.1.0
 */
class Search_Form extends Extras_Widget {

	/**
	 * Has template content
	 *
	 * @since  1.6.0
	 * @var    bool
	 */
	protected $_has_template_content = false;

	/**
	 * Has Inline Filters
	 *
	 * @since  2.1.0
	 * @var    bool
	 */
	public $_has_inline_filters = false;

	/**
	 * Has Inline Filters
	 *
	 * @since  2.1.0
	 * @var    bool
	 */
	public $_has_block_filters = false;

	/**
	 * Search Filters
	 *
	 * @since  2.1.0
	 * @var    bool
	 */
	public $_filters = [];

	/**
	 * Search Fields
	 *
	 * @since  2.1.0
	 * @var    bool
	 */
	public $_fields = [];

	/**
	 * Query Filters
	 *
	 * Used to generate hidden fields
	 *
	 * @since  2.1.0
	 * @var    bool
	 */
	public $_query_filters = [];

	/**
	 * Search Fields
	 *
	 * @since  2.1.0
	 * @var    bool
	 */
	public $_filter_types = [
		'post_type',
		'author',
		'taxonomies',
	];

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_name() {
		return 'ee-search-form';
	}

	/**
	 * Get Title
	 * 
	 * Get the title of the widget
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Search Form', 'elementor-extras' );
	}

	/**
	 * Get Icon
	 * 
	 * Get the icon of the widget
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_icon() {
		return 'nicon nicon-search-form';
	}

	/**
	 * Get Script Depends
	 * 
	 * A list of scripts that the widgets is depended in
	 *
	 * @since  2.1.0
	 * @return array
	 */
	public function get_script_depends() {
		return [ 'jquery-elementor-select2' ];
	}

	/**
	 * Get Style Depends
	 * 
	 * A list of css files that the widgets is depended in
	 *
	 * @since  2.1.0
	 * @return array
	 */
	public function get_style_depends() {
		return [ 'elementor-select2' ];
	}

	/**
	 * Get Search URL
	 * 
	 * Returns the complete search url with vars
	 *
	 * @since  2.1.0
	 * @return array
	 */
	public function get_search_url() {
		$url = home_url();

		return $url;
	}

	/**
	 * Add filter 'all' option
	 * 
	 * Shorthand for retrieving a control ID specific to filters
	 *
	 * @since  2.1.0
	 * @return array
	 */
	public function maybe_add_filter_all_option( $type ) {

		if ( ! $type || '' === $type )
			return;

		// Add when setting exists
		$condition_show_all = '' !== $this->get_filter_control_setting( $type, 'all' );

		if ( $condition_show_all ) {

			$label = '' !== trim( $this->get_filter_control_setting( $type, 'all_label' ) ) ? $this->get_filter_control_setting( $type, 'all_label' ) : sprintf( __( 'All %s', 'elementor-extras' ), $this->_filters[ $type ]['label'] );

			$this->_filters[ $type ]['values'][] = [
				'name'  => 'all',
				'title' => $label,
			];
		}
	}

	/**
	 * Get Field Values
	 *
	 * @since  2.1.0
	 * @return array
	 */
	public function get_field_values( $category ) {

		$values = [];

		foreach ( $this->_fields[ $category ]['values'] as $index => $data ) {
			if ( 'all' === $data['name'] ) continue;
			$values[] = $data['name'];
		}

		return $values;
	}

	/**
	 * Get Filter Control ID
	 * 
	 * Shorthand for retrieving a control ID specific to filters
	 *
	 * @since  2.1.0
	 * @return array
	 */
	public function get_filter_control_setting( $type, $name = '' ) {

		$control_id = 'filter_' . $type;

		if ( '' !== $name ) {
			$control_id .= '_' . $name;
		}

		return $this->get_settings( $control_id );
	}

	/**
	 * Set Search Filters
	 * 
	 * Calls methods to set up required filter vars from filter types
	 *
	 * @since  2.1.0
	 * @return array
	 */
	public function set_search_filters() {
		foreach ( $this->_filter_types as $type ) {
			call_user_func( [ $this, 'set_' . $type . '_search_filters' ] );
		}

		$this->_has_inline_filters = ! empty( array_keys( array_filter(
			$this->_fields, function ( $item ) {
				return $item['inline'] === true;
			}
		) ) );

		$this->_has_block_filters = ! empty( array_keys( array_filter(
			$this->_fields, function ( $item ) {
				return $item['inline'] === false;
			}
		) ) );
	}

	/**
	 * Set Post Type Search Filters
	 * 
	 * Processes post type filter settings and sets up required filter vars
	 *
	 * @since  2.1.0
	 * @return array
	 */
	public function set_post_type_search_filters() {
		$settings = $this->get_settings();
		$filter_types = $settings['filter_types'];

		// Post type
		$post_types = Utils::get_public_post_types_options( true, false );

		if ( $post_types && in_array( 'post_type', $filter_types ) ) {
			$this->_filters['post_type'] = [
				'label' => __( 'Post Types', 'elementor-extras' ),
				'inline' => '' !== $settings['filter_post_type_inline'],
			];
			
			$this->maybe_add_filter_all_option( 'post_type' );

			foreach ( $post_types as $post_type => $name ) {

				if ( $settings['filter_post_type_exclude'] && in_array( $post_type , $settings['filter_post_type_exclude'] ) )
					continue;

				$post_type_object = get_post_type_object( $post_type );
				
				$this->_filters['post_type']['values'][] = [
					'name' 	=> $post_type,
					'title' => $post_type_object->labels->singular_name,
				];

				if ( '' === $settings['filter_post_type_fields'] ) {
					$this->_query_filters['post_type'][] = $post_type;
				}
			}

			// Add to list of available fields for user
			if ( $settings['filter_post_type_fields'] ) {
				$this->_fields['post_type'] = $this->_filters['post_type'];
			}
		}
	}

	/**
	 * Set Author Search Filters
	 * 
	 * Processes author filter settings and sets up required filter vars
	 *
	 * @since  2.1.0
	 * @return array
	 */
	public function set_author_search_filters() {
		$settings = $this->get_settings();
		$filter_types = $settings['filter_types'];

		// Authors
		$authors = Utils::get_users_options();

		if ( $authors && in_array( 'author', $filter_types ) ) {
			$this->_filters['author'] = [
				'label' => __( 'Authors', 'elementor-extras' ),
				'inline' => '' !== $settings['filter_author_inline'],
			];

			$this->maybe_add_filter_all_option( 'author' );

			foreach ( $authors as $author => $name ) {

				if ( $settings['filter_author_exclude'] && in_array( $author , $settings['filter_author_exclude'] ) )
					continue;

				$user_info = get_userdata( $author );
				
				$this->_filters['author']['values'][] = [
					'name' 	=> $author,
					'title' => $user_info->display_name,
				];

				if ( '' === $settings['filter_author_fields'] ) {
					$this->_query_filters['author'][] = $author;
				}
			}

			// Add to list of available fields for user
			if ( $settings['filter_author_fields'] )
				$this->_fields['author'] = $this->_filters['author'];
		}
	}

	/**
	 * Set Taxonomies Search Filters
	 * 
	 * Processes taxonomies filter settings and sets up required filter vars
	 *
	 * @since  2.1.0
	 * @return array
	 */
	public function set_taxonomies_search_filters() {
		$settings = $this->get_settings();
		$filter_types = $settings['filter_types'];

		// Taxonomy terms
		$taxonomies = Utils::get_taxonomies_options();

		foreach ( $taxonomies as $name => $label ) {

			$terms 		= Utils::get_terms_options( $name, 'slug', false );
			$prefix 	= 'filter_' . str_replace( '-', '_', $name );
			$exclude 	= $settings[ $prefix . '_exclude' ];

			if ( ! in_array( $name, $filter_types ) ) {

				continue;

			} else {

				if ( $terms ) {

					$this->_query_filters[ $name ] = [];

					$this->_filters[ $name ] = [
						'label' => $label,
						'inline' => '' !== $settings['filter_' . $name . '_inline'],
					];

					$this->maybe_add_filter_all_option( $name );

					if ( ! $exclude && '' === $settings[ $prefix . '_fields' ] ) {
						$this->_query_filters[ $name ][] = 'all';
					}

					foreach ( $terms as $term_name => $term ) {

						if ( $exclude && in_array( $term_name, $exclude ) ) {
							continue;
						}

						$this->_filters[ $name ]['values'][] = [
							'name' 	=> $term_name,
							'title' => $term,
						];

						if ( $exclude && '' === $settings[ $prefix . '_fields' ] ) {
							$this->_query_filters[ $name ][] = $term_name;
						}
					}

					// Add to list of available fields for user
					if ( $settings[ $prefix . '_fields'] )
						$this->_fields[ $name ] = $this->_filters[ $name ];
				}
			}
		}
	}

	/**
	 * Register Skins
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function _register_skins() {
		$this->add_skin( new Skins\Skin_Classic( $this ) );
		$this->add_skin( new Skins\Skin_Expand( $this ) );
		$this->add_skin( new Skins\Skin_Fullscreen( $this ) );
	}

	/**
	 * Register Widget Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function _register_controls() {
		// Content tab
		$this->register_content_controls();
		$this->register_style_controls();
	}

	/**
	 * Register Content Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function register_content_controls() {
		$this->register_settings_content_controls();
		$this->register_filters_content_controls();
		$this->register_button_content_controls();
		$this->register_input_content_controls();
	}

	/**
	 * Register Settings Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function register_settings_content_controls() {
		$this->start_controls_section(
			'section_settings',
			[
				'label' => __( 'Settings', 'elementor-extras' ),
			]
		);

			if ( is_elementor_pro_active() ) {

				$this->add_control(
					'search_id',
					[
						'label' 		=> __( 'Search ID', 'elementor-extras' ),
						'description' 	=> __( 'Enter a unique ID for the search results page.', 'elementor-extras' ),
						'type' 			=> Controls_Manager::TEXT,
					]
				);
			
			}

		$this->end_controls_section();
	}

	/**
	 * Register Filters Settings Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function register_filters_content_controls() {
		$this->start_controls_section(
			'section_filters',
			[
				'label' => __( 'Restrictions & Filters', 'elementor-extras' ),
			]
		);

			$this->add_control(
				'filter_types',
				[	
					'label'			=> __( 'Restrict to', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SELECT2,
					'label_block' 	=> true,
					'default' 		=> [],
					'options'		=> array_merge(
						Utils::get_taxonomies_options(),
						[
							'post_type' => __( 'Post Type', 'elementor-extras' ),
							'author' 	=> __( 'Author', 'elementor-extras' ),
						]
					),
					'multiple' 		=> true,
				]
			);

			$this->add_control(
				'filters_titles',
				[
					'label' 		=> __( 'Show Filter Titles', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'condition'		=> [
						'filter_types!'	=> [],
					],
				]
			);

			$this->add_control(
				'filters_strict',
				[
					'label' 		=> __( 'Strict Filters Mode', 'elementor-extras' ),
					'description'	=> __( 'In strict mode, when selecting \'All\' in filters, the results will not include posts that don\'t have a term belonging to this taxonomy.', 'elementor-extras'),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
				]
			);

			$this->add_control(
				'filter_post_type_heading',
				[
					'label' 	=> __( 'Post Types', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition'		=> [
						'filter_types' => 'post_type',
					],
				]
			);

			$this->add_control(
				'filter_post_type_fields',
				[
					'label' 		=> __( 'Show Filters', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'condition'		=> [
						'filter_types' => 'post_type',
					],
				]
			);

			$this->add_control(
				'filter_post_type_inline',
				[
					'label' 		=> __( 'Show Inline', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'condition'		=> [
						'filter_types' => 'post_type',
						'filter_post_type_fields!' => ''
					],
				]
			);

			$this->add_control(
				'filter_post_type_all',
				[
					'label' 		=> __( 'Show All Option', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'condition' 	=> [
						'filter_types' => 'post_type',
						'filter_post_type_fields!' => '',
					],
				]
			);

			$this->add_control(
				'filter_post_type_checked',
				[
					'label' 		=> __( 'Show Checked', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'condition'		=> [
						'filter_types' => 'post_type',
						'filter_post_type_inline' => '',
						'filter_post_type_fields!' => '',
						'filter_post_type_control' => 'checkbox',
					],
				]
			);

			$this->add_control(
				'filter_post_type_exclude',
				[	
					'label'			=> __( 'Exclude', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SELECT2,
					'label_block' 	=> true,
					'default' 		=> '',
					'options'		=> Utils::get_public_post_types_options( true, false ),
					'multiple' 		=> true,
					'condition'		=> [
						'filter_types' => 'post_type',
					],
				]
			);

			$this->add_control(
				'filter_post_type_control',
				[
					'label' 		=> __( 'Field Type', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'select',
					'options'		=> [
						'checkbox'		=> __( 'Checkboxes', 'elementor-extras' ),
						'radio'		=> __( 'Radio Buttons', 'elementor-extras' ),
						'select'	=> __( 'Dropdown', 'elementor-extras' ),
					],
					'condition'		=> [
						'filter_types' => 'post_type',
						'filter_post_type_inline' => '',
						'filter_post_type_fields!' => '',
					],
				]
			);

			$this->add_control(
				'filter_author_heading',
				[
					'label' 	=> __( 'Authors', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition'		=> [
						'filter_types' => 'author',
					],
				]
			);

			$this->add_control(
				'filter_author_fields',
				[
					'label' 		=> __( 'Show Filters', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'condition'		=> [
						'filter_types' => 'author',
					],
				]
			);

			$this->add_control(
				'filter_author_inline',
				[
					'label' 		=> __( 'Show Inline', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'condition'		=> [
						'filter_types' => 'author',
						'filter_author_fields!' => '',
					],
				]
			);

			$this->add_control(
				'filter_author_all',
				[
					'label' 		=> __( 'Show All Option', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'condition' 	=> [
						'filter_types' => 'author',
						'filter_author_fields!' => '',
					],
				]
			);

			$this->add_control(
				'filter_author_checked',
				[
					'label' 		=> __( 'Show Checked', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'condition' 	=> [
						'filter_types' => 'author',
						'filter_author_inline' => '',
						'filter_author_fields!' => '',
						'filter_author_control' => 'checkbox',
					],
				]
			);

			$this->add_control(
				'filter_author_exclude',
				[	
					'label'			=> __( 'Exclude', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SELECT2,
					'label_block' 	=> true,
					'default' 		=> '',
					'options'		=> Utils::get_users_options(),
					'multiple' 		=> true,
					'condition'		=> [
						'filter_types' => 'author',
					],
				]
			);

			$this->add_control(
				'filter_author_control',
				[
					'label' 		=> __( 'Filter Type', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'select',
					'options'		=> [
						'checkbox'		=> __( 'Checkboxes', 'elementor-extras' ),
						'radio'		=> __( 'Radio Buttons', 'elementor-extras' ),
						'select'	=> __( 'Dropdown', 'elementor-extras' ),
					],
					'condition'		=> [
						'filter_types' => 'author',
						'filter_author_inline' => '',
						'filter_author_fields!' => '',
					],
				]
			);

			$taxonomies = Utils::get_taxonomies_options();

			foreach ( $taxonomies as $name => $label ) {

				$terms 			= Utils::get_terms_options( $name, 'slug', false );
				$labels 		= Utils::get_taxonomy_labels( $name );
				$control_prefix = 'filter_' . str_replace( '-', '_', $name );

				$this->add_control(
					$control_prefix . '_heading',
					[
						'label' 	=> $label,
						'type' 		=> Controls_Manager::HEADING,
						'separator' => 'before',
						'condition'		=> [
							'filter_types' => $name,
						],
					]
				);

				$this->add_control(
					$control_prefix . '_fields',
					[
						'label' 		=> __( 'Show Filters', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SWITCHER,
						'default' 		=> '',
						'condition'		=> [
							'filter_types' => $name,
						],
					]
				);

				$this->add_control(
					$control_prefix . '_inline',
					[
						'label' 		=> __( 'Show Inline', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SWITCHER,
						'default' 		=> '',
						'condition'		=> [
							'filter_types' => $name,
							$control_prefix . '_fields!' => '',
						],
					]
				);

				$this->add_control(
					$control_prefix . '_all',
					[
						'label' 		=> __( 'Show All Option', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SWITCHER,
						'default' 		=> 'yes',
						'condition' => [
							'filter_types' => $name,
							$control_prefix . '_fields!' => '',
						],
					]
				);

				$this->add_control(
					$control_prefix . '_checked',
					[
						'label' 		=> __( 'Show Checked', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SWITCHER,
						'default' 		=> '',
						'condition' 	=> [
							'filter_types' => $name,
							$control_prefix . '_inline' => '',
							$control_prefix . '_fields!' => '',
							$control_prefix . '_control' => 'checkbox',
						],
					]
				);

				$this->add_control(
					$control_prefix . '_exclude',
					[
						'label'			=> __( 'Exclude', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SELECT2,
						'label_block' 	=> true,
						'multiple'		=> true,
						'default'		=> '',
						'options' 		=> $terms,
						'condition'		=> [
							'filter_types' => $name,
						],
					]
				);

				$this->add_control(
					$control_prefix . '_all_label',
					[
						'label' 	=> __( 'All Label', 'elementor-extras' ),
						'type' 		=> Controls_Manager::TEXT,
						'default' 	=> '',
						'condition'		=> [
							'filter_types' => $name,
							$control_prefix . '_fields!' => '',
							$control_prefix . '_all!' => '',
						],
					]
				);

				$this->add_control(
					$control_prefix . '_control',
					[
						'label' 		=> __( 'Filter Type', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SELECT,
						'default' 		=> 'select',
						'options'		=> [
							'checkbox'		=> __( 'Checkboxes', 'elementor-extras' ),
							'radio'		=> __( 'Radio Buttons', 'elementor-extras' ),
							'select'	=> __( 'Dropdown', 'elementor-extras' ),
						],
						'condition' 	=> [
							'filter_types' => $name,
							$control_prefix . '_inline' => '',
							$control_prefix . '_fields!' => '',
						],
					]
				);

			}

		$this->end_controls_section();
	}

	/**
	 * Register Button Content Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function register_button_content_controls() {

		$this->start_controls_section(
			'section_button',
			[
				'label' => __( 'Submit Button', 'elementor-extras' ),
			]
		);

			$this->add_control(
				'heading_icon_content',
				[
					'label' 	=> __( 'Icon', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Register Input Content Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function register_input_content_controls() {

		$this->start_controls_section(
			'section_input',
			[
				'label' => __( 'Keyword Field', 'elementor-extras' ),
			]
		);

			$this->add_control(
				'heading_input_content',
				[
					'label' 	=> __( 'Input', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'input_placeholder',
				[
					'label' 	=> __( 'Placeholder', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'What are you looking for?', 'elementor-extras' ),
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Register Style Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function register_style_controls() {
		$this->register_form_style_controls();
		$this->register_filters_style_controls();
		$this->register_button_style_controls();
		$this->register_input_style_controls();
	}

	/**
	 * Register Form Style Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function register_form_style_controls() {

		$this->start_controls_section(
			'section_form_style',
			[
				'label' => __( 'Form', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'alignment',
				[
					'label' => __( 'Alignment', 'elementor-extras' ),
					'type' => Controls_Manager::CHOOSE,
					'label_block' => false,
					'options' => [
						'left' => [
							'title' => __( 'Left', 'elementor-extras' ),
							'icon' => 'eicon-h-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'elementor-extras' ),
							'icon' => 'eicon-h-align-center',
						],
						'right' => [
							'title' => __( 'Right', 'elementor-extras' ),
							'icon' => 'eicon-h-align-right',
						],
					],
					'selectors' => [
						'{{WRAPPER}} .elementor-widget-container' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'collapse_spacing',
				[
					'label' 		=> __( 'Collapse Spacing', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'prefix_class'	=> 'ee-search-form-spacing--',
					'return_value'	=> 'collapse',
					'separator'		=> 'before',
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'form_border',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'selector' 	=> '{{WRAPPER}} .ee-search-form.ee-search-form-skin--classic .ee-search-form__container,
									{{WRAPPER}} .ee-search-form.ee-search-form-skin--fullscreen .ee-search-form__container,
									{{WRAPPER}} .ee-search-form.ee-search-form-skin--expand .ee-search-form__fields,
									{{WRAPPER}} .ee-search-form__filters .ee-form__field__control--text,
									{{WRAPPER}} .ee-form__field--checkbox label i',
					'condition' => [
						'collapse_spacing!' => '',
					],
				]
			);

			$this->add_control(
				'fields_style_heading',
				[
					'label' 	=> __( 'Fields', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'spacing',
				[
					'label' 		=> __( 'Spacing', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 		=> [
						'size' 		=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 1,
							'max' 	=> 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-form__fields .ee-form__field,
						 {{WRAPPER}} .ee-search-form-skin--classic .ee-search-form__submit' => 'margin-right: {{SIZE}}px;',
						'{{WRAPPER}} .ee-search-form__container' => 'margin-right: -{{SIZE}}px;',
						'{{WRAPPER}}.ee-search-form-input-position--right .ee-form__fields' => 'margin-left: {{SIZE}}px;',

						'(desktop){{WRAPPER}}.ee-search-form-fields-wrap--desktop .ee-form__fields .ee-form__field' => 'margin-bottom: {{SIZE}}px;',
						'(tablet){{WRAPPER}}.ee-search-form-fields-wrap--tablet .ee-form__fields .ee-form__field' => 'margin-bottom: {{SIZE}}px;',
						'(mobile){{WRAPPER}}.ee-search-form-fields-wrap--mobile .ee-form__fields .ee-form__field' => 'margin-bottom: {{SIZE}}px;',
					],
					'condition' => [
						'collapse_spacing!' => 'collapse',
					]
				]
			);

			$this->add_responsive_control(
				'padding', 
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 		=> [
						'size' 		=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 50,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-form__field__control--text:not(.ee-form__field__control--submit),
						 {{WRAPPER}} .ee-search-form-skin--classic .ee-form__field__control--submit' => 'padding: 0 {{SIZE}}px;',
					],
				]
			);

			$this->add_responsive_control(
				'height', 
				[
					'label' 		=> __( 'Height', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 		=> [
						'size' 		=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 1,
							'max' 	=> 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-form__field__control--text' => 'min-height: {{SIZE}}px;',
						'{{WRAPPER}} .ee-search-form .ee-search-form__submit.ee-search-form__control--icon' => 'min-width: {{SIZE}}px',
						'{{WRAPPER}} .ee-search-form.ee-search-form-skin--expand .ee-search-form__submit' => 'min-width: {{SIZE}}px;',
					],
				]
			);

			$this->add_responsive_control(
				'fields_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'description'	=> __( 'For perfectly rounded corners set this to half of the height', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 50,
						],
					],
					'selectors'	=> [
						'{{WRAPPER}} .ee-form__field__control--text' => 'border-radius: {{SIZE}}px;',
						'{{WRAPPER}} .select2-container--open.select2-container--below .ee-form__field__control--select2,
						.ee-select2__dropdown--{{ID}}.select2-dropdown--above' => 'border-radius: {{SIZE}}px {{SIZE}}px 0 0',
						'{{WRAPPER}} .select2-container--open.select2-container--above .ee-form__field__control--select2,
						.ee-select2__dropdown--{{ID}}.select2-dropdown--below' => 'border-radius: 0 0 {{SIZE}}px {{SIZE}}px',
					],
					'condition' => [
						'collapse_spacing' => '',
					],
				]
			);

			$this->add_responsive_control(
				'separator_width',
				[
					'label' 		=> __( 'Separator Width', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 6,
						],
					],
					'selectors'	=> [
						'{{WRAPPER}} .ee-form__fields .ee-form__field:not(:first-child)' => 'border-left-width: {{SIZE}}px;',
						'(desktop){{WRAPPER}}.ee-search-form-fields-wrap--desktop .ee-form__fields .ee-form__field:not(:first-child)' => 'border-top-width: {{SIZE}}px;',
						'(tablet){{WRAPPER}}.ee-search-form-fields-wrap--tablet .ee-form__fields .ee-form__field:not(:first-child)' => 'border-top-width: {{SIZE}}px;',
						'(mobile){{WRAPPER}}.ee-search-form-fields-wrap--mobile .ee-form__fields .ee-form__field:not(:first-child)' => 'border-top-width: {{SIZE}}px;',
					],
					'condition'	=> [
						'collapse_spacing!' => '',
					],
				]
			);

			$this->add_control(
				'separator_color',
				[
					'label' 	=> __( 'Separator Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ee-form__fields .ee-form__field:not(:first-child)' => 'border-color: {{VALUE}};',
					],
					'condition'	=> [
						'collapse_spacing!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'fields_border',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'selector' 	=> '{{WRAPPER}} .ee-form__field__control--text,
									{{WRAPPER}} .ee-form__field--check label i',
					'exclude'	=> ['color'],
					'condition' => [
						'collapse_spacing' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'fields_typography',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-search-form__container .ee-form__field__control,
									{{WRAPPER}} .ee-search-form__filters .ee-form__field__control--text',
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 		=> 'days',
					'selector' 	=> '{{WRAPPER}} .ee-form__field__control',
					'separator'	=> '',
				]
			);

			$this->start_controls_tabs( 'fields_style' );

			$this->start_controls_tab( 'fields_style_default', [ 'label' => __( 'Default', 'elementor-extras' ) ] );

				$this->add_control(
					'fields_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-form__field__control--text' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'fields_background_color',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-form__field__control--text' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'fields_border_color',
					[
						'label' 	=> __( 'Border Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-form__field__control--text,
							 {{WRAPPER}} .ee-form__field--check label i' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'collapse_spacing' => '',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' 		=> 'fields_box_shadow',
						'selector' 	=> '{{WRAPPER}} .ee-form__field__control--text',
						'condition' => [
							'collapse_spacing' => '',
							'_skin!' => 'expand',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'fields_style_focus', [ 'label' => __( 'Focus', 'elementor-extras' ) ] );

				$this->add_control(
					'fields_color_focus',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-form__field__control--text:focus,
							 {{WRAPPER}} .select2-container--open .ee-select2 .select2-selection__rendered,
							 {{WRAPPER}} .select2-container--focus .ee-select2 .select2-selection__rendered' => 'color: {{VALUE}};',
							'{{WRAPPER}} .select2-container--open .select2-selection__arrow b' => 'border-bottom-color: {{VALUE}};',
							'{{WRAPPER}} .select2-container--focus .select2-selection__arrow b,
							 {{WRAPPER}} .ee-form__field--select select:focus + label:after' => 'border-top-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'fields_background_color_focus',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-form__field__control--text:focus,
							 {{WRAPPER}} .select2-container--open .ee-select2,
							 {{WRAPPER}} .select2-container--focus .ee-select2' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'fields_border_color_focus',
					[
						'label' 	=> __( 'Border Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-form__field__control--text:focus,
							 {{WRAPPER}} .select2-container--open .ee-select2,
							 {{WRAPPER}} .select2-container--focus .ee-select2' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'collapse_spacing' => '',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' 		=> 'fields_box_shadow_focus',
						'selector' 	=> '{{WRAPPER}} .ee-form__field__control--text:focus,
										{{WRAPPER}} .select2-container--open .ee-select2,
										{{WRAPPER}} .select2-container--focus .ee-select2',
						'condition' => [
							'collapse_spacing' => '',
							'_skin!' => 'expand',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register Filters Style Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function register_filters_style_controls() {

		$this->start_controls_section(
			'section_filters_style',
			[
				'label' => __( 'Filters', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'filters_custom',
				[
					'label' 		=> __( 'Custom', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'separator' 	=> 'after',
				]
			);

			$this->add_control(
				'filters_layout_heading',
				[
					'label' 	=> __( 'Layout', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
				]
			);

			$this->add_responsive_control(
				'columns',
				[
					'label' 	=> __( 'Columns', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '3',
					'tablet_default' 	=> '2',
					'mobile_default' 	=> '1',
					'options' 			=> [
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					],
					'prefix_class'	=> 'ee-grid-columns%s-',
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				'filters_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-search-form__filters' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'filters_distance', 
				[
					'label' 		=> __( 'Distance', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 		=> [
						'size' 		=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-search-form__filters' => 'margin-top: {{SIZE}}px;',
					],
				]
			);

			$columns_horizontal_margin = is_rtl() ? 'margin-left' : 'margin-right';
			$columns_horizontal_padding = is_rtl() ? 'padding-left' : 'padding-right';

			$this->add_responsive_control(
				'filters_horizontal_spacing', 
				[
					'label' 		=> __( 'Horizontal Spacing', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 		=> [
						'size' 		=> 24,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-search-form__filters' 				=> $columns_horizontal_margin . ': -{{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ee-search-form__filters__category' 	=> $columns_horizontal_padding . ': {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'filters_vertical_spacing', 
				[
					'label' 		=> __( 'Vertical Spacing', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 		=> [
						'size' 		=> 24,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-search-form__filters' => 'margin-bottom: -{{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ee-search-form__filters__category' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'filters_titles_heading',
				[
					'label' 	=> __( 'Titles', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'filters_titles_typography',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-search-form__filters-category__title',
				]
			);

			$this->add_responsive_control(
				'filters_titles_margin',
				[
					'label' 		=> __( 'Margin', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-search-form__filters-category__title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'filters_labels_heading',
				[
					'label' 	=> __( 'Labels', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'filters_labels_typography',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-form__field__label',
				]
			);

			$this->add_control(
				'filters_checkboxes_heading',
				[
					'label' 	=> __( 'Checkboxes', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'filters_checkboxes_size', 
				[
					'label' 		=> __( 'Size', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 		=> [
						'size' 		=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 12,
							'max' 	=> 48,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-form__field--check.ee-custom label i' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
						'{{WRAPPER}} .ee-form__field--check.ee-custom:hover .ee-form__field__control--check + label i,
						 {{WRAPPER}} .ee-form__field--check.ee-custom .ee-form__field__control--check:checked + label i' => 'font-size: calc({{SIZE}}px/2)',
					],
					'condition' => [
						'filters_custom!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'filters_checkboxes_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 6,
						],
					],
					'selectors'	=> [
						'{{WRAPPER}} .ee-search-form__filters .ee-form__field--checkbox label i' => 'border-radius: {{SIZE}}px;',
					],
					'condition' => [
						'filters_custom!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'filters_check_distance', 
				[
					'label' 		=> __( 'Distance', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 		=> [
						'size' 		=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 50,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-form__field--check.ee-custom label i' => 'margin-right: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ee-form__field--check:not(.ee-custom) label'=> 'margin-left: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->start_controls_tabs( 'filters_check_style' );

			$this->start_controls_tab( 'filters_check_style_default', [
				'label' => __( 'Default', 'elementor-extras' ),
				'condition' => [
					'filters_custom!' => '',
				],
			] );

				$this->add_control(
					'filters_check_background_color',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-form__field--check label i' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'filters_custom!' => '',
						],
					]
				);

				$this->add_control(
					'filters_check_border_color',
					[
						'label' 	=> __( 'Border Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-form__field--check label i' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'filters_custom!' => '',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' 		=> 'filters_check_box_shadow',
						'selector' 	=> '{{WRAPPER}} .ee-form__field--check label i',
						'condition' => [
							'filters_custom!' => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'filters_check_style_hover', [
				'label' => __( 'Hover', 'elementor-extras' ),
				'condition' => [
					'filters_custom!' => '',
				],
			] );

				$this->add_control(
					'filters_check_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'global' => [
							'default' => Global_Colors::COLOR_PRIMARY,
						],
						'selectors' => [
							'{{WRAPPER}} .ee-form__field--check.ee-custom:hover .ee-form__field__control--check:not(:checked) + label i:before' => 'color: {{VALUE}};',
						],
						'condition' => [
							'filters_custom!' => '',
						],
					]
				);

				$this->add_control(
					'filters_check_background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-form__field--check.ee-custom:hover label i' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'filters_custom!' => '',
						],
					]
				);

				$this->add_control(
					'filters_check_border_color_hover',
					[
						'label' 	=> __( 'Border Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-form__field--check.ee-custom:hover label i' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'filters_custom!' => ''
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' 		=> 'filters_check_box_shadow_hover',
						'selector' 	=> '{{WRAPPER}} .ee-form__field--check:hover label i',
						'condition' => [
							'filters_custom!' => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'filters_check_style_checked', [
				'label' => __( 'Checked', 'elementor-extras' ),
				'condition' => [
					'filters_custom!' => '',
				],
			] );


				$this->add_control(
					'filters_check_accent_checked',
					[
						'label' 	=> __( 'Accent Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'global' => [
							'default' => Global_Colors::COLOR_PRIMARY,
						],
						'selectors' => [
							'{{WRAPPER}} .ee-form__field--check.ee-custom .ee-form__field__control--checkbox:checked + label i' => 'border-color: {{VALUE}}; background-color: {{VALUE}};',
							'{{WRAPPER}} .ee-form__field--check.ee-custom .ee-form__field__control--radio:checked + label i' => 'border-color: {{VALUE}}; color: {{VALUE}};',
						],
						'condition' => [
							'filters_custom!' => '',
						],
					]
				);

				$this->add_control(
					'filters_check_border_color_checked',
					[
						'label' 	=> __( 'Border Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-form__field__control--check:checked + label i' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'filters_custom!' => '',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' 		=> 'filters_check_box_shadow_checked',
						'selector' 	=> '{{WRAPPER}} .ee-form__field__control--check:checked + label i',
						'condition' => [
							'filters_custom!' => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'filters_dropdown_heading',
				[
					'label' 	=> __( 'Dropdowns', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'filters_dropdown_padding', 
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'allowed_dimensions' => 'horizontal',
					'selectors' => [
						'{{WRAPPER}} .ee-form__field--select .ee-form__field__control--select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'filters_custom' => '',
					],
				]
			);

			$this->add_responsive_control(
				'filters_dropdown_items_padding', 
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' => [
						'.ee-select2__dropdown--{{ID}} .select2-results__option' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'filters_custom!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'label' 	=> __( 'Options Box Shadow', 'elementor-extras' ),
					'name' 		=> 'filters_dropdown_options_box_shadow',
					'selector' 	=> '.ee-select2__dropdown.ee-select2__dropdown--{{ID}}',
					'condition' => [
						'filters_custom!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'filters_dropdown_options_typography',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'separator' => 'before',
					'selector' 	=> '.ee-select2__dropdown.ee-select2__dropdown--{{ID}}',
					'condition' => [
						'filters_custom!' => '',
					],
				]
			);

			$this->start_controls_tabs( 'filters_dropdown_items_style' );

			$this->start_controls_tab( 'filters_dropdown_items_style_default', [
				'label' => __( 'Default', 'elementor-extras' ),
				'condition' => [
					'filters_custom!' => '',
				],
			] );

				$this->add_control(
					'filters_dropdown_items_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'.ee-select2__dropdown--{{ID}} .select2-results__option' => 'color: {{VALUE}};',
						],
						'condition' => [
							'filters_custom!' => '',
						],
					]
				);

				$this->add_control(
					'filters_dropdown_items_background_color',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'.ee-select2__dropdown--{{ID}} .select2-results__option[aria-selected]' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'filters_custom!' => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'filters_dropdown_items_style_hover', [
				'label' => __( 'Hover', 'elementor-extras' ),
				'condition' => [
					'filters_custom!' => '',
				],
			] );

				$this->add_control(
					'filters_dropdown_items_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'.ee-select2__dropdown.ee-select2__dropdown--{{ID}} .select2-results__option.select2-results__option--highlighted[aria-selected]' => 'color: {{VALUE}};',
						],
						'condition' => [
							'filters_custom!' => '',
						],
					]
				);

				$this->add_control(
					'filters_dropdown_items_background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'global' => [
							'default' => Global_Colors::COLOR_PRIMARY,
						],
						'selectors' => [
							'.ee-select2__dropdown.ee-select2__dropdown--{{ID}} .select2-results__option.select2-results__option--highlighted[aria-selected]' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'filters_custom!' => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'filters_dropdown_items_style_selected', [
				'label' => __( 'Selected', 'elementor-extras' ),
				'condition' => [
					'filters_custom!' => '',
				],
			] );

				$this->add_control(
					'filters_dropdown_items_color_highlighted',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'.ee-select2__dropdown.ee-select2__dropdown--{{ID}} .select2-results__option[aria-selected=true]' => 'color: {{VALUE}};',
						],
						'condition' => [
							'filters_custom!' => '',
						],
					]
				);

				$this->add_control(
					'filters_dropdown_items_background_color_selected',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'.ee-select2__dropdown.ee-select2__dropdown--{{ID}} .select2-results__option[aria-selected=true]' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'filters_custom!' => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

	}

	/**
	 * Register Button Style Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function register_button_style_controls() {

		$this->start_controls_section(
			'section_button_style',
			[
				'label' => __( 'Submit Button', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs( 'button_style' );

			$this->start_controls_tab( 'button_style_default', [ 'label' => __( 'Default', 'elementor-extras' ) ] );

				$this->add_control(
					'button_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-search-form__submit' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'button_background_color',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'global' => [
							'default' => Global_Colors::COLOR_PRIMARY,
						],
						'selectors' => [
							'{{WRAPPER}} .ee-search-form__submit' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'button_border_color',
					[
						'label' 	=> __( 'Border Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-search-form__submit' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'collapse_spacing' => '',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' 		=> 'button_box_shadow',
						'selector' 	=> '{{WRAPPER}} .ee-search-form__submit',
						'condition' => [
							'collapse_spacing' => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'button_style_hover', [ 'label' => __( 'Hover', 'elementor-extras' ) ] );

				$this->add_control(
					'button_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-search-form__submit:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'button_background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-search-form__submit:hover' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'button_border_color_hover',
					[
						'label' 	=> __( 'Border Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-search-form__submit:hover' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'collapse_spacing' => '',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' 		=> 'button_box_shadow_hover',
						'selector' 	=> '{{WRAPPER}} .ee-search-form__submit:hover',
						'condition' => [
							'collapse_spacing' => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'button_typography',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'separator' => 'before',
					'selector' 	=> '{{WRAPPER}} .ee-search-form__submit',
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Register Input Style Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function register_input_style_controls() {

		$this->start_controls_section(
			'section_input_style',
			[
				'label' => __( 'Keyword Field', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'input_width',
				[
					'label' 	=> __( 'Width', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-form__field--input' => 'flex-basis: {{SIZE}}%',
					],
				]
			);

			$this->start_controls_tabs( 'input_style' );

			$this->start_controls_tab( 'input_style_default', [ 'label' => __( 'Default', 'elementor-extras' ) ] );

				$this->add_control(
					'input_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-form__field__control.ee-search-form__input' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'placeholder_color',
					[
						'label' 	=> __( 'Placeholder Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => Utils::get_placeholder_selectors( '{{WRAPPER}} .ee-form__field__control.ee-search-form__input', 'color: {{VALUE}};' ),
					]
				);

				$this->add_control(
					'input_background_color',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-form__field__control.ee-search-form__input' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'input_border_color',
					[
						'label' 	=> __( 'Border Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-form__field__control.ee-search-form__input' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'collapse_spacing' => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'input_style_focus', [ 'label' => __( 'Focus', 'elementor-extras' ) ] );

				$this->add_control(
					'input_color_focus',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-form__field__control.ee-search-form__input:focus' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'placeholder_color_focus',
					[
						'label' 	=> __( 'Placeholder Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => Utils::get_placeholder_selectors( '{{WRAPPER}} .ee-form__field__control.ee-search-form__input:focus', 'color: {{VALUE}};' ),
					]
				);

				$this->add_control(
					'input_background_color_focus',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-form__field__control.ee-search-form__input:focus' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'input_border_color_focus',
					[
						'label' 	=> __( 'Border Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-form__field__control.ee-search-form__input:focus' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'collapse_spacing' => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * parse_text_editor wrapper
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function _parse_text_editor( $content ) {
		return $this->parse_text_editor( $content );
	}

	/**
	 * get_repeater_setting_key wrapper
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function _get_repeater_setting_key( $setting_key, $repeater_key, $repeater_item_index ) {
		return $this->get_repeater_setting_key( $setting_key, $repeater_key, $repeater_item_index );
	}

	/**
	 * Render widget content
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render() {

		$settings = $this->get_settings();

		if ( $settings['filter_types'] ) {
			$this->set_search_filters();
		}
	}

	/**
	 * Render Filters
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_filters() {
		$settings = $this->get_settings();

		if ( ! $this->_fields || ! $this->_has_block_filters )
			return;

		$this->add_render_attribute( [
			'filters' => [
				'class' => [
					'ee-search-form__filters',
					'ee-grid',
				],
			],
		] );

		?><div <?php echo $this->get_render_attribute_string( 'filters' ); ?>><?php
			foreach ( $this->_fields as $category => $field ) {

				if ( true === $field['inline'] ) {
					continue;
				} else {
					$this->render_filter( $category, $field );
				}
			}
		?></div><?php
	}

	/**
	 * Render Inline Filters
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_inline_filters() {
		$settings = $this->get_settings();

		if ( ! $this->_fields || ! $this->_has_inline_filters )
			return;

		foreach ( $this->_fields as $category => $field ) {

			if ( false === $field['inline'] ) {
				continue;
			} else {
				$this->render_filter( $category, $field );
			}
		}
	}

	/**
	 * Render Filter
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_filter( $category, $field ) {

		$settings 	= $this->get_settings();
		$is_inline 	= true === $field['inline'];
		$field_key 	= $this->get_repeater_setting_key( 'category', 'filters', $category );

		if ( 'select' === $settings['filter_' . $category . '_control'] || $is_inline ) {
			add_action( 'elementor-extras/search-form/{$category}/options/before', [ $this, 'render_filter_select_start' ], 10, 2 );
			add_action( 'elementor-extras/search-form/{$category}/options/after', [ $this, 'render_filter_select_end' ], 10, 1 );
		}


		if ( ! $is_inline ) {
			$this->add_render_attribute( [
				$field_key => [
					'class' => [
						'ee-grid__item',
						'ee-search-form__filters-category',
						'ee-search-form__filters__category',
						'ee-search-form__filters__category--' . $settings['filter_' . $category . '_control'],
					],
				],
			] );

			?><div <?php echo $this->get_render_attribute_string( $field_key ); ?>><?php
		}

		if ( '' !== $settings['filters_titles'] && ! $is_inline ) {
			$this->render_filter_title( $category, $field );
		}

		$this->render_filter_options( $category, $field );

		if ( ! $is_inline ) {
			?></div><?php
		}

		if ( 'select' === $settings['filter_' . $category . '_control'] || $is_inline ) {
			remove_action( 'elementor-extras/search-form/{$category}/options/before', [ $this, 'render_filter_select_start' ], 10 );
			remove_action( 'elementor-extras/search-form/{$category}/options/after', [ $this, 'render_filter_select_end' ], 10, 1 );
		}
	}

	/**
	 * Render Filter Title
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_filter_title( $category, $field ) {
		$title_key = $this->get_repeater_setting_key( 'category-title', 'filters', $category );

		$this->add_render_attribute( [
			$title_key => [
				'class' => [
					'ee-search-form__filters-category__title',
				],
			],
		] );

		?><div <?php echo $this->get_render_attribute_string( $title_key ); ?>>
			<?php echo $field['label']; ?>
		</div><?php
	}

	/**
	 * Render Filter Options
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_filter_options( $category, $field ) {

		/**
		 * elementor-extras/search-form/{$category}/options/before hook
		 *
		 * @since 2.1.0
		 */
		do_action( 'elementor-extras/search-form/{$category}/options/before', $category, $field );
		
		foreach ( $field['values'] as $values ) { ?>
			<?php $this->render_filter_option( $category, $values ); ?>
		<?php }

		/**
		 * elementor-extras/search-form/{$category}/options/after hook
		 *
		 * @since 2.1.0
		 */
		do_action( 'elementor-extras/search-form/{$category}/options/after', $category );
	}

	/**
	 * Render Single Filter
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_filter_option( $category, $field ) {
		$settings 	= $this->get_settings();
		
		$control  	= ( true === $this->_fields[ $category ]['inline'] ) ? 'select' : $settings['filter_' . $category . '_control'] ;
		$method 	= 'radio' === $control ? 'checkbox' : $control;

		call_user_func( [ $this, 'render_filter_' . $method . '_option' ], $category, $field, $control );
	}

	/**
	 * Render Filter Checkbox Option
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_filter_checkbox_option( $category, $field, $control = 'checkbox' ) {
		$settings = $this->get_settings();

		$option_key 	= $this->get_repeater_setting_key( $this->get_id() . '.' . $field['name'], 'filters', $category );
		$label_key 		= $this->get_repeater_setting_key( $this->get_id() . '.' . $field['name'], 'labels', $category );
		$input_key 		= $this->get_repeater_setting_key( $this->get_id() . '.' . $field['name'], 'inputs', $category );
		$icon_key 		= $this->get_repeater_setting_key( $this->get_id() . '.' . $field['name'], 'icons', $category );

		$this->add_render_attribute( [
			$option_key => [
				'class' => [
					'ee-search-form__filters-category__filter',
					'ee-form__field',
					'ee-form__field--' . $control,
					'ee-form__field--check',
					'ee-search-form__field',
				],
			],
			$label_key => [
				'for' 	=> $input_key,
				'class' => [
					'ee-form__field__label',
				],
			],
			$input_key => [
				'class' 	=> [
					'ee-form__field__control',
					'ee-form__field__control--check',
					'ee-form__field__control--' . $control,
				],
				'id' 		=> $input_key,
				'type' 		=> $control,
				'name' 		=> $category,
			],
		] );

		if ( 'all' === $field['name'] ) {
			$this->add_render_attribute( $input_key, [
				'class' => 'ee-form__field__control--all',
				// 'value' => json_encode( $this->get_field_values( $category ) ),
				'value' => '' !== $settings['filters_strict'] ? 'all' : 'any',
			] );

			if ( 'radio' === $control ) {
				$this->add_render_attribute( $input_key, 'class', 'ee-form__field__control--search' );
			}
		} else {
			$this->add_render_attribute( $input_key, [
				'class' => 'ee-form__field__control--search',
				'value' => $field['name'],
			] );
		}

		if ( 'checkbox' === $control ) {
			if ( 'yes' === $this->get_filter_control_setting( $category, 'checked' ) ) {
				$this->add_render_attribute( $input_key, 'checked', 'checked' );
			}
		} else if ( 'radio' === $control ) {
			if ( 'all' === $field['name'] ) {
				$this->add_render_attribute( $input_key, 'checked', 'checked' );
			}
		}

		if ( '' !== $settings['filters_custom'] ) {
			$this->add_render_attribute( $option_key, 'class', 'ee-custom' );
			$this->add_render_attribute( $icon_key, [
				'class' => 'nicon nicon-' . $control,
				'aria-hidden' => 'true',
			] );
		} ?>

		<div <?php echo $this->get_render_attribute_string( $option_key ); ?>>
			<input <?php echo $this->get_render_attribute_string( $input_key ); ?>>
			<label <?php echo $this->get_render_attribute_string( $label_key ); ?>>
				<?php if ( '' !== $settings['filters_custom'] ) { ?>
					<i <?php echo $this->get_render_attribute_string( $icon_key ); ?>></i>
				<?php } ?>
				<?php echo $field['title']; ?>
			</label>
		</div><?php
	}

	/**
	 * Render Filter Select Option
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_filter_select_option( $category, $field, $control = 'checkbox' ) {
		$settings 	= $this->get_settings();

		$option_key = $this->get_repeater_setting_key( $field['name'], 'filters', $category );

		if ( 'all' === $field['name'] ) {
			$value = '' !== $settings['filters_strict'] ? 'all' : 'any';
		} else {
			$value = $field['name'];
		}

		$this->add_render_attribute( [
			$option_key => [
				'value' => $value,
			]
		] ); ?>

		<option <?php echo $this->get_render_attribute_string( $option_key ); ?>>
			<?php echo $field['title']; ?>
		</option><?php
	}

	/**
	 * Render Filter Select Start 
	 *
	 * Markup for opening select tag for filters
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_filter_select_start( $category, $field ) {
		$settings 		= $this->get_settings();
		$select_key 	= $this->get_repeater_setting_key( 'select', 'filters', $category );
		$option_key 	= $this->get_repeater_setting_key( 'field', 'filters', $category );

		$this->add_render_attribute( [
			$option_key => [
				'class' => [
					'ee-form__field',
					'ee-form__field--select',
					'ee-search-form__field',
				],
			],
			$select_key => [
				'name' 	=> $category,
				'id'	=> 'ee_filter_' . $category . '_' . $this->get_id(),
				'class' => [
					'ee-search-form__filters-category__filter',
					'ee-form__field__control',
					'ee-form__field__control--search',
					'ee-form__field__control--select',
					'ee-form__field__control--text',
				],
			],
		] );

		if ( '' !== $settings['filters_custom'] ) {
			$this->add_render_attribute( $option_key, 'class', 'ee-custom' );
		}

		?><div <?php echo $this->get_render_attribute_string( $option_key ); ?>>
			<select <?php echo $this->get_render_attribute_string( $select_key ); ?>><?php // Wee need this for focus states

	}

	/**
	 * Render Filter Select End 
	 *
	 * Markup for closing select tag for filters
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_filter_select_end( $category ) {
		$select_key 	= $this->get_repeater_setting_key( 'select', 'filters', $category );
		$label_key 		= $this->get_repeater_setting_key( 'label', 'filters', $category );

		$this->add_render_attribute( [
			$label_key => [
				'for' 	=> $select_key,
				'class' => [
					'ee-form__field__label',
				],
			],
		] );

				?></select>
			<label <?php echo $this->get_render_attribute_string( $label_key ); ?>></label>
		</div><?php
	}

	/**
	 * Render Hidden Fields
	 *
	 * Outputs markup with hidden fields for search queries
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_hidden_fields() {
		$settings = $this->get_settings();

		foreach ( $this->_query_filters as $category => $filter ) {
			foreach ( $filter as $value ) {
				$this->render_hidden_field( $category, $value, [
					'ee-form__field__control--search',
				] );
			}
		}

		$this->render_hidden_field( 'ee_search_query', '', 'ee-form__field__control--sent' );

		if ( '' !== $settings['search_id'] ) {
			$this->render_hidden_field( 'ee_search_id', $settings['search_id'], 'ee-form__field__control--sent' );
		}
	}

	/**
	 * Render Hidden Fields
	 *
	 * Outputs markup with hidden fields for search queries
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_hidden_field( $name, $value, $classes = '' ) {
		$hidden_key = $this->get_repeater_setting_key( $name, 'filters', $value );

		$this->add_render_attribute( $hidden_key, [
			'type' 	=> 'hidden',
			'class' => $classes,
			'name' 	=> $name,
			'value' => $value,
		] );

		?><input <?php echo $this->get_render_attribute_string( $hidden_key ); ?> /><?php
	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering. None in this case
	 *
	 * @since  2.1.0
	 * @return void
	 */
	public function _content_template() {}

}