<?php
namespace ElementorExtras\Modules\Navigation\Widgets;

// Extras for Elementor Classes
use ElementorExtras\Base\Extras_Widget;
use ElementorExtras\Modules\Navigation\Skins;
use ElementorExtras\Modules\Navigation\Module as Module;
use ElementorExtras\Modules\TemplatesControl\Module as TemplatesControl;
use ElementorExtras\Group_Control_Transition;

// Elementor Classes
use Elementor\Utils;
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
 * Offcanvas
 *
 * @since 2.0.0
 */
class Offcanvas extends Extras_Widget {

	/**
	 * Has template content
	 *
	 * @since  2.0.0
	 * @var    bool
	 */
	protected $_has_template_content = false;

	/**
	 * Sidebar Options
	 *
	 * @since  2.0.0
	 * @var    array
	 */
	protected $_sidebars_options = [];

	/**
	 * Sidebars Default Key
	 *
	 * @since  2.0.0
	 * @var    array
	 */
	protected $_sidebars_default_key;

	/**
	 * Set Sidebar Vars
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function set_sidebars_vars() {
		global $wp_registered_sidebars;

		if ( ! $wp_registered_sidebars ) {
			$this->_sidebars_options[''] = __( 'No sidebars were found', 'elementor-extras' );
		} else {
			$this->_sidebars_options[''] = __( 'Choose Sidebar', 'elementor-extras' );

			foreach ( $wp_registered_sidebars as $sidebar_id => $sidebar ) {
				$this->_sidebars_options[ $sidebar_id ] = $sidebar['name'];
			}
		}

		$this->_sidebars_default_key = array_keys( $this->_sidebars_options );
		$this->_sidebars_default_key = array_shift( $this->_sidebars_default_key );
	}

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_name() {
		return 'ee-offcanvas';
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
		return __( 'Offcanvas', 'elementor-extras' );
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
		return 'nicon nicon-offcanvas';
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
			'slidebars',
			'jquery-resize-ee',
		];
	}

	/**
	 * Whether the reload preview is required or not.
	 *
	 * Used to determine whether the reload preview is required.
	 *
	 * @since  2.0.0
	 * @return bool
	 */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Register Skins
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function _register_skins() {
		$this->add_skin( new Skins\Skin_Classic( $this ) );
	}

	/**
	 * Register Widget Controls
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function _register_controls() {

		// Content tab
		$this->register_settings_controls();
		$this->register_trigger_content_controls();
		$this->register_title_content_controls();
		$this->register_close_content_controls();
		$this->register_content_boxes_content_controls();

		// Style tab
		$this->register_offcanvas_style_controls();
		$this->register_page_style_controls();
		$this->register_trigger_style_controls();
		$this->register_title_style_controls();
		$this->register_close_style_controls();
		$this->register_content_boxes_style_controls();
	}

	/**
	 * Register Settings Controls
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function register_settings_controls() {
		$this->start_controls_section(
			'section_settings',
			[
				'label' => __( 'Settings', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control(
				'open',
				[
					'label' 		=> __( 'Toggle Offcanvas', 'elementor-extras' ),
					'type' 			=> \Elementor\Controls_Manager::BUTTON,
					'button_type' 	=> 'default',
					'text' 			=> __( 'Toggle', 'elementor-extras' ),
					'event' 		=> 'ee:editor:offcanvas:open',
				]
			);

			$this->add_control(
				'container_scroll',
				[
					'label' 		=> __( 'Allow Page Scroll', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value'	=> 'yes',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'position',
				[
					'label' 	=> __( 'Position', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default'	=> 'left',
					'options' 	=> [
						'left' 		=> __( 'Left', 'elementor-extras' ),
						'right' 	=> __( 'Right', 'elementor-extras' ),
						'top' 		=> __( 'Top', 'elementor-extras' ),
						'bottom' 	=> __( 'Bottom', 'elementor-extras' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'animation',
				[
					'label' 	=> __( 'Animation', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default'	=> 'reveal',
					'options' 	=> [
						'reveal' 	=> __( 'Reveal', 'elementor-extras' ),
						'push' 		=> __( 'Push', 'elementor-extras' ),
						'overlay' 	=> __( 'Overlay', 'elementor-extras' ),
						'shift' 	=> __( 'Shift', 'elementor-extras' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				'duration',
				[
					'label' 		=> __( 'Animation Duration', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'step'=> 0.1,
							'max' => 5,
						],
					],
					'selectors' 	=> [
						'.ee-offcanvas__slidebar--{{ID}}' => 'transition-duration: {{SIZE}}s;',
						'body.ee-offcanvas--id-{{ID}} .ee-offcanvas__overlay' => 'transition-duration: {{SIZE}}s;',
					],
				]
			);

			$this->add_control(
				'anchor_navigation',
				[
					'label' 		=> __( 'Anchor Navigation', 'elementor-extras' ),
					'description'	=> __( 'Allow navigation to anchors on page', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'separator'		=> 'before',
					'frontend_available' => true,
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
				]
			);

			$this->add_control(
				'anchor_navigation_speed',
				[
					'label' => __( 'Anchor Navigation Speed', 'elementor-extras' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 500,
					],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 10000,
						],
					],
					'frontend_available' => true,
					'condition'	=> [
						'anchor_navigation!' => '',
					],
				]
			);

			$this->add_control(
				'anchor_navigation_close',
				[
					'label' 		=> __( 'Close After Scroll', 'elementor-extras' ),
					'description'	=> __( 'Close offcanvas after animating to anchor', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'frontend_available' => true,
					'condition'		=> [
						'anchor_navigation!' => '',
					],
				]
			);

			$this->add_control(
				'scroll_fix',
				[
					'label' 	=> __( 'Page Jump Fix', 'elementor-extras' ),
					'description' => sprintf( __( 'Read more about this issue %1$shere%2$s', 'elementor-extras' ), '<a href="https://shop.namogo.com/docs/elementor-extras/offcanvas/#scroll-fix">', '</a>' ),
					'type' 		=> Controls_Manager::SELECT,
					'default'	=> '',
					'options' 	=> [
						'' 			=> __( 'None', 'elementor-extras' ),
						'margin' 	=> __( 'Margin Hack', 'elementor-extras' ),
						'overflow' 	=> __( 'Overflow Fix', 'elementor-extras' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'refresh_widgets',
				[
					'label' 		=> __( 'Refresh Panel Widgets', 'elementor-extras' ),
					'description'	=> __( 'If you are using templates as content for the offcanvas panel, this option will refresh any frontend functionality for all elements inside the panel when opened. Turn this off if you notice strange behaviour or broken elements inside the panel.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Register Trigger Content Controls
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function register_trigger_content_controls() {
		$this->start_controls_section(
			'section_trigger',
			[
				'label' => __( 'Trigger', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control(
				'trigger_source',
				[
					'label' 	=> __( 'Source', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default'	=> 'burger',
					'options' 	=> [
						'burger' 	=> __( 'Burger', 'elementor-extras' ),
						'id' 		=> __( 'Element ID', 'elementor-extras' ),
						'class' 	=> __( 'Element Class', 'elementor-extras' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'trigger_id',
				[
					'label' 		=> __( 'Trigger CSS ID', 'elementor-extras' ),
					'type' 			=> Controls_Manager::TEXT,
					'dynamic'		=> [ 'active' => true ],
					'default' 		=> '',
					'label_block' 	=> false,
					'frontend_available' => true,
					'title' 		=> __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'elementor-extras' ),
					'condition'	=> [
						'trigger_source' => 'id',
					],
				]
			);

			$this->add_control(
				'trigger_class',
				[
					'label' 		=> __( 'Trigger CSS Class', 'elementor-extras' ),
					'type' 			=> Controls_Manager::TEXT,
					'dynamic'		=> [ 'active' => true ],
					'default' 		=> '',
					'label_block' 	=> false,
					'frontend_available' => true,
					'title' 		=> __( 'Add your custom class WITHOUT the DOT key. e.g: my-class', 'elementor-extras' ),
					'condition'	=> [
						'trigger_source' => 'class',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'trigger_position',
				[
					'label' 	=> __( 'Position', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'separator' => 'before',
					'default'	=> 'inline',
					'options' 	=> [
						'inline' 	=> __( 'Inline', 'elementor-extras' ),
						'floating' 	=> __( 'Floating', 'elementor-extras' ),
					],
					'condition'	=> [
						'trigger_source' => 'burger',
					],
				]
			);

			$this->add_control(
				'trigger_placement',
				[
					'label' 	=> __( 'Placement', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default'	=> 'top-left',
					'options' 	=> [
						'top-left' 		=> __( 'Top Left', 'elementor-extras' ),
						'top-center' 	=> __( 'Top Center', 'elementor-extras' ),
						'top-right' 	=> __( 'Top Right', 'elementor-extras' ),
						'middle-right' 	=> __( 'Middle Right', 'elementor-extras' ),
						'bottom-right' 	=> __( 'Bottom Right', 'elementor-extras' ),
						'bottom-center' => __( 'Bottom Center', 'elementor-extras' ),
						'bottom-left' 	=> __( 'Bottom Left', 'elementor-extras' ),
						'middle-left' 	=> __( 'Middle Left', 'elementor-extras' ),
					],
					'prefix_class' => 'ee-offcanvas-placement--',
					'condition'	=> [
						'trigger_source' => 'burger',
						'trigger_position' => 'floating',
					],
				]
			);

			$this->add_responsive_control(
				'trigger_align',
				[
					'label' 		=> __( 'Align', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> '',
					'options' 		=> [
						'flex-start' 	=> [
							'title' 	=> __( 'Left', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-left',
						],
						'center' 		=> [
							'title' 	=> __( 'Center', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-center',
						],
						'flex-end' 		=> [
							'title' 	=> __( 'Right', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-right',
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-button-wrapper' => 'justify-content: {{VALUE}};',
					],
					'condition'	=> [
						'trigger_source' => 'burger',
						'trigger_position' => 'inline',
					],
				]
			);

			$this->add_control(
				'trigger_zindex',
				[
					'label'			=> __( 'zIndex', 'elementor-extras' ),
					'description'   => __( 'Adjust the z-index of the floating trigger. Defaults to 999', 'elementor-extras' ),
					'type'			=> Controls_Manager::NUMBER,
					'default'		=> '999',
					'min'			=> 0,
					'step'			=> 1,
					'condition'		=> [
						'trigger_source' => 'burger',
						'trigger_position' => 'floating',
					],
					'selectors'		=> [
						'{{WRAPPER}} .ee-offcanvas__trigger' => 'z-index: {{SIZE}};',
					]
				]
			);

			$this->add_control(
				'trigger_label_heading',
				[
					'label' 	=> __( 'Label', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition'	=> [
						'trigger_source' => 'burger',
					],
				]
			);

			$this->add_control(
				'trigger_label',
				[
					'label' 		=> __( 'Show', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'condition'		=> [
						'trigger_source' => 'burger',
					],
				]
			);

			$this->add_control(
				'trigger_text',
				[
					'label' 	=> __( 'Text', 'elementor-extras' ),
					'dynamic'	=> [ 'active' => true ],
					'type' 		=> Controls_Manager::TEXT,
					'default'	=> __( 'Menu', 'elementor-extras' ),
					'condition'	=> [
						'trigger_source' => 'burger',
						'trigger_label!' => '',
					],
				]
			);

			$this->add_control(
				'trigger_icon_heading',
				[
					'label' 	=> __( 'Icon', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition'	=> [
						'trigger_source' => 'burger',
					],
				]
			);

			$this->add_control(
				'trigger_icon_position',
				[
					'label' => __( 'Position', 'elementor-extras' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'left',
					'options' => [
						'' 		=> __( 'Hide', 'elementor-extras' ),
						'left' 	=> __( 'Before Label', 'elementor-extras' ),
						'right' => __( 'After Label', 'elementor-extras' ),
					],
					'condition'	=> [
						'trigger_source' => 'burger',
					],
				]
			);	

			$this->add_control(
				'trigger_effect',
				[
					'label' 	=> __( 'Animation', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default'	=> 'arrow',
					'options' 	=> [
						'' 				=> __( 'None', 'elementor-extras' ),
						'arrow' 		=> __( 'Arrow Left', 'elementor-extras' ),
						'arrow-r' 		=> __( 'Arrow Right', 'elementor-extras' ),
						'arrowalt' 		=> __( 'Arrow Alt Left', 'elementor-extras' ),
						'arrowalt-r' 	=> __( 'Arrow Alt Right', 'elementor-extras' ),
						'arrowturn' 	=> __( 'Arrow Turn Left', 'elementor-extras' ),
						'arrowturn-r' 	=> __( 'Arrow Turn Right', 'elementor-extras' ),
						'collapse' 		=> __( 'Collapse Left', 'elementor-extras' ),
						'collapse-r' 	=> __( 'Collapse Right', 'elementor-extras' ),
						'elastic' 		=> __( 'Elastic Left', 'elementor-extras' ),
						'elastic-r' 	=> __( 'Elastic Right', 'elementor-extras' ),
						'emphatic' 		=> __( 'Emphatic Left', 'elementor-extras' ),
						'emphatic-r' 	=> __( 'Emphatic Right', 'elementor-extras' ),
						'slider' 		=> __( 'Slider Left', 'elementor-extras' ),
						'slider-r' 		=> __( 'Slider Right', 'elementor-extras' ),
						'spin' 			=> __( 'Spin Left', 'elementor-extras' ),
						'spin-r' 		=> __( 'Spin Right', 'elementor-extras' ),
						'spring' 		=> __( 'Spring Left', 'elementor-extras' ),
						'spring-r' 		=> __( 'Spring Right', 'elementor-extras' ),
						'stand' 		=> __( 'Stand Left', 'elementor-extras' ),
						'stand-r' 		=> __( 'Stand Right', 'elementor-extras' ),
						'vortex' 		=> __( 'Vortex Left', 'elementor-extras' ),
						'vortex-r' 		=> __( 'Vortex Right', 'elementor-extras' ),
						'minus' 		=> __( 'Minus', 'elementor-extras' ),
						'squeeze' 		=> __( 'Squeeze', 'elementor-extras' ),
					],
					'condition'	=> [
						'trigger_source' => 'burger',
					],
				]
			);

			$this->add_control(
				'trigger_icon_indent',
				[
					'label' => __( 'Spacing', 'elementor-extras' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'max' => 50,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-icon--right' => 'margin-left: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ee-icon--left' => 'margin-right: {{SIZE}}{{UNIT}};',
					],
					'condition'	=> [
						'trigger_source' => 'burger',
						'trigger_label!' => '',
					],
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Register Header Content Controls
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function register_title_content_controls() {

		$this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Title', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control(
				'header_title',
				[
					'label' 		=> __( 'Show', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
				]
			);

			$this->add_control(
				'header_title_text',
				[
					'label' 	=> __( 'Title', 'elementor-extras' ),
					'dynamic'	=> [ 'active' => true ],
					'type' 		=> Controls_Manager::TEXT,
					'default'	=> __( 'Menu', 'elementor-extras' ),
					'condition' => [
						'header_title!' => '',
					],
				]
			);

			$this->add_control(
				'header_title_tag',
				[
					'label' 	=> __( 'Title HTML Tag', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'options' 	=> [
						'h1' 	=> __( 'H1', 'elementor-extras' ),
						'h2' 	=> __( 'H2', 'elementor-extras' ),
						'h3' 	=> __( 'H3', 'elementor-extras' ),
						'h4' 	=> __( 'H4', 'elementor-extras' ),
						'h5' 	=> __( 'H5', 'elementor-extras' ),
						'h6' 	=> __( 'H6', 'elementor-extras' ),
						'div' 	=> __( 'div', 'elementor-extras' ),
						'span' 	=> __( 'span', 'elementor-extras' ),
						'p' 	=> __( 'p', 'elementor-extras' ),
					],
					'default' => 'h3',
					'condition' => [
						'header_title!' => '',
					],
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Register Close Content Controls
	 *
	 * @since  2.1.4
	 * @return void
	 */
	protected function register_close_content_controls() {

		$this->start_controls_section(
			'section_close',
			[
				'label' => __( 'Close', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);	

			$this->add_control(
				'header_close_source',
				[
					'label'		=> __( 'Source', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '',
					'options' 	=> [
						''			=> __( 'Default', 'elementor-extras' ),
						'id' 		=> __( 'Element ID', 'elementor-extras' ),
						'class' 	=> __( 'Element Class', 'elementor-extras' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'header_close_id',
				[
					'label' 		=> __( 'Close CSS ID', 'elementor-extras' ),
					'type' 			=> Controls_Manager::TEXT,
					'dynamic'		=> [ 'active' => true ],
					'default' 		=> '',
					'label_block' 	=> false,
					'frontend_available' => true,
					'title' 		=> __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'elementor-extras' ),
					'condition'	=> [
						'header_close_source' => 'id',
					],
				]
			);

			$this->add_control(
				'header_close_class',
				[
					'label' 		=> __( 'Close CSS Class', 'elementor-extras' ),
					'type' 			=> Controls_Manager::TEXT,
					'dynamic'		=> [ 'active' => true ],
					'default' 		=> '',
					'label_block' 	=> false,
					'frontend_available' => true,
					'title' 		=> __( 'Add your custom class WITHOUT the DOT key. e.g: my-class', 'elementor-extras' ),
					'condition'	=> [
						'header_close_source' => 'class',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'header_close_position',
				[
					'label'		=> __( 'Position', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'after',
					'options' 	=> [
						''			=> __( 'Hide', 'elementor-extras' ),
						'left' 		=> __( 'Left', 'elementor-extras' ),
						'right'		=> __( 'Right', 'elementor-extras' ),
						'custom'	=> __( 'Custom', 'elementor-extras' ),
					],
					'condition' => [
						'header_close_source' => '',
					],
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Register Content Boxes Controls
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function register_content_boxes_content_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content Boxes', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

			$this->set_sidebars_vars();

			$repeater = new Repeater();

			$repeater->add_control(
				'content_type',
				[
					'label'		=> __( 'Type', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'text',
					'options' 	=> [
						'text' 		=> __( 'Text', 'elementor-extras' ),
						'template' 	=> __( 'Template', 'elementor-extras' ),
						'sidebar' 	=> __( 'WordPress Sidebar', 'elementor-extras' ),
					],
				]
			);

			$repeater->add_control(
				'content',
				[
					'label' 	=> __( 'Content', 'elementor-extras' ),
					'type' 		=> Controls_Manager::WYSIWYG,
					'dynamic'	=> [ 'active' => true ],
					'default' 	=> __( 'I am a content box for offcanvas navigation', 'elementor-extras' ),
					'condition'	=> [
						'content_type' => 'text',
					],
				]
			);

			TemplatesControl::add_controls( $repeater, [
				'condition' => [
					'content_type' => 'template',
				],
				'prefix' => 'content_',
			] );

			$repeater->add_control( 'sidebar', [
				'label' => __( 'Choose Sidebar', 'elementor-extras' ),
				'type' => Controls_Manager::SELECT,
				'default' => $this->_sidebars_default_key,
				'options' => $this->_sidebars_options,
				'condition'	=> [
					'content_type' => 'sidebar',
				],
			] );

			$this->add_control(
				'content_boxes',
				[
					'label' 	=> '',
					'type' 		=> Controls_Manager::REPEATER,
					'default' 	=> [
						[
							'content_type' => 'text',
							'content' => __( 'I am a content box for offcanvas navigation', 'elementor-extras' ),
						],
						[
							'content_type' => 'text',
							'content' => __( 'I am a content box for offcanvas navigation', 'elementor-extras' ),
						],
					],
					'fields' 		=> $repeater->get_controls(),
					'title_field' 	=> 'Box: {{{ content_type }}}',
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Register Offcanvas Style Controls
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function register_offcanvas_style_controls() {
		$this->start_controls_section(
			'section_offcanvas_style',
			[
				'label' => __( 'Offcanvas', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'offcanvas_width',
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
						'.ee-offcanvas__slidebar.ee-offcanvas__slidebar--{{ID}}' => 'width: {{SIZE}}{{UNIT}};',
					],
					'conditions'	=> [
						'relation'	=> 'or',
						'terms'		=> [
							[
								'name'		=> 'position',
								'operator' 	=> '==',
								'value'		=> 'left',
							],
							[
								'name'		=> 'position',
								'operator' 	=> '==',
								'value'		=> 'right',
							],
						],
					],
				]
			);

			$this->add_responsive_control(
				'offcanvas_height',
				[
					'label' 		=> __( 'Height', 'elementor-extras' ),
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
						'.ee-offcanvas__slidebar.ee-offcanvas__slidebar--{{ID}}' => 'height: {{SIZE}}{{UNIT}};',
					],
					'conditions'	=> [
						'relation'	=> 'or',
						'terms'		=> [
							[
								'name'		=> 'position',
								'operator' 	=> '==',
								'value'		=> 'top',
							],
							[
								'name'		=> 'position',
								'operator' 	=> '==',
								'value'		=> 'bottom',
							],
						],
					],
				]
			);

			$this->add_responsive_control(
				'offcanvas_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'.ee-offcanvas__slidebar--{{ID}} .ee-offcanvas__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'offcanvas_background',
				[
					'label' 	=> __( 'Background Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'.ee-offcanvas__slidebar.ee-offcanvas__slidebar--{{ID}}' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'offcanvas',
					'selector' 	=> '.ee-offcanvas__slidebar--{{ID}}',
					'separator'	=> '',
					'condition'	=> [
						'animation' => 'overlay',
					],
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Register Offcanvas Style Controls
	 *
	 * @since  2.2.11
	 * @return void
	 */
	protected function register_page_style_controls() {

		$this->start_controls_section(
			'section_page_style',
			[
				'label' => __( 'Page', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
			$this->add_control(
				'page_style_notice',
				[
					'type' 				=> Controls_Manager::RAW_HTML,
					'raw' 				=> __( 'The settings below apply to the page when the offcanvas panel is opened.', 'elementor-extras' ),
					'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-info',
				]
			);

			$this->add_control(
				'page_background_color',
				[
					'type' 			=> Controls_Manager::COLOR,
					'label' 		=> __( 'Container Background', 'elementor-extras' ),
					'description' 	=> __( 'When the offcanvas opens, the page is wrapped in an element that needs a background colour which is the same as the page.', 'elementor-extras' ),
					'default'		=> 'rgba(255,255,255,1)',
					'selectors' 	=> [
						'body.ee-offcanvas--id-{{ID}} .ee-offcanvas__container' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'overlay_background',
				[
					'label' 	=> __( 'Overlay Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'body.ee-offcanvas--id-{{ID}} .ee-offcanvas__overlay' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'overlay_opacity',
				[
					'label' 	=> __( 'Overlay Opacity (%)', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 0.8,
					],
					'range' 	=> [
						'px' 	=> [
							'max' 	=> 1,
							'min' 	=> 0,
							'step' 	=> 0.01,
						],
					],
					'selectors' => [
						'body.ee-offcanvas--id-{{ID}}.ee-offcanvas--opening .ee-offcanvas__overlay,
						 body.ee-offcanvas--id-{{ID}}.ee-offcanvas--open .ee-offcanvas__overlay' => 'opacity: {{SIZE}};',
					],
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Register Trigger Style Controls
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function register_trigger_style_controls() {
		$this->start_controls_section(
			'section_trigger_style',
			[
				'label' => __( 'Trigger', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'trigger_source' => 'burger',
				],
			]
		);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'trigger_border',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'selector' 	=> '{{WRAPPER}} .ee-button',
					'condition'	=> [
						'trigger_source' => 'burger',
					],
				]
			);

			$this->add_control(
				'trigger_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'	=> [
						'trigger_source' => 'burger',
					],
				]
			);

			$this->add_responsive_control(
				'trigger_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-button-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'	=> [
						'trigger_source' => 'burger',
					],
				]
			);

			$this->add_responsive_control(
				'trigger_margin',
				[
					'label' 		=> __( 'Margin', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-button-content-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'	=> [
						'trigger_source' => 'burger',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'trigger',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'selector' 	=> '{{WRAPPER}} .ee-button-wrapper',
					'condition'	=> [
						'trigger_source' => 'burger',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 		=> 'trigger',
					'selector' 	=> '{{WRAPPER}} .ee-hamburger-inner,
									{{WRAPPER}} .ee-hamburger-inner:before,
									{{WRAPPER}} .ee-hamburger-inner:after,
									{{WRAPPER}} .ee-button',
					'separator'	=> '',
					'condition'	=> [
						'trigger_source' => 'burger',
					],
				]
			);

			$this->start_controls_tabs( 'trigger_tabs_hover' );

			$this->start_controls_tab( 'trigger_tab_default', [
				'label' => __( 'Default', 'elementor-extras' ),
				'condition'	=> [
					'trigger_source' => 'burger',
				],
			] );

				$this->add_control(
					'trigger_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-hamburger-inner,
							 {{WRAPPER}} .ee-hamburger-inner:before,
							 {{WRAPPER}} .ee-hamburger-inner:after' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} .ee-button' => 'color: {{VALUE}};',
						],
						'condition'	=> [
							'trigger_source' => 'burger',
						],
					]
				);

				$this->add_control(
					'trigger_background',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-button' => 'background-color: {{VALUE}};',
						],
						'condition'	=> [
							'trigger_source' => 'burger',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'trigger_tab_hover', [
				'label' => __( 'Hover', 'elementor-extras' ),
				'condition'	=> [
					'trigger_source' => 'burger',
				],
			] );

				$this->add_control(
					'trigger_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-button:hover .ee-hamburger-inner,
							 {{WRAPPER}} .ee-button:hover .ee-hamburger-inner:before,
							 {{WRAPPER}} .ee-button:hover .ee-hamburger-inner:after' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} .ee-button:hover' => 'color: {{VALUE}};',
						],
						'condition'	=> [
							'trigger_source' => 'burger',
						],
					]
				);

				$this->add_control(
					'trigger_background_hover',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-button:hover' => 'background-color: {{VALUE}};',
						],
						'condition'	=> [
							'trigger_source' => 'burger',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'trigger_tab_open', [
				'label' => __( 'Open', 'elementor-extras' ),
				'condition'	=> [
					'trigger_source' => 'burger',
				],
			] );

				$this->add_control(
					'trigger_color_open',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-button.ee--is-active .ee-hamburger-inner,
							 {{WRAPPER}} .ee-button.ee--is-active .ee-hamburger-inner:before,
							 {{WRAPPER}} .ee-button.ee--is-active .ee-hamburger-inner:after' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} .ee-button.ee--is-active' => 'color: {{VALUE}};',
						],
						'condition'	=> [
							'trigger_source' => 'burger',
						],
					]
				);

				$this->add_control(
					'trigger_background_open',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-button.ee--is-active' => 'background-color: {{VALUE}};',
						],
						'condition'	=> [
							'trigger_source' => 'burger',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'trigger_icon_style_heading',
				[
					'label' 	=> __( 'Icon', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition'	=> [
						'trigger_source' => 'burger',
					],
				]
			);

			$this->add_responsive_control(
				'trigger_icon_size',
				[
					'label' 	=> __( 'Icon Size', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 1,
					],
					'range' 	=> [
						'px' 	=> [
							'max' 	=> 3,
							'min' 	=> 0.1,
							'step' 	=> 0.01,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-hamburger-box' => 'font-size: {{SIZE}}em;',
					],
					'condition'	=> [
						'trigger_source' => 'burger',
					],
				]
			);

			$this->add_control(
				'trigger_label_style_heading',
				[
					'label' 	=> __( 'Label', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition'	=> [
						'trigger_source' => 'burger',
						'trigger_label!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'trigger_label',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-button-text',
					'condition'	=> [
						'trigger_source' => 'burger',
						'trigger_label!' => '',
					],
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Register Title Style Controls
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function register_title_style_controls() {
		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __( 'Title', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'header_title!' => '',
				],
			]
		);

			$this->add_responsive_control(
				'title_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'.ee-offcanvas__slidebar--{{ID}} .ee-offcanvas__header__title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'	=> [
						'header_title!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'title_align',
				[
					'label' 		=> __( 'Align', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> '',
					'options' 		=> [
						'left' 			=> [
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
					'selectors' => [
						'.ee-offcanvas__slidebar--{{ID}} .ee-offcanvas__header__title' => 'text-align: {{VALUE}};',
					],
					'condition'	=> [
						'header_title!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'title',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
					],
					'selector' 	=> '.ee-offcanvas__slidebar--{{ID}} .ee-offcanvas__header__title',
					'condition'	=> [
						'header_title!' => '',
					],
				]
			);

			$this->add_control(
				'title_color',
				[
					'label' 	=> __( 'Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'.ee-offcanvas__slidebar--{{ID}} .ee-offcanvas__header__title' => 'color: {{VALUE}};',
					],
					'condition'	=> [
						'header_title!' => '',
					],
				]
			);

		$this->end_controls_section();

	}

	/**
	 * Register Close Style Controls
	 *
	 * @since  2.1.4
	 * @return void
	 */
	protected function register_close_style_controls() {
		$this->start_controls_section(
			'section_close_style',
			[
				'label' => __( 'Close', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'header_close_source' => '',
					'header_close_position!' => '',
				],
			]
		);

			$this->add_responsive_control(
				'close_size',
				[
					'label' 	=> __( 'Size', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 30,
					],
					'range' 	=> [
						'px' 	=> [
							'max' 	=> 50,
							'min' 	=> 10,
							'step' 	=> 1,
						],
					],
					'selectors' => [
						'.ee-offcanvas__slidebar--{{ID}} .ee-offcanvas__header__close' => 'font-size: {{SIZE}}px;',
					],
					'condition'	=> [
						'header_close_position!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'close_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'.ee-offcanvas__slidebar--{{ID}} .ee-offcanvas__header__close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'	=> [
						'header_close_position!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'close_border',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'selector' 	=> '.ee-offcanvas__slidebar--{{ID}} .ee-offcanvas__header__close',
					'condition'	=> [
						'header_close_position!' => '',
					],
				]
			);

			$this->add_control(
				'close_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'.ee-offcanvas__slidebar--{{ID}} .ee-offcanvas__header__close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'	=> [
						'header_close_position!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 		=> 'close',
					'selector' 	=> '.ee-offcanvas__slidebar--{{ID}} .ee-offcanvas__header__close',
					'separator'	=> '',
					'condition'	=> [
						'header_close_position!' => '',
					],
				]
			);

			$this->start_controls_tabs( 'close_tabs_hover' );

			$this->start_controls_tab( 'close_tab_default', [
				'label' => __( 'Default', 'elementor-extras' ),
				'condition'	=> [
					'header_close_position!' => '',
				],
			] );

				$this->add_control(
					'close_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'.ee-offcanvas__slidebar--{{ID}} .ee-offcanvas__header__close' => 'color: {{VALUE}};',
						],
						'condition'	=> [
							'header_close_position!' => '',
						],
					]
				);

				$this->add_control(
					'close_background_color',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'.ee-offcanvas__slidebar--{{ID}} .ee-offcanvas__header__close' => 'background-color: {{VALUE}};',
						],
						'condition'	=> [
							'header_close_position!' => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'close_tab_hover', [
				'label' => __( 'Hover', 'elementor-extras' ),
				'condition'	=> [
					'header_close_position!' => '',
				],
			] );

				$this->add_control(
					'close_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'.ee-offcanvas__slidebar--{{ID}} .ee-offcanvas__header__close:hover' => 'color: {{VALUE}};',
						],
						'condition'	=> [
							'header_close_position!' => '',
						],
					]
				);

				$this->add_control(
					'close_background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'.ee-offcanvas__slidebar--{{ID}} .ee-offcanvas__header__close:hover' => 'background-color: {{VALUE}};',
						],
						'condition'	=> [
							'header_close_position!' => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register Content Boxes Style Controls
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function register_content_boxes_style_controls() {

		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Content Boxes', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'boxes_spacing',
				[
					'label' 		=> __( 'Boxes Spacing', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px' ],
					'default'		=> [
						'size' => 24,
					],
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' 	=> [
						'.ee-offcanvas__slidebar--{{ID}} .ee-offcanvas__content__item:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'boxes_padding',
				[
					'label' 		=> __( 'Boxes Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'.ee-offcanvas__slidebar--{{ID}} .ee-offcanvas__content__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'boxes',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'selector' 	=> '.ee-offcanvas__slidebar--{{ID}} .ee-offcanvas__content__item',
				]
			);

			$this->add_control(
				'boxes_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'.ee-offcanvas__slidebar--{{ID}} .ee-offcanvas__content__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'boxes',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '.ee-offcanvas__slidebar--{{ID}} .ee-offcanvas__content__item',
				]
			);

			$this->add_control(
				'boxes_foreground_color',
				[
					'label' 	=> __( 'Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'.ee-offcanvas__slidebar--{{ID}} .ee-offcanvas__content__item' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'boxes_background_color',
				[
					'label' 	=> __( 'Background Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'.ee-offcanvas__slidebar--{{ID}} .ee-offcanvas__content__item' => 'background-color: {{VALUE}};',
					],
				]
			);

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
	public function render() {

		$settings = $this->get_settings_for_display();

		$has_placeholder = true;
		$placeholder = '';

		if ( 'burger' === $settings['trigger_source'] ) {
			if ( 'floating' === $settings['trigger_position'] ) {
				$placeholder .= __( 'Your menu trigger is floating. ', 'elementor-extras' );
				$has_placeholder = true;
			} else {
				$has_placeholder = false;
			}

			$this->render_trigger();
			
		} else {
			$placeholder .= __( 'You selected to trigger offcanvas using another element on the page. ', 'elementor-extras' );
			$has_placeholder = true;
		}

		if ( $has_placeholder ) {
			$placeholder .= __( ' This placeholder will not be shown on the live page.', 'elementor-extras' );

			echo $this->render_placeholder( [
				'body' => $placeholder,
			] );
		}

		if ( ! empty( $settings['content_boxes'] ) ) {
			$this->render_content_boxes();
		}
	}

	/**
	 * Render Trigger
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function render_trigger() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( [
			'button-wrapper' => [
				'class' => [
					'ee-button-wrapper',
					'ee-offcanvas-position--' . $settings['trigger_position'],
				],
			],
			'button' => [
				'class' => [
					'ee-button',
					'ee-hamburger',
					'ee-hamburger--' . $settings['trigger_effect'],
					'ee-offcanvas__trigger',
					'ee-offcanvas__trigger--' . $settings['trigger_position'],
				],
				'id' => 'slidebar-trigger_' . $this->get_id(),
				'data-slidebar-id' => $this->get_id(),
				'aria-label' => 'Menu',
				'aria-controls' => 'navigation',
			],
			'button-content-wrapper' => [
				'class' => [
					'ee-button-icon',
					'ee-icon--' . $settings['trigger_icon_position'],
					'ee-hamburger-box',
				],
			],
			'button-content' => [
				'class' => [
					'ee-button-content-wrapper',
				],
			],
			'button-text' => [
				'class' => [
					'ee-button-text',
				],
			],

			'button-inner' => [
				'class' => [
					'ee-hamburger-inner',
				],
			],
		] );

		?><div <?php echo $this->get_render_attribute_string( 'button-wrapper' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'button' ); ?>>

				<span <?php echo $this->get_render_attribute_string( 'button-content' ); ?>>

					<?php if ( '' !== $settings['trigger_icon_position'] ) { ?>
					<span <?php echo $this->get_render_attribute_string( 'button-content-wrapper' ); ?>>
						<span <?php echo $this->get_render_attribute_string( 'button-inner' ); ?>></span>
					</span>
					<?php } ?>

					<?php if ( '' !== $settings['trigger_label'] ) : ?>
					<span <?php echo $this->get_render_attribute_string( 'button-text' ); ?>>
						<?php echo $settings['trigger_text']; ?>
					</span>
					<?php endif; ?>

				</span>

			</div>
		</div><?php
	}

	/**
	 * Render Content Boxes
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function render_content_boxes() {
		$settings = $this->get_settings_for_display();

		$has_title = '' !== $settings['header_title'];
		$has_close = ! in_array( $settings['header_close_position'], ['', 'custom'] );

		$this->add_render_attribute( [
			'content-boxes' => [
				'class' => [
					'ee-offcanvas__content',
					'ee-offcanvas__content-' . $this->get_id(),
				],
			],
			'content-header' => [
				'class' => [
					'ee-offcanvas__header',
				],
			],
			'header-title' => [
				'class' => [
					'ee-offcanvas__header__title',
				],
			],
			'header-close' => [
				'class' => [
					'ee-offcanvas__header__close',
				],
			],
		] );

		if ( $has_close ) {
			$this->add_render_attribute( [
				'content-header' => [
					'class' => [
						'ee-offcanvas__header-close--' . $settings['header_close_position'],
					],
				],
			] );
		}

		if ( $has_title ) {
			$title_tag = $settings['header_title_tag'];

			$this->add_render_attribute( [
				'content-header' => [
					'class' => [
						'ee-offcanvas__header--hide-title',
					],
				],
			] );
		}

		?><div <?php echo $this->get_render_attribute_string( 'content-boxes' ); ?>>
			<?php if ( $has_title || $has_close ) { ?>
			<div <?php echo $this->get_render_attribute_string( 'content-header' ); ?>>
				<?php if ( $has_title ) { ?>
					<<?php echo $title_tag; ?> <?php echo $this->get_render_attribute_string( 'header-title' ); ?>>
						<?php echo $settings['header_title_text']; ?>
					</<?php echo $title_tag; ?>>
				<?php } ?>
				<?php if ( $has_close ) { ?>
					<div <?php echo $this->get_render_attribute_string( 'header-close' ); ?>><i class="eicon-close"></i></div>
				<?php } ?>
			</div>
			<?php } ?>
			<?php foreach ( $settings['content_boxes'] as $index => $item ) {

				$box_key = $this->get_repeater_setting_key( 'box', 'content_boxes', $index );

				$this->add_render_attribute( $box_key, [
					'class' => [
						'ee-offcanvas__content__item',
						'elementor-repeater-item-' . $item['_id'],
					]
				] );

				?><div <?php echo $this->get_render_attribute_string( $box_key ); ?>><?php

					switch ( $item['content_type'] ) {
						case 'text':
							$this->render_text( $index, $item );
							break;
						case 'sidebar':
							$this->render_sidebar( $item );
							break;
						case 'template':
							$template_key = 'content_' . $item['content_template_type'] . '_template_id';
							if ( array_key_exists( $template_key, $item ) )
								TemplatesControl::render_template_content( $item[ $template_key ], $this );
							break;
						default:
							break;
					}

				?></div><?php
			} ?>
		</div><?php
	}

	/**
	 * Render Sidebar
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function render_sidebar( $item ) {
		$sidebar = $item['sidebar'];

		if ( empty( $sidebar ) ) {
			return;
		}

		?><aside class="widget-area" role="complementary"><?php
			dynamic_sidebar( $sidebar );
		?></aside><?php
	}

	/**
	 * Render Text
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function render_text( $index, $item ) {
		echo $this->parse_text_editor( $item['content'] );
	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering. None in this case
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function _content_template() {}

}