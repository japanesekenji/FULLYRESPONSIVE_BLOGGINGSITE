<?php
namespace ElementorExtras\Modules\Map\Widgets;

// Extras for Elementor Classes
use ElementorExtras\Base\Extras_Widget;
use ElementorExtras\Group_Control_Transition;

// Elementor Classes
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Google_Map
 *
 * @since 2.0.0
 */
class Google_Map extends Extras_Widget {

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_name() {
		return 'ee-google-map';
	}

	/**
	 * Get Title
	 * 
	 * Get the title of the widget
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Google Map', 'elementor-extras' );
	}

	/**
	 * Get Icon
	 * 
	 * Get the icon of the widget
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_icon() {
		return 'nicon nicon-map';
	}

	/**
	 * Get Script Depends
	 * 
	 * A list of scripts that the widgets is depended in
	 *
	 * @since  2.0.0
	 * @return array
	 */
	public function get_script_depends() {
		return [
			'gmap3',
			'google-maps-api',
			'jquery-resize-ee',
		];
	}

	/**
	 * Register Widget Controls
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_pins',
			[
				'label' => __( 'Locations', 'elementor-extras' ),
			]
		);

			$repeater = new Repeater();

			$repeater->start_controls_tabs( 'pins_repeater' );

			$repeater->start_controls_tab( 'pins_pin', [ 'label' => __( 'Pin', 'elementor-extras' ) ] );

				$repeater->add_control(
					'lat',
					[
						'label'		=> __( 'Latitude', 'elementor-extras' ),
						'dynamic'	=> [ 'active' => true ],
						'type' 		=> Controls_Manager::TEXT,
						'default' 	=> '',
					]
				);

				$repeater->add_control(
					'lng',
					[
						'label'		=> __( 'Longitude', 'elementor-extras' ),
						'dynamic'	=> [ 'active' => true ],
						'type' 		=> Controls_Manager::TEXT,
						'default' 	=> '',
					]
				);

				$repeater->add_control(
					'icon',
					[
						'label' 	=> __( 'Icon', 'elementor-extras' ),
						'dynamic'	=> [ 'active' => true ],
						'description' => __( 'IMPORTANT: Your icon image needs to be a square to avoid distortion of the artwork.', 'elementor-extras' ),
						'type' 		=> Controls_Manager::MEDIA,
					]
				);

			$repeater->end_controls_tab();

			$repeater->start_controls_tab( 'pins_info', [ 'label' => __( 'Popup', 'elementor-extras' ) ] );

				$repeater->add_control(
					'name',
					[
						'label'		=> __( 'Title', 'elementor-extras' ),
						'dynamic'	=> [ 'active' => true ],
						'type' 		=> Controls_Manager::TEXT,
						'label_block' => true,
						'default' 	=> __( 'Pin', 'elementor-extras' ),
					]
				);

				$repeater->add_control(
					'description',
					[
						'label'		=> __( 'Description', 'elementor-extras' ),
						'dynamic'	=> [ 'active' => true ],
						'type' 		=> Controls_Manager::WYSIWYG,
					]
				);

				$repeater->add_control(
					'trigger',
					[
						'label'		=> __( 'Trigger', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SELECT,
						'default' 	=> 'click',
						'label_block' => true,
						'options'	=> [
							'click' 	=> __( 'Click', 'elementor-extras' ),
							'auto' 		=> __( 'Auto', 'elementor-extras' ),
							'mouseover' => __( 'Mouse Over', 'elementor-extras' ),
						],
					]
				);

			$repeater->end_controls_tab();

			$repeater->end_controls_tabs();

			$this->add_control(
				'pins',
				[
					'type' 		=> Controls_Manager::REPEATER,
					'default' 	=> [
						[
							'name' => __( 'Tour Eiffel', 'elementor-extras' ),
							'lat' => '48.8583736',
							'lng' => '2.2922873',
						],
						[
							'name' => __( 'Arc de Triomphe', 'elementor-extras' ),
							'lat' => '48.8737952',
							'lng' => '2.2928335',
						],
						[
							'name' => __( 'Louvre Museum', 'elementor-extras' ),
							'lat' => '48.8606146',
							'lng' => '2.33545',
						],
					],
					'fields' 		=> $repeater->get_controls(),
					'title_field' 	=> '{{{ name }}}',
				]
			);
			
		$this->end_controls_section();

		$this->start_controls_section(
			'section_popups',
			[
				'label' => __( 'Popups', 'elementor-extras' ),
			]
		);

			$this->add_control(
				'popups',
				[
					'label' 		=> __( 'Enable Popups', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'title_tag',
				[
					'label' 	=> __( 'Title Tag', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'options' 	=> [
						'h1' 	=> __( 'H1', 'elementor-extras' ),
						'h2' 	=> __( 'H2', 'elementor-extras' ),
						'h3' 	=> __( 'H3', 'elementor-extras' ),
						'h4' 	=> __( 'H4', 'elementor-extras' ),
						'h5' 	=> __( 'H5', 'elementor-extras' ),
						'h6' 	=> __( 'H6', 'elementor-extras' ),
						'div'	=> __( 'div', 'elementor-extras' ),
						'span' 	=> __( 'span', 'elementor-extras' ),
					],
					'default' => 'h5',
					'condition' => [
						'popups' => 'yes',
					],
				]
			);

			$this->add_control(
				'description_tag',
				[
					'label' 	=> __( 'Description Tag', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'p',
					'options' 	=> [
						'p' 	=> __( 'p', 'elementor-extras' ),
						'div'	=> __( 'div', 'elementor-extras' ),
						'span' 	=> __( 'span', 'elementor-extras' ),
					],
					'condition' => [
						'popups' => 'yes',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_map',
			[
				'label' => __( 'Map', 'elementor-extras' ),
			]
		);

			$this->add_control(
				'heading_center',
				[
					'type'		=> Controls_Manager::HEADING,
					'label' 	=> __( 'Center Map', 'elementor-extras' ),
					'condition'	=> [
						'route'	=> '',
					],
				]
			);

			$this->add_control(
				'fit',
				[
					'label' 		=> __( 'Fit to Locations', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
					'condition'		=> [
						'route'		=> '',
					],
				]
			);

			$this->add_control(
				'lat',
				[
					'label'		=> __( 'Latitude', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'dynamic'	=> [ 'active' => true ],
					'default' 	=> '48.8583736',
					'condition'	=> [
						'fit' 	=> '',
						'route'	=> '',
					],
				]
			);

			$this->add_control(
				'lng',
				[
					'label'		=> __( 'Longitude', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'dynamic'	=> [ 'active' => true ],
					'default' 	=> '2.2922873',
					'condition'	=> [
						'fit' 	=> '',
						'route'	=> '',
					],
				]
			);

			$this->add_control(
				'zoom',
				[
					'label' 		=> __( 'Zoom', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 10,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 18,
							'step'	=> 1,
						],
					],
					'condition' => [
						'fit' 	=> '',
						'route'	=> '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'heading_settings',
				[
					'type'		=> Controls_Manager::HEADING,
					'label' 	=> __( 'Settings', 'elementor-extras' ),
					'separator' => 'before',
				]
			);

			$this->add_control(
				'map_type',
				[
					'label'		=> __( 'Map Type', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'ROADMAP',
					'options'	=> [
						'ROADMAP' 	=> __( 'Roadmap', 'elementor-extras' ),
						'SATELLITE' => __( 'Satellite', 'elementor-extras' ),
						'TERRAIN' 	=> __( 'Terrain', 'elementor-extras' ),
						'HYBRID' 	=> __( 'Hybrid', 'elementor-extras' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'scrollwheel',
				[
					'label' 		=> __( 'Scrollwheel', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'clickable_icons',
				[
					'label' 		=> __( 'Clickable Icons', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'doubleclick_zoom',
				[
					'label' 		=> __( 'Double Click to Zoom', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'draggable',
				[
					'label' 		=> __( 'Draggable', 'elementor-extras' ),
					'description'	=> __( 'Note: Map is not draggable in edit mode.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'keyboard_shortcuts',
				[
					'label' 		=> __( 'Keyboard Shortcuts', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'heading_controls',
				[
					'type'		=> Controls_Manager::HEADING,
					'label' 	=> __( 'Interface', 'elementor-extras' ),
					'separator' => 'before',
				]
			);

			$this->add_control(
				'fullscreen_control',
				[
					'label' 		=> __( 'Fullscreen Control', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'map_type_control',
				[
					'label' 		=> __( 'Map Type Control', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'rotate_control',
				[
					'label' 		=> __( 'Rotate Control', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'scale_control',
				[
					'label' 		=> __( 'Scale Control', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'streetview_control',
				[
					'label' 		=> __( 'Street View Control', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'zoom_control',
				[
					'label' 		=> __( 'Zoom Control', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_polygon',
			[
				'label' => __( 'Polygon', 'elementor-extras' ),
			]
		);

			$this->add_control(
				'polygon',
				[
					'label' 		=> __( 'Enable', 'elementor-extras' ),
					'description' 	=> __( 'Draws a polygon on the map by connecting the locations.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_route',
			[
				'label' => __( 'Route', 'elementor-extras' ),
			]
		);

			$this->add_control(
				'route',
				[
					'label' 		=> __( 'Enable', 'elementor-extras' ),
					'description' 	=> __( 'Draws a route on the map between the locations.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'route_mode',
				[
					'label' 	=> __( 'Mode', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'DRIVING',
					'options' 	=> [
						'DRIVING' 	=> __( 'Driving', 'elementor-extras' ),
						'WALKING' 	=> __( 'Walking', 'elementor-extras' ),
						'BICYCLING' => __( 'Bicycling', 'elementor-extras' ),
						'TRANSIT' 	=> __( 'Transit', 'elementor-extras' ),
					],
					'condition' 	=> [
						'route!' => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'route_markers',
				[
					'label' 		=> __( 'Markers', 'elementor-extras' ),
					'description' 	=> __( 'Enables direction markers to be shown on your route.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'condition' 	=> [
						'route!' => '',
					],
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_navigation',
			[
				'label' => __( 'Navigation', 'elementor-extras' ),
			]
		);

			$this->add_responsive_control(
				'navigation',
				[
					'label' 		=> __( 'Enable', 'elementor-extras' ),
					'description' 	=> __( 'Adds a list which visitors can use to navigate through your locations.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'navigation_zoom',
				[
					'label' 	=> __( 'Zoom Level', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 18,
					],
					'range' 	=> [
						'px' 	=> [
							'max' => 18,
						],
					],
					'condition' => [
						'navigation!' => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'navigation_hide_on',
				[
					'label' 	=> __( 'Hide On', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'mobile',
					'options' 	=> [
						'' 			=> __( 'None', 'elementor-extras' ),
						'tablet' 	=> __( 'Mobile & Tablet', 'elementor-extras' ),
						'mobile' 	=> __( 'Mobile Only', 'elementor-extras' ),
					],
					'condition' => [
						'navigation!' => '',
					],
					'prefix_class' => 'ee-google-map-navigation--hide-',
				]
			);

			$this->add_control(
				'all_text',
				[
					'label'		=> __( 'All label', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'All locations', 'elementor-extras' ),
					'frontend_available' => true,
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_control(
				'selected_navigation_icon',
				[
					'label' 			=> __( 'Icon', 'elementor-extras' ),
					'type' 				=> Controls_Manager::ICONS,
					'fa4compatibility' 	=> 'navigation_icon',
					'default' 			=> [
						'value' 		=> 'fas fa-map-marker-alt',
						'library' 		=> 'fa-solid',
					],
					'label_block'		=> false,
					'skin' 				=> 'inline',
					'condition' 		=> [
						'navigation!' 	=> '',
					],
				]
			);

			$this->add_control(
				'navigation_icon_align',
				[
					'label' => __( 'Icon Position', 'elementor-extras' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'left',
					'options' => [
						'left' => __( 'Before', 'elementor-extras' ),
						'right' => __( 'After', 'elementor-extras' ),
					],
					'condition' => [
						'navigation!' => '',
						'selected_navigation_icon[value]!' => '',
					],
				]
			);

			$this->add_control(
				'navigation_icon_indent',
				[
					'label' => __( 'Icon Spacing', 'elementor-extras' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'max' => 50,
						],
					],
					'condition' => [
						'navigation!' => '',
						'selected_navigation_icon[value]!' => '',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-icon--right' => 'margin-left: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ee-icon--left' => 'margin-right: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_pins',
			[
				'label' => __( 'Pins', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'pin_size',
				[
					'label' 		=> __( 'Size', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'description' 	=> __( 'Note: This setting only applies to custom pins.', 'elementor-extras' ),
					'default' 	=> [
						'size' 	=> 50,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 100,
							'step'	=> 1,
						],
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'pin_position_horizontal',
				[
					'label' 		=> __( 'Horizontal Position', 'elementor-extras' ),
					'description' 	=> __( 'Note: This setting only applies to custom pins.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'center',
					'options' 		=> [
						'left'    		=> [
							'title' 	=> __( 'Left', 'elementor-extras' ),
							'icon' 		=> 'eicon-h-align-left',
						],
						'center' 		=> [
							'title' 	=> __( 'Center', 'elementor-extras' ),
							'icon' 		=> 'eicon-h-align-center',
						],
						'right' 		=> [
							'title' 	=> __( 'Right', 'elementor-extras' ),
							'icon' 		=> 'eicon-h-align-right',
						],
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'pin_position_vertical',
				[
					'label' 		=> __( 'Vertical Position', 'elementor-extras' ),
					'description' 	=> __( 'Note: This setting only applies to custom pins.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'top',
					'options' 		=> [
						'top'    		=> [
							'title' 	=> __( 'Top', 'elementor-extras' ),
							'icon' 		=> 'eicon-v-align-top',
						],
						'middle'    		=> [
							'title' 	=> __( 'Middle', 'elementor-extras' ),
							'icon' 		=> 'eicon-v-align-middle',
						],
						'bottom' 		=> [
							'title' 	=> __( 'Bottom', 'elementor-extras' ),
							'icon' 		=> 'eicon-v-align-bottom',
						],
					],
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_map',
			[
				'label' => __( 'Map', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'map_style_type',
				[
					'label' => __( 'Add style from', 'elementor-extras' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'api',
					'options' => [
						'api' 	=> __( 'Snazzy Maps API', 'elementor-extras' ),
						'json' 	=> __( 'Custom JSON', 'elementor-extras' ),
					],
					'label_block' => true,
					'frontend_available' => true,
				]
			);

			$sm_endpoint_option = \ElementorExtras\ElementorExtrasPlugin::$instance->settings->get_option( 'snazzy_maps_endpoint', 'elementor_extras_apis', false );

			$this->add_control(
				'map_style_api',
				[
					'label' 				=> __( 'Search Snazzy Maps', 'elementor-extras' ),
					'type' 					=> 'ee-snazzy',
					'placeholder'			=> __( 'Search styles', 'elementor-extras' ),
					'snazzy_options'		=> [
						'endpoint'			=> $sm_endpoint_option ? $sm_endpoint_option : 'explore',
					],
					'default'				=> '',
					'frontend_available' 	=> true,
					'condition'				=> [
						'map_style_type'	=> 'api',
					],
				]
			);

			$this->add_control(
				'map_style_json',
				[
					'label'					=> __( 'Custom JSON', 'elementor-extras' ),
					'description' 			=> sprintf( __( 'Paste the JSON code for styling the map. You can get it from %1$sSnazzyMaps%2$s or similar services. Note: If you enter an invalid JSON string you\'ll be alerted.', 'elementor-extras' ), '<a target="_blank" href="https://snazzymaps.com/explore">', '</a>' ),
					'type' 					=> Controls_Manager::TEXTAREA,
					'default' 				=> '',
					'frontend_available' 	=> true,
					'condition'				=> [
						'map_style_type'	=> 'json',
					],
				]
			);

			$this->add_responsive_control(
				'map_height',
				[
					'label' 		=> __( 'Height', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px', 'vh', '%' ],
					'default' 	=> [
						'size' 	=> 400,
					],
					'range' 	=> [
						'vh' 		=> [
							'min' => 0,
							'max' => 100,
						],
						'%' 	=> [
							'min' 	=> 10,
							'max' 	=> 100,
							'step'	=> 1,
						],
						'px' 	=> [
							'min' 	=> 100,
							'max' 	=> 1000,
							'step'	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-google-map' => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_polygon',
			[
				'label' => __( 'Polygon', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'polygon!' => '',
				],
			]
		);

			$this->start_controls_tabs( 'polygon_tabs' );

			$this->start_controls_tab( 'polygon_default', [ 'label' => __( 'Default', 'elementor-extras' ) ] );

				$this->add_control(
					'heading_polygon_stroke',
					[
						'type'		=> Controls_Manager::HEADING,
						'label' 	=> __( 'Stroke', 'elementor-extras' ),
						'condition' => [
							'polygon!' => '',
						],
					]
				);

				$this->add_control(
					'polygon_stroke_weight',
					[
						'label' 		=> __( 'Weight', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SLIDER,
						'default' 	=> [
							'size' 	=> 2,
						],
						'range' 	=> [
							'px' 	=> [
								'min' 	=> 0,
								'max' 	=> 10,
								'step'	=> 1,
							],
						],
						'condition' => [
							'polygon!' => '',
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'polygon_stroke_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'polygon!' => '',
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'polygon_stroke_opacity',
					[
						'label' 		=> __( 'Opacity', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SLIDER,
						'default' 	=> [
							'size' 	=> 0.8,
						],
						'range' 	=> [
							'px' 	=> [
								'min' 	=> 0,
								'max' 	=> 1,
								'step'	=> 0.01,
							],
						],
						'condition' => [
							'polygon!' => '',
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'heading_polygon_fill',
					[
						'type'		=> Controls_Manager::HEADING,
						'label' 	=> __( 'Fill', 'elementor-extras' ),
						'separator' => 'before',
						'condition' => [
							'polygon!' => '',
						],
					]
				);

				$this->add_control(
					'polygon_fill_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'polygon!' => '',
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'polygon_fill_opacity',
					[
						'label' 		=> __( 'Opacity', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SLIDER,
						'default' 	=> [
							'size' 	=> 0.35,
						],
						'range' 	=> [
							'px' 	=> [
								'min' 	=> 0,
								'max' 	=> 1,
								'step'	=> 0.01,
							],
						],
						'condition' => [
							'polygon!' => '',
						],
						'frontend_available' => true,
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'polygon_hover', [ 'label' => __( 'Hover', 'elementor-extras' ) ] );

				$this->add_control(
					'heading_polygon_stroke_hover',
					[
						'type'		=> Controls_Manager::HEADING,
						'label' 	=> __( 'Stroke', 'elementor-extras' ),
						'condition' => [
							'polygon!' => '',
						],
					]
				);

				$this->add_control(
					'polygon_stroke_weight_hover',
					[
						'label' 		=> __( 'Weight', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SLIDER,
						'default' 	=> [
							'size' 	=> 2,
						],
						'range' 	=> [
							'px' 	=> [
								'min' 	=> 0,
								'max' 	=> 10,
								'step'	=> 1,
							],
						],
						'condition' => [
							'polygon!' => '',
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'polygon_stroke_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'polygon!' => '',
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'polygon_stroke_opacity_hover',
					[
						'label' 		=> __( 'Opacity', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SLIDER,
						'default' 	=> [
							'size' 	=> 0.8,
						],
						'range' 	=> [
							'px' 	=> [
								'min' 	=> 0,
								'max' 	=> 1,
								'step'	=> 0.01,
							],
						],
						'condition' => [
							'polygon!' => '',
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'heading_polygon_fill_hover',
					[
						'type'		=> Controls_Manager::HEADING,
						'label' 	=> __( 'Fill', 'elementor-extras' ),
						'separator' => 'before',
						'condition' => [
							'polygon!' => '',
						],
					]
				);

				$this->add_control(
					'polygon_fill_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'polygon!' => '',
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'polygon_fill_opacity_hover',
					[
						'label' 		=> __( 'Opacity', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SLIDER,
						'default' 	=> [
							'size' 	=> 0.35,
						],
						'range' 	=> [
							'px' 	=> [
								'min' 	=> 0,
								'max' 	=> 1,
								'step'	=> 0.01,
							],
						],
						'condition' => [
							'polygon!' => '',
						],
						'frontend_available' => true,
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_navigation',
			[
				'label' => __( 'Navigation', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'navigation!' => '',
				],
			]
		);

			$this->add_responsive_control(
				'navigation_position',
				[
					'label'		=> __( 'Position', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'top-left',
					'options'	=> [
						'top-left' 		=> __( 'Top Left', 'elementor-extras' ),
						'top-right' 	=> __( 'Top Right', 'elementor-extras' ),
						'bottom-right' 	=> __( 'Bottom Right', 'elementor-extras' ),
						'bottom-left' 	=> __( 'Bottom Left', 'elementor-extras' ),
					],
					'frontend_available' => true,
					'prefix_class' => 'ee-google-map-navigation%s--',
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'navigation_width',
				[
					'label' 		=> __( 'Width', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px', '%' ],
					'range' 		=> [
						'%' 		=> [
							'min' => 0,
							'max' => 100,
						],
						'px' 		=> [
							'min' => 100,
							'max' => 1000,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-google-map__navigation' => 'width: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'navigation_margin',
				[
					'label' 		=> __( 'Margin', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-google-map__navigation' => 'margin: {{SIZE}}{{UNIT}}; max-height: calc( 100% - {{SIZE}}px * 2 );',
					],
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_control(
				'navigation_background',
				[
					'label' 	=> __( 'Background', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'global' => [
						'default' => Global_Colors::COLOR_PRIMARY,
					],
					'default'	=> '',
					'selectors' => [
						'{{WRAPPER}} .ee-google-map__navigation' => 'background-color: {{VALUE}};',
					],
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'navigation_border',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'selector' 	=> '{{WRAPPER}} .ee-google-map__navigation',
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_control(
				'navigation_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'selectors' 	=> [
						'{{WRAPPER}} .ee-google-map__navigation' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .ee-google-map__navigation__item:first-child a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;',
						'{{WRAPPER}} .ee-google-map__navigation__item:last-child a' => 'border-radius: 0 0 {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'navigation_box_shadow',
					'selector' 	=> '{{WRAPPER}} .ee-google-map__navigation',
					'separator'	=> '',
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_control(
				'heading_navigation_separator',
				[
					'type'		=> Controls_Manager::HEADING,
					'label' 	=> __( 'Separator', 'elementor-extras' ),
					'separator' => 'before',
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'navigation_links_separator_thickness',
				[
					'label' 		=> __( 'Thickness', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 50,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-google-map__navigation__item:not(:last-child) a' => 'border-bottom: {{SIZE}}px solid;',
					],
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_control(
				'heading_navigation_links',
				[
					'type'		=> Controls_Manager::HEADING,
					'label' 	=> __( 'Links', 'elementor-extras' ),
					'separator' => 'before',
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'navigation_links_spacing',
				[
					'label' 		=> __( 'Spacing', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default'		=> [
						'size'		=> 0,
					],
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 50,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-google-map__navigation__item:not(:last-child)' => 'margin-bottom: {{SIZE}}px;',
					],
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_control(
				'navigation_links_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'selectors' 	=> [
						'{{WRAPPER}} .ee-google-map__navigation__link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'navigation_links_typography',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-google-map__navigation',
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_control(
				'navigation_links_text_align',
				[
					'label' 		=> __( 'Align Text', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'left',
					'options' 		=> [
						'left'    		=> [
							'title' 	=> __( 'Left', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-left',
						],
						'center' 		=> [
							'title' 	=> __( 'Center', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-center',
						],
						'right' 		=> [
							'title' 	=> __( 'Right', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-right',
						],
					],
					'condition' => [
						'navigation!' => '',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-google-map__navigation__link' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 		=> 'image',
					'selector' 	=> '{{WRAPPER}} .ee-google-map__navigation__link',
					'separator'	=> '',
				]
			);

			$this->start_controls_tabs( 'navigation_tabs' );

			$this->start_controls_tab( 'navigation_default', [ 'label' => __( 'Default', 'elementor-extras' ) ] );

				$this->add_control(
					'navigation_links_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'navigation!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .ee-google-map__navigation__link' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'navigation_links_separator_color',
					[
						'label' 	=> __( 'Separator Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'navigation!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .ee-google-map__navigation__item:not(:last-child) a' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'navigation_links_background',
					[
						'label' 	=> __( 'Background', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-google-map__navigation__link' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'navigation!' => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'navigation_hover', [ 'label' => __( 'Hover', 'elementor-extras' ) ] );

				$this->add_control(
					'navigation_links_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'navigation!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .ee-google-map__navigation__link:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'navigation_links_separator_color_hover',
					[
						'label' 	=> __( 'Separator Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'navigation!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .ee-google-map__navigation__item:not(:last-child) .ee-google-map__navigation__link:hover' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'navigation_links_background_hover',
					[
						'label' 	=> __( 'Background', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-google-map__navigation__link:hover' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'navigation!' => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'navigation_current', [ 'label' => __( 'Current', 'elementor-extras' ) ] );

				$this->add_control(
					'navigation_links_color_current',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'navigation!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .ee-google-map__navigation__item.ee--is-active .ee-google-map__navigation__link,
							 {{WRAPPER}} .ee-google-map__navigation__item.ee--is-active .ee-google-map__navigation__link:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'navigation_links_separator_color_current',
					[
						'label' 	=> __( 'Separator Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'navigation!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .ee-google-map__navigation__item.ee--is-active .ee-google-map__navigation__item:not(:last-child) .ee-google-map__navigation__link,
							 {{WRAPPER}} .ee-google-map__navigation__item.ee--is-active .ee-google-map__navigation__item:not(:last-child) .ee-google-map__navigation__link:hover' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'navigation_links_background_current',
					[
						'label' 	=> __( 'Background', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-google-map__navigation__item.ee--is-active .ee-google-map__navigation__link,
							 {{WRAPPER}} .ee-google-map__navigation__item.ee--is-active .ee-google-map__navigation__link:hover' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'navigation!' => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render
	 * 
	 * Render widget contents on frontend
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$plugin = \ElementorExtras\ElementorExtrasPlugin::$instance;

		if ( '' === $plugin->settings->get_option( 'google_maps_api_key', 'elementor_extras_apis', false ) ) {
			echo $this->render_placeholder( [
				'body' => __( 'You have not set your Google Maps API key.', 'elementor-extras' ),
			] );

			return;
		}

		$this->add_render_attribute( [
			'wrapper' => [
				'class' => [
					'ee-google-map-wrapper',
				],
			],
			'map' => [
				'class' => [
					'ee-google-map',
				],
				'data-lat' => $settings['lat'],
				'data-lng' => $settings['lng'],
			],
			'title' => [
				'class' => 'ee-google-map__pin__title',
			],
			'description' => [
				'class' => 'ee-google-map__pin__description',
			],
		] );

		if ( ! empty( $settings['pins'] ) ) {

			?><div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>><?php

				if ( '' !== $settings['navigation'] ) {
					$this->render_navigation();
				}
				
				?><div <?php echo $this->get_render_attribute_string( 'map' ); ?>>
						
					<?php foreach ( $settings['pins'] as $index => $item ) {

						$key = $this->get_repeater_setting_key( 'pin', 'pins', $index );
						$title_key = $this->get_repeater_setting_key( 'title', 'pins', $index );
						$description_key = $this->get_repeater_setting_key( 'description', 'pins', $index );

						$this->add_render_attribute( [
							$key => [
								'class' => [
									'ee-google-map__pin',
								],
								'data-trigger' 	=> $item['trigger'],
								'data-lat' 		=> $item['lat'],
								'data-lng' 		=> $item['lng'],
								'data-id' 		=> $item['_id'],
							],
						] );

						if ( ! empty( $item['icon']['url'] ) ) {
							$this->add_render_attribute( $key, [
								'data-icon' => esc_url( $item['icon']['url'] ),
							] );
						}

						?><div <?php echo $this->get_render_attribute_string( $key ); ?>>
							<?php if ( '' !== $settings['popups'] ) {

								$title_tag = $settings['title_tag'];
								$description_tag = $settings['description_tag'];
								
								?><<?php echo $title_tag; ?> <?php echo $this->get_render_attribute_string( 'title' ); ?>>
									<?php echo $item['name']; ?>
								</<?php echo $title_tag; ?>>
								<<?php echo $description_tag; ?> <?php echo $this->get_render_attribute_string( 'description' ); ?>>
									<?php echo $item['description']; ?>
								</<?php echo $description_tag; ?>>

							<?php } ?>
						</div><?php 
					}

				?></div><?php

			?></div><?php

		}
	}

	/**
	 * Render Navigation
	 * 
	 * Render widget navigation on frontend
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function render_navigation() {

		$settings = $this->get_settings_for_display();
		$has_icon = false;

		$this->add_render_attribute( [
			'navigation-wrapper' => [
				'class' => [
					'ee-google-map__navigation',
				],
			],
			'navigation' => [
				'class' => [
					'ee-nav',
					'ee-nav--stacked',
					'ee-google-map__navigation__items',
				],
			],
			'text' => [
				'class' => [
					'ee-google-map__navigation__text'
				],
			],
		] );

		if ( ! empty( $settings['navigation_icon'] ) || ! empty( $settings['selected_navigation_icon']['value'] ) ) {
			$this->add_render_attribute( 'icon', 'class', [
				'ee-button-icon',
				'ee-icon',
				'ee-icon-support--svg',
				'ee-icon--' . $settings['navigation_icon_align'],
			] );

			$has_icon = true;
		}

		?><div <?php echo $this->get_render_attribute_string( 'navigation-wrapper' ); ?>>
			<ul <?php echo $this->get_render_attribute_string( 'navigation' ); ?>><?php

				$this->render_all_link( $has_icon );

				foreach ( $settings['pins'] as $index => $item ) {

					$item_key = $this->get_repeater_setting_key( 'item', 'pins', $index );
					$link_key = $this->get_repeater_setting_key( 'link', 'pins', $index );

					$this->add_render_attribute( [
						$item_key => [
							'class' => [
								'ee-google-map__navigation__item',
								'elementor-repeater-item-' . $item['_id'],
							],
							'data-id' => $item['_id'],
						],
						$link_key => [
							'class' => [
								'ee-google-map__navigation__link',
								'ee-button',
								'ee-button-link',
							],
						],
					] );

					?><li <?php echo $this->get_render_attribute_string( $item_key ); ?>>
						<a <?php echo $this->get_render_attribute_string( $link_key ); ?>><?php

							if ( $has_icon ) {
								$this->render_navigation_icon();
							}

							?><span <?php echo $this->get_render_attribute_string( 'text' ); ?>>
								<?php echo $item['name']; ?>
							</span>
						</a>
					</li><?php 
				} ?>

			</ul>
		</div><?php
	}

	/**
	 * Render Navigation Icon
	 *
	 * @since  2.1.5
	 * @return void
	 */
	protected function render_navigation_icon() {
		$settings = $this->get_settings();

		$migrated = isset( $settings['__fa4_migrated']['selected_navigation_icon'] );
		$is_new = empty( $settings['navigation_icon'] ) && Icons_Manager::is_migration_allowed();
		
		?><span <?php echo $this->get_render_attribute_string( 'icon' ); ?>><?php
			if ( $is_new || $migrated ) {
				Icons_Manager::render_icon( $settings['selected_navigation_icon'], [ 'aria-hidden' => 'true' ] );
			} else {
				?><i class="<?php echo esc_attr( $settings['navigation_icon'] ); ?>" aria-hidden="true"></i><?php
			}
		?></span><?php
	}

	/**
	 * Render All Link
	 * 
	 * Render widget navigations' "all" link
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function render_all_link( $icon = false ) {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( [
				'all' => [
					'class' => [
						'ee-google-map__navigation__item',
						'ee-google-map__navigation__item--all',
					],
				],
				'link' => [
					'class' => [
						'ee-google-map__navigation__link',
						'ee-button',
						'ee-button-link',
					],
				],
			] );

			?><li <?php echo $this->get_render_attribute_string( 'all' ); ?>>
				<a <?php echo $this->get_render_attribute_string( 'link' ); ?>><?php

					if ( $icon ) {
						$this->render_navigation_icon();
					}
					
					?><span <?php echo $this->get_render_attribute_string( 'text' ); ?>>
						<?php echo $settings['all_text']; ?>
					</span>
				</a>
			</li><?php
	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering. None in this case
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function _content_template() {}
}
