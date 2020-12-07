<?php
namespace ElementorExtras\Modules\ScrollIndicator\Widgets;

// Extras for Elementor Classes
use ElementorExtras\Base\Extras_Widget;
use ElementorExtras\Modules\ScrollIndicator\Skins;
use ElementorExtras\Modules\ScrollIndicator\Module as Module;
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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Scroll_Indicator
 *
 * @since 2.1.0
 */
class Scroll_Indicator extends Extras_Widget {

	/**
	 * Has template content
	 *
	 * @since  2.1.0
	 * @var    bool
	 */
	protected $_has_template_content = false;

	/**
	 * Nav menu index
	 *
	 * @since  2.1.0
	 * @var    int
	 */
	protected $nav_menu_index = 1;

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_name() {
		return 'ee-scroll-indicator';
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
		return __( 'Scroll Indicator', 'elementor-extras' );
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
		return 'nicon nicon-scroll-indicator';
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
		return [
			'ee-scroll-indicator',
			'hotips',
		];
	}

	/**
	 * Whether the reload preview is required or not.
	 *
	 * Used to determine whether the reload preview is required.
	 *
	 * @since  2.1.0
	 * @return bool
	 */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Register Skins
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function _register_skins() {
		$this->add_skin( new Skins\Skin_List( $this ) );
		$this->add_skin( new Skins\Skin_Bar( $this ) );
		$this->add_skin( new Skins\Skin_Bullets( $this ) );
	}

	/**
	 * Register Widget Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function _register_controls() {
		// Content tab
		$this->register_settings_controls();
		$this->register_content_controls();
	}

	/**
	 * Register Settings Controls
	 *
	 * @since  2.1.0
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
				'click',
				[
					'label' 		=> __( 'Enable Click', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'scroll_offset',
				[
					'label' 	=> __( 'Scroll Offset', 'elementor-extras' ),
					'description' => __( 'Offset for scrolling to element on click.', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'frontend_available' => true,
					'default' 	=> [
						'size' 	=> 0,
						'unit' 	=> 'px',
					],
					'range' => [
						'px' => [
							'min'	=> -200,
							'max' 	=> 200,
							'step' 	=> 1,
						],
					],
					'condition' => [
						'click!' => '',
					],
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Register Content Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function register_content_controls() {
		$this->start_controls_section(
			'section_elements',
			[
				'label' => __( 'Sections', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

			$repeater = new Repeater();

			$repeater->add_control(
				'selector',
				[
					'label' 		=> __( 'Element ID', 'elementor-extras' ),
					'description'	=> __( 'Enter the element CSS ID which you want the indicator to for this section.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::TEXT,
					'dynamic'		=> [ 'active' => true ],
					'title' 		=> __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'elementor-extras' ),
				]
			);

			$repeater->add_control(
				'title',
				[
					'label'		=> __( 'Title', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'dynamic'		=> [ 'active' => true ],
					'default' 	=> __( 'Element title', 'elementor-extras' ),
				]
			);

			$repeater->add_control(
				'subtitle',
				[
					'label'		=> __( 'Subtitle', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'dynamic'		=> [ 'active' => true ],
					'default' 	=> __( 'Element subtitle', 'elementor-extras' ),
				]
			);

			$repeater->start_controls_tabs( 'progress' );

			$repeater->start_controls_tab( 'tab_progress_start', [ 'label' => __( 'Start', 'elementor-extras' ) ] );

				$repeater->add_control(
					'progress_start',
					[
						'label' 		=> __( 'Start At', 'elementor-extras' ),
						'type' 			=> Controls_Manager::CHOOSE,
						'description'	=> __( 'Set when the progress starts. Example: "Top to Top" means progress starts when the top of the window hits the top of section.', 'elementor-extras' ),
						'default'		=> 'top-top',
						'label_block'	=> false,
						'options' 		=> [
							'top-top'    	=> [
								'title' 	=> __( 'Top to Top', 'elementor-extras' ),
								'icon' 		=> 'nicon nicon-top-top',
							],
							'bottom-top'	=> [
								'title' 	=> __( 'Bottom to Top', 'elementor-extras' ),
								'icon' 		=> 'nicon nicon-bottom-top',
							],
						],
					]
				);

				$repeater->add_control(
					'progress_start_offset',
					[
						'label' 	=> __( 'Start Offset', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SLIDER,
						'default' 	=> [
							'size' 	=> 0,
							'unit' 	=> 'px',
						],
						'range' => [
							'px' => [
								'min'	=> -200,
								'max' 	=> 200,
								'step' 	=> 1,
							],
						],
					]
				);

			$repeater->end_controls_tab();

			$repeater->start_controls_tab( 'tab_progress_end', [ 'label' => __( 'End', 'elementor-extras' ) ] );

				$repeater->add_control(
					'progress_end',
					[
						'label' 		=> __( 'End At', 'elementor-extras' ),
						'type' 			=> Controls_Manager::CHOOSE,
						'description'	=> __( 'Set when the progress ends. Example: "Top to Bottom" means progress ends when the top of the window hits the bottom of section.', 'elementor-extras' ),
						'default'		=> 'top-bottom',
						'label_block'	=> false,
						'options' 		=> [
							'top-bottom'	=> [
								'title' 	=> __( 'Top to Bottom', 'elementor-extras' ),
								'icon' 		=> 'nicon nicon-top-bottom',
							],
							'bottom-bottom'	=> [
								'title' 	=> __( 'Bottom to Bottom', 'elementor-extras' ),
								'icon' 		=> 'nicon nicon-bottom-bottom',
							],
						],
					]
				);

				$repeater->add_control(
					'progress_end_offset',
					[
						'label' 	=> __( 'End Offset', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SLIDER,
						'default' 	=> [
							'size' 	=> 0,
							'unit' 	=> 'px',
						],
						'range' => [
							'px' => [
								'min'	=> -200,
								'max' 	=> 200,
								'step' 	=> 1,
							],
						],
					]
				);

			$repeater->end_controls_tab();

			$repeater->end_controls_tabs();

			$this->add_control(
				'sections',
				[
					'label' 	=> '',
					'type' 		=> Controls_Manager::REPEATER,
					'default' 	=> [
						[
							'selector'	=> '',
							'title' 	=> __( 'Section', 'elementor-extras' ),
							'subtitle' 	=> __( 'Section subtitle', 'elementor-extras' ),
						],
					],
					'fields' 		=> $repeater->get_controls(),
					'title_field' 	=> '{{{ title }}}',
				]
			);

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