<?php
namespace ElementorExtras\Modules\Posts\Skins;

// Extras for Elementor Classes
use ElementorExtras\Group_Control_Transition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\Posts\Skins
 *
 * @since  1.6.0
 */
class Skin_Carousel extends Skin_Base {

	/**
	 * Get ID
	 * 
	 * Gets the current skin ID
	 *
	 * @since  1.6.0
	 * @return string
	 */
	public function get_id() {
		return 'carousel';
	}

	/**
	 * Get Title
	 * 
	 * Gets the current skin title
	 *
	 * @since  1.6.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Carousel', 'elementor-extras' );
	}

	/**
	 * Register Controls Actions
	 * 
	 * Registers controls at specific points in the Controls Stack
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function _register_controls_actions() {
		parent::_register_controls_actions();

		add_action( 'elementor/element/posts-extra/section_query/after_section_end', [ $this, 'register_carousel_controls' ] );
		add_action( 'elementor/element/posts-extra/section_layout/before_section_end', [ $this, 'inject_layout_content_controls' ] );
		add_action( 'elementor/element/posts-extra/section_style_posts/after_section_end', [ $this, 'register_carousel_style_controls' ] );
	}

	/**
	 * Register Layout Content Controls
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function register_layout_content_controls() {
		$slides_per_column = range( 1, 6 );
		$slides_per_column = array_combine( $slides_per_column, $slides_per_column );

		$this->add_control(
			'carousel_heading',
			[
				'label' 	=> __( 'Carousel', 'elementor-extras' ),
				'type' 		=> Controls_Manager::HEADING,
				'separator'	=> 'before',
			]
		);

		$this->add_responsive_control(
			'direction',
			[
				'type' 				=> Controls_Manager::SELECT,
				'label' 			=> __( 'Orientation', 'elementor-extras' ),
				'default'			=> 'horizontal',
				'tablet_default'	=> 'horizontal',
				'mobile_default'	=> 'horizontal',
				'options' 			=> [
					'horizontal' 	=> __( 'Horizontal', 'elementor-extras' ),
					'vertical' 		=> __( 'Vertical', 'elementor-extras' ),
				],
				'frontend_available' 	=> true,
			]
		);

		$this->add_responsive_control(
			'slides_per_view',
			[
				'label' 			=> __( 'Slides Per View', 'elementor-extras' ),
				'type' 				=> Controls_Manager::SELECT,
				'default' 			=> '',
				'tablet_default' 	=> '',
				'mobile_default' 	=> '',
				'options' => [
					''	=> __( 'Default', 'elementor-extras' ),
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'slides_per_column',
			[
				'type' 					=> Controls_Manager::SELECT,
				'label' 				=> __( 'Slides Per Column', 'elementor-extras' ),
				'options' 				=> [ '' => __( 'Default', 'elementor-extras' ) ] + $slides_per_column,
				'condition'				=> [
					$this->get_control_id( 'direction' ) => 'horizontal',
				],
				'frontend_available' 	=> true,
			]
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __( 'Slides to Scroll', 'elementor-extras' ),
				'options' 		=> [ '' => __( 'Default', 'elementor-extras' ) ] + $slides_per_column,
				'frontend_available' => true,
			]
		);

		parent::register_layout_content_controls();

		$this->update_control( 'grid_columns_spacing', [
			'label' => __( 'Grid Spacing', 'elementor-extras' ),
		] );

		$this->remove_control( 'grid_rows_spacing' );
	}

	/**
	 * Inject Layout Content Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	public function inject_layout_content_controls() {

		$this->parent->start_injection( [
			'at' => 'before',
			'of' => 'carousel_grid_columns_spacing',
		] );

			$this->add_control(
				'height',
				[
					'label' 		=> __( 'Height', 'elementor-extras' ),
					'description'	=> __( 'The carousel needs to have a fixed defined height to work in vertical mode.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px', '%', 'vh' ],
					'default' => [
						'size' => 500,
						'unit' => 'px',
					],
					'range' 		=> [
						'px' 		=> [
							'min' => 200,
							'max' => 2000,
						],
						'%' 		=> [
							'min' => 0,
							'max' => 100,
						],
						'vh' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-swiper__container' => 'height: {{SIZE}}{{UNIT}};',
					],
					'condition'		=> [
						$this->get_control_id( 'direction' ) => 'vertical',
					],
				]
			);

		$this->parent->end_injection();

		$this->parent->start_injection( [
			'at' => 'after',
			'of' => 'slides_to_scroll',
		] );

			$this->add_responsive_control(
				'slides_align',
				[
					'label' 		=> __( 'Vertical Align', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'stretch',
					'options' 		=> [
						'top' 			=> [
							'title' 	=> __( 'Top', 'elementor-extras' ),
							'icon' 		=> 'eicon-v-align-top',
						],
						'middle' 		=> [
							'title' 	=> __( 'Middle', 'elementor-extras' ),
							'icon' 		=> 'eicon-v-align-middle',
						],
						'bottom' 		=> [
							'title' 	=> __( 'Bottom', 'elementor-extras' ),
							'icon' 		=> 'eicon-v-align-bottom',
						],
						'stretch' 		=> [
							'title' 	=> __( 'Stretch', 'elementor-extras' ),
							'icon' 		=> 'eicon-v-align-stretch',
						],
					],
					'prefix_class' 	=> 'ee-grid-align%s--',
				]
			);

		$this->parent->end_injection();
	}

	/**
	 * Register Carousel Controls
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function register_carousel_controls() {

		$this->start_controls_section(
			'section_carousel',
			[
				'label' => __( 'Carousel', 'elementor-extras' ),
			]
		);

			$this->add_control(
				'effect',
				[
					'type' 			=> Controls_Manager::SELECT,
					'label' 		=> __( 'Effect', 'elementor-extras' ),
					'default' 		=> 'slide',
					'options' 		=> [
						'slide' 	=> __( 'Slide', 'elementor-extras' ),
						'fade' 		=> __( 'Fade', 'elementor-extras' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'effect_fade_warning',
				[
					'type' 				=> Controls_Manager::RAW_HTML,
					'raw' 				=> __( 'The Fade effect ignores the Slides per View and Slides per Column settings', 'elementor-extras' ),
					'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-info',
					'condition' 		=> [
						$this->get_control_id('effect') => 'fade',
					],
				]
			);

			$this->add_control(
				'speed',
				[
					'label' 	=> __( 'Duration (ms)', 'elementor-extras' ),
					'description' => __( 'Duration of the effect transition.', 'elementor-extras' ) ,
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 300,
						'unit' 	=> 'px',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 2000,
							'step'	=> 100,
						],
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'resistance_ratio',
				[
					'label' 		=> __( 'Resistance', 'elementor-extras' ),
					'description'	=> __( 'Set the value for resistant bounds.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 		=> [
						'size' 		=> 0.25,
						'unit' 		=> 'px',
					],
					'range' 		=> [
						'px' 		=> [
							'min' 	=> 0,
							'max' 	=> 1,
							'step'	=> 0.05,
						],
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'loop',
				[
					'type' 			=> Controls_Manager::SWITCHER,
					'label' 		=> __( 'Loop', 'elementor-extras' ),
					'default' 		=> '',
					'separator'		=> 'before',
					'frontend_available' 	=> true,
				]
			);

			$this->add_control(
				'autoheight',
				[
					'type' 			=> Controls_Manager::SWITCHER,
					'label' 		=> __( 'Auto Height', 'elementor-extras' ),
					'default' 		=> '',
					'frontend_available' 	=> true,
					'conditions'=> [
						'relation' 	=> 'or',
						'terms' 	=> [
							[
								'name' 		=> $this->get_control_id('slides_per_column'),
								'operator' 	=> '==',
								'value' 	=> '1',
							],
							[
								'name' 		=> $this->get_control_id('slides_per_column'),
								'operator' 	=> '==',
								'value' 	=> '',
							],
						]
					]
				]
			);

			$this->add_control(
				'slide_change_resize',
				[
					'type' 			=> Controls_Manager::SWITCHER,
					'label' 		=> __( 'Trigger Resize on Slide', 'elementor-extras' ),
					'description'	=> __( 'Some widgets inside post skins templates might require triggering a window resize event when changing slides to display correctly.', 'elementor-extras' ),
					'default' 		=> '',
					'frontend_available' => true,
					'condition' 	=> [
						'skin_source' => 'template',
					],
				]
			);

			$this->add_control(
				'arrows',
				[
					'label' 		=> __( 'Arrows', 'elementor-extras' ),
					'type' 			=> Controls_Manager::POPOVER_TOGGLE,
					'default' 		=> 'on',
					'label_on' 		=> __( 'On', 'elementor-extras' ),
					'label_off' 	=> __( 'Off', 'elementor-extras' ),
					'return_value' 	=> 'on',
					'frontend_available' => true,
				]
			);

			$this->parent->start_popover();

			$this->add_control(
				'arrows_placement',
				[
					'type' 			=> Controls_Manager::SELECT,
					'label' 		=> __( 'Placement', 'elementor-extras' ),
					'default'		=> 'inside',
					'options' 		=> [
						'inside' 	=> __( 'Inside', 'elementor-extras' ),
						'outside' 	=> __( 'Outside', 'elementor-extras' ),
					],
					'condition'		=> [
						$this->get_control_id('arrows!') => '',
					]
				]
			);

			$this->parent->end_popover();

			$this->add_control(
				'free_mode',
				[
					'type' 			=> Controls_Manager::POPOVER_TOGGLE,
					'label' 		=> __( 'Free Mode', 'elementor-extras' ),
					'description'	=> __( 'Disable fixed positions for slides.', 'elementor-extras' ),
					'default' 		=> '',
					'return_value' 	=> 'yes',
					'frontend_available' 	=> true,
				]
			);

			$this->parent->start_popover();

			$this->add_control(
				'free_mode_sticky',
				[
					'type' 			=> Controls_Manager::SWITCHER,
					'label' 		=> __( 'Snap to position', 'elementor-extras' ),
					'description'	=> __( 'Enable to snap slides to positions in free mode.', 'elementor-extras' ),
					'default' 		=> '',
					'return_value' 	=> 'yes',
					'frontend_available' 	=> true,
					'condition' => [
						$this->get_control_id( 'free_mode!' ) => '',
					],
				]
			);

			$this->add_control(
				'free_mode_momentum',
				[
					'type' 			=> Controls_Manager::SWITCHER,
					'label' 		=> __( 'Momentum', 'elementor-extras' ),
					'description'	=> __( 'Enable to keep slide moving for a while after you release it.', 'elementor-extras' ),
					'default' 		=> 'yes',
					'return_value' 	=> 'yes',
					'separator'		=> 'before',
					'frontend_available' => true,
					'condition' => [
						$this->get_control_id( 'free_mode!' ) => '',
					],
				]
			);

			$this->add_control(
				'free_mode_momentum_ratio',
				[
					'label' 		=> __( 'Ratio', 'elementor-extras' ),
					'description'	=> __( 'Higher value produces larger momentum distance after you release slider.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' 	=> 0,
							'max' 	=> 5,
							'step'	=> 0.1,
						],
					],
					'condition' => [
						$this->get_control_id( 'free_mode!' ) => '',
						$this->get_control_id( 'free_mode_momentum!' ) => '',
					],	
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'free_mode_momentum_velocity',
				[
					'label' 		=> __( 'Velocity', 'elementor-extras' ),
					'description'	=> __( 'Higher value produces larger momentum velocity after you release slider.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' 	=> 0,
							'max' 	=> 5,
							'step'	=> 0.1,
						],
					],
					'condition' => [
						$this->get_control_id( 'free_mode!' ) => '',
						$this->get_control_id( 'free_mode_momentum!' ) => '',
					],	
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'free_mode_momentum_bounce',
				[
					'type' 			=> Controls_Manager::SWITCHER,
					'label' 		=> __( 'Bounce', 'elementor-extras' ),
					'description'	=> __( 'Set to No if you want to disable momentum bounce in free mode.', 'elementor-extras' ),
					'default' 		=> 'yes',
					'return_value' 	=> 'yes',
					'frontend_available' => true,
					'condition' => [
						$this->get_control_id( 'free_mode!' ) => '',
						$this->get_control_id( 'free_mode_momentum!' ) => '',
					],
				]
			);

			$this->add_control(
				'free_mode_momentum_bounce_ratio',
				[
					'label' 		=> __( 'Bounce Ratio', 'elementor-extras' ),
					'description'	=> __( 'Higher value produces larger momentum bounce effect.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' 	=> 0,
							'max' 	=> 5,
							'step'	=> 0.1,
						],
					],
					'condition' => [
						$this->get_control_id( 'free_mode!' ) => '',
						$this->get_control_id( 'free_mode_momentum!' ) => '',
						$this->get_control_id( 'free_mode_momentum_bounce!' ) => '',
					],	
					'frontend_available' => true,
				]
			);

			$this->parent->end_popover();

			$this->add_control(
				'autoplay',
				[
					'label' 	=> __( 'Autoplay', 'elementor-extras' ),
					'type' 		=> Controls_Manager::POPOVER_TOGGLE,
					'default' 	=> '',
					'frontend_available' => true,
				]
			);

			$this->parent->start_popover();

			$this->add_control(
				'autoplay_speed',
				[
					'label' 	=> __( 'Autoplay Speed', 'elementor-extras' ),
					'type' 		=> Controls_Manager::NUMBER,
					'default' 	=> 5000,
					'condition' => [
						$this->get_control_id( 'autoplay!' ) => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'pause_on_interaction',
				[
					'label' 		=> __( 'Disable on Interaction', 'elementor-extras' ),
					'description' 	=> __( 'Removes autoplay completely on the first interaction with the carousel.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'condition' 	=> [
						$this->get_control_id( 'autoplay!' ) => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'stop_on_hover',
				[
					'label' 	=> __( 'Pause on Hover', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SWITCHER,
					'default' 	=> '',
					'frontend_available' => true,
					'condition'	=> [
						$this->get_control_id( 'autoplay!' ) => '',
					],
				]
			);

			$this->parent->end_popover();

			$this->add_control(
				'pagination',
				[
					'label' 		=> __( 'Pagination', 'elementor-extras' ),
					'type' 			=> Controls_Manager::POPOVER_TOGGLE,
					'default' 		=> 'on',
					'label_on' 		=> __( 'On', 'elementor-extras' ),
					'label_off' 	=> __( 'Off', 'elementor-extras' ),
					'return_value' 	=> 'on',
					'frontend_available' => true,
				]
			);

			$this->parent->start_popover();

			$this->add_control(
				'pagination_position',
				[
					'type' 			=> Controls_Manager::SELECT,
					'label' 		=> __( 'Position', 'elementor-extras' ),
					'default'		=> 'inside',
					'options' 		=> [
						'inside' 		=> __( 'Inside', 'elementor-extras' ),
						'outside' 		=> __( 'Outside', 'elementor-extras' ),
					],
					'frontend_available' 	=> true,
					'condition'		=> [
						$this->get_control_id( 'pagination!' ) => '',
					]
				]
			);

			$this->add_control(
				'pagination_type',
				[
					'type' 			=> Controls_Manager::SELECT,
					'label' 		=> __( 'Type', 'elementor-extras' ),
					'default'		=> 'bullets',
					'options' 		=> [
						'bullets' 		=> __( 'Bullets', 'elementor-extras' ),
						'fraction' 		=> __( 'Fraction', 'elementor-extras' ),
					],
					'condition'		=> [
						$this->get_control_id( 'pagination!' ) => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'pagination_clickable',
				[
					'type' 			=> Controls_Manager::SWITCHER,
					'label' 		=> __( 'Clickable', 'elementor-extras' ),
					'default' 		=> 'yes',
					'return_value' 	=> 'yes',
					'condition' => [
						$this->get_control_id( 'pagination!' ) => '',
						$this->get_control_id( 'pagination_type' ) => 'bullets',
					],
					'frontend_available' 	=> true,
				]
			);

			$this->parent->end_popover();

		$this->end_controls_section();

	}

	/**
	 * Register Carousel Style Controls
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function register_carousel_style_controls() {

		$this->start_controls_section(
			'section_style_carousel',
			[
				'label' => __( 'Carousel', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'conditions'=> [
					'relation' 	=> 'and',
					'terms' 	=> [
						[
							'name' => '_skin',
							'operator' => '==',
							'value' => 'carousel',
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' 		=> $this->get_control_id('arrows'),
									'operator' 	=> '!=',
									'value' 	=> '',
								],
								[
									'name' 		=> $this->get_control_id('pagination'),
									'operator' 	=> '!=',
									'value' 	=> '',
								],
							]
						]
					]
				]
			]
		);

			$this->add_control(
				'arrows_style_heading',
				[
					'label' 	=> __( 'Arrows', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'condition'		=> [
						$this->get_control_id('arrows!') => '',
					]
				]
			);

			$this->add_control(
				'arrows_position',
				[
					'type' 			=> Controls_Manager::SELECT,
					'label' 		=> __( 'Position', 'elementor-extras' ),
					'default'		=> 'middle',
					'options' 		=> [
						'top' 		=> __( 'Top', 'elementor-extras' ),
						'middle' 	=> __( 'Middle', 'elementor-extras' ),
						'bottom' 	=> __( 'Bottom', 'elementor-extras' ),
					],
					'condition'		=> [
						$this->get_control_id('arrows!') => '',
						$this->get_control_id('direction') => 'horizontal',
					]
				]
			);

			$this->add_control(
				'arrows_position_vertical',
				[
					'type' 			=> Controls_Manager::SELECT,
					'label' 		=> __( 'Position', 'elementor-extras' ),
					'default'		=> 'center',
					'options' 		=> [
						'left' 		=> __( 'Left', 'elementor-extras' ),
						'center' 	=> __( 'Center', 'elementor-extras' ),
						'right' 	=> __( 'Right', 'elementor-extras' ),
					],
					'condition'		=> [
						$this->get_control_id('arrows!') => '',
						$this->get_control_id('direction') => 'vertical',
					]
				]
			);

			$this->add_responsive_control(
				'arrows_size',
				[
					'label' 		=> __( 'Size', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 12,
							'max' => 48,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-swiper__button' => 'font-size: {{SIZE}}px;',
					],
					'condition'		=> [
						$this->get_control_id('arrows!') => '',
					]
				]
			);

			$this->add_responsive_control(
				'arrows_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' 	=> 0,
							'max' 	=> 1,
							'step'	=> 0.1,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-swiper__button' => 'padding: {{SIZE}}em;',
					],
					'condition'		=> [
						$this->get_control_id('arrows!') => '',
					]
				]
			);

			$this->add_responsive_control(
				'arrows_distance',
				[
					'label' 		=> __( 'Distance', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 200,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-swiper__navigation--inside.ee-swiper__navigation--middle.ee-arrows--horizontal .ee-swiper__button' => 'margin-left: {{SIZE}}px; margin-right: {{SIZE}}px;',
						'{{WRAPPER}} .ee-swiper__navigation--inside:not(.ee-swiper__navigation--middle).ee-arrows--horizontal .ee-swiper__button' => 'margin: {{SIZE}}px;',
						'{{WRAPPER}} .ee-swiper__navigation--outside.ee-arrows--horizontal .ee-swiper__button--prev' => 'left: -{{SIZE}}px;',
						'{{WRAPPER}} .ee-swiper__navigation--outside.ee-arrows--horizontal .ee-swiper__button--next' => 'right: -{{SIZE}}px;',

						'{{WRAPPER}} .ee-swiper__navigation--inside.ee-swiper__navigation--center.ee-arrows--vertical .ee-swiper__button' => 'margin-top: {{SIZE}}px; margin-bottom: {{SIZE}}px;',
						'{{WRAPPER}} .ee-swiper__navigation--inside:not(.ee-swiper__navigation--center).ee-arrows--vertical .ee-swiper__button' => 'margin: {{SIZE}}px;',
						'{{WRAPPER}} .ee-swiper__navigation--outside.ee-arrows--vertical .ee-swiper__button--prev' => 'top: -{{SIZE}}px;',
						'{{WRAPPER}} .ee-swiper__navigation--outside.ee-arrows--vertical .ee-swiper__button--next' => 'bottom: -{{SIZE}}px;',
					],
					'condition'		=> [
						$this->get_control_id('arrows!') => '',
					]
				]
			);

			$this->add_responsive_control(
				'arrows_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-swiper__button' => 'border-radius: {{SIZE}}%;',
					],
					'condition'		=> [
						$this->get_control_id('arrows!') => '',
					],
					'separator'		=> 'after',
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 			=> 'arrows',
					'selector' 		=> '{{WRAPPER}} .ee-swiper__button',
					'condition'		=> [
						$this->get_control_id('arrows!') => '',
					]
				]
			);

			$this->start_controls_tabs( 'arrows_tabs_hover' );

			$this->start_controls_tab( 'arrows_tab_default', [
				'label' => __( 'Default', 'elementor-extras' ),
				'condition'	=> [
					$this->get_control_id('arrows!') => '',
				]
			] );

				$this->add_control(
					'arrows_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-swiper__button i:before' => 'color: {{VALUE}};',
						],
						'condition'		=> [
							$this->get_control_id('arrows!') => '',
						]
					]
				);

				$this->add_control(
					'arrows_background_color',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-swiper__button' => 'background-color: {{VALUE}};',
						],
						'condition'		=> [
							$this->get_control_id('arrows!') => '',
						]
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'arrows_tab_hover', [
				'label' => __( 'Hover', 'elementor-extras' ),
				'condition'	=> [
					$this->get_control_id('arrows!') => '',
				]
			] );

				$this->add_control(
					'arrows_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-swiper__button:not(.ee-swiper__button--disabled):hover i:before' => 'color: {{VALUE}};',
						],
						'condition'		=> [
							$this->get_control_id('arrows!') => '',
						]
					]
				);

				$this->add_control(
					'arrows_background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-swiper__button:not(.ee-swiper__button--disabled):hover' => 'background-color: {{VALUE}};',
						],
						'condition'		=> [
							$this->get_control_id('arrows!') => '',
						]
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'arrows_tab_disabled', [
				'label' => __( 'Disabled', 'elementor-extras' ),
				'condition'	=> [
					$this->get_control_id('arrows!') => '',
				]
			] );

				$this->add_responsive_control(
					'arrows_opacity_disabled',
					[
						'label' 		=> __( 'Opacity', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SLIDER,
						'range' 		=> [
							'px' 		=> [
								'min' => 0,
								'max' => 1,
								'step'=> 0.05,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .ee-swiper__button--disabled' => 'opacity: {{SIZE}};',
						],
						'condition'		=> [
							$this->get_control_id('arrows!') => '',
						]
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'pagination_style_heading',
				[
					'separator'	=> 'before',
					'label' 	=> __( 'Pagination', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
					'condition'		=> [
						$this->get_control_id( 'pagination!' ) => '',
					]
				]
			);

			$this->add_responsive_control(
				'pagination_align',
				[
					'label' 		=> __( 'Align', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'center',
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
					'selectors'		=> [
						'{{WRAPPER}} .ee-swiper__pagination.ee-swiper__pagination--horizontal' => 'text-align: {{VALUE}};',
					],
					'condition'		=> [
						$this->get_control_id( 'pagination!' ) => '',
						$this->get_control_id( 'direction' ) => 'horizontal',
					]
				]
			);

			$this->add_responsive_control(
				'pagination_align_vertical',
				[
					'label' 		=> __( 'Align', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'middle',
					'options' 		=> [
						'flex-start'    => [
							'title' 	=> __( 'Top', 'elementor-extras' ),
							'icon' 		=> 'eicon-v-align-top',
						],
						'center' 		=> [
							'title' 	=> __( 'Center', 'elementor-extras' ),
							'icon' 		=> 'eicon-v-align-middle',
						],
						'flex-end' 		=> [
							'title' 	=> __( 'Right', 'elementor-extras' ),
							'icon' 		=> 'eicon-v-align-bottom',
						],
					],
					'selectors'		=> [
						'{{WRAPPER}} .ee-swiper__pagination.ee-swiper__pagination--vertical' => 'justify-content: {{VALUE}};',
					],
					'condition'		=> [
						$this->get_control_id( 'pagination!' ) => '',
						$this->get_control_id( 'direction' ) => 'vertical',
					]
				]
			);

			$this->add_responsive_control(
				'pagination_distance',
				[
					'label' 		=> __( 'Distance', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-swiper__pagination--inside.ee-swiper__pagination--horizontal' => 'padding: 0 {{SIZE}}px {{SIZE}}px {{SIZE}}px;',
						'{{WRAPPER}} .ee-swiper__pagination--outside.ee-swiper__pagination--horizontal' => 'padding: {{SIZE}}px 0 0 0;',
						'{{WRAPPER}} .ee-swiper__pagination--inside.ee-swiper__pagination--vertical' => 'padding: {{SIZE}}px {{SIZE}}px {{SIZE}}px 0;',
						'{{WRAPPER}} .ee-swiper__pagination--outside.ee-swiper__pagination--vertical' => 'padding: 0 0 0 {{SIZE}}px;',
					],
					'condition'		=> [
						$this->get_control_id('pagination!') => '',
					]
				]
			);

			$this->add_responsive_control(
				'pagination_bullets_spacing',
				[
					'label' 		=> __( 'Spacing', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 20,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-swiper__pagination--horizontal .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}px',
						'{{WRAPPER}} .ee-swiper__pagination--vertical .swiper-pagination-bullet' => 'margin: {{SIZE}}px 0',
					],
					'condition'		=> [
						$this->get_control_id( 'pagination!' ) => '',
						$this->get_control_id( 'pagination_type' ) => 'bullets',
					]
				]
			);

			$this->add_responsive_control(
				'pagination_bullets_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .swiper-pagination-bullet' => 'border-radius: {{SIZE}}px;',
					],
					'condition'		=> [
						$this->get_control_id( 'pagination!' ) => '',
						$this->get_control_id( 'pagination_type' ) => 'bullets',
					],
					'separator'		=> 'after',
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 			=> 'pagination',
					'selector' 		=> '{{WRAPPER}} .swiper-pagination-bullet',
					'condition'		=> [
						$this->get_control_id( 'pagination!' ) => '',
					]
				]
			);

			$this->start_controls_tabs( 'pagination_bullets_tabs_hover' );

			$this->start_controls_tab( 'pagination_bullets_tab_default', [
				'label' 		=> __( 'Default', 'elementor-extras' ),
				'condition'		=> [
					$this->get_control_id( 'pagination!' ) => '',
					$this->get_control_id( 'pagination_type' ) => 'bullets',
				]
			] );

				$this->add_responsive_control(
					'pagination_bullets_size',
					[
						'label' 		=> __( 'Size', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SLIDER,
						'range' 		=> [
							'px' 		=> [
								'min' => 0,
								'max' => 12,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .swiper-pagination-bullet' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
						],
						'condition'		=> [
							$this->get_control_id( 'pagination!' ) => '',
							$this->get_control_id( 'pagination_type' ) => 'bullets',
						]
					]
				);

				$this->add_control(
					'pagination_bullets_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
						],
						'condition'		=> [
							$this->get_control_id( 'pagination!' ) => '',
							$this->get_control_id( 'pagination_type' ) => 'bullets',
						]
					]
				);

				$this->add_responsive_control(
					'pagination_bullets_opacity',
					[
						'label' 		=> __( 'Opacity', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SLIDER,
						'range' 		=> [
							'px' 		=> [
								'min' => 0,
								'max' => 1,
								'step'=> 0.05,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .swiper-pagination-bullet' => 'opacity: {{SIZE}};',
						],
						'condition'		=> [
							$this->get_control_id( 'pagination!' ) => '',
							$this->get_control_id( 'pagination_type' ) => 'bullets',
						]
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'pagination_bullets_tab_hover',[
				'label' 		=> __( 'Hover', 'elementor-extras' ),
				'condition'		=> [
					$this->get_control_id( 'pagination!' ) => '',
					$this->get_control_id( 'pagination_type' ) => 'bullets',
				]
			] );

				$this->add_responsive_control(
					'pagination_bullets_size_hover',
					[
						'label' 		=> __( 'Size', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SLIDER,
						'range' 		=> [
							'px' 		=> [
								'min' => 1,
								'max' => 1.5,
								'step'=> 0.1,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .swiper-pagination-bullet:hover' => 'transform: scale({{SIZE}});',
						],
						'condition'		=> [
							$this->get_control_id( 'pagination!' ) => '',
							$this->get_control_id( 'pagination_type' ) => 'bullets',
						]
					]
				);

				$this->add_control(
					'pagination_bullets_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .swiper-pagination-bullet:hover' => 'background-color: {{VALUE}};',
						],
						'condition'		=> [
							$this->get_control_id( 'pagination!' ) => '',
							$this->get_control_id( 'pagination_type' ) => 'bullets',
						]
					]
				);

				$this->add_responsive_control(
					'pagination_bullets_opacity_hover',
					[
						'label' 		=> __( 'Opacity', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SLIDER,
						'range' 		=> [
							'px' 		=> [
								'min' => 0,
								'max' => 1,
								'step'=> 0.05,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .swiper-pagination-bullet:hover' => 'opacity: {{SIZE}};',
						],
						'condition'		=> [
							$this->get_control_id( 'pagination!' ) => '',
							$this->get_control_id( 'pagination_type' ) => 'bullets',
						]
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'pagination_bullets_tab_active', [
				'label' => __( 'Active', 'elementor-extras' ),
				'condition'	=> [
					$this->get_control_id( 'pagination!' ) => '',
					$this->get_control_id( 'pagination_type' ) => 'bullets',
				]
			] );

				$this->add_responsive_control(
					'pagination_bullets_size_active',
					[
						'label' 		=> __( 'Size', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SLIDER,
						'range' 		=> [
							'px' 		=> [
								'min' => 1,
								'max' => 1.5,
								'step'=> 0.1,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .swiper-pagination-bullet-active' => 'transform: scale({{SIZE}});',
						],
						'condition'		=> [
							$this->get_control_id( 'pagination!' ) => '',
							$this->get_control_id( 'pagination_type' ) => 'bullets',
						]
					]
				);

				$this->add_control(
					'pagination_bullets_color_active',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
						],
						'condition'		=> [
							$this->get_control_id( 'pagination!' ) => '',
							$this->get_control_id( 'pagination_type' ) => 'bullets',
						]
					]
				);

				$this->add_responsive_control(
					'pagination_bullets_opacity_active',
					[
						'label' 		=> __( 'Opacity', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SLIDER,
						'range' 		=> [
							'px' 		=> [
								'min' => 0,
								'max' => 1,
								'step'=> 0.05,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .swiper-pagination-bullet-active' => 'opacity: {{SIZE}};',
						],
						'condition'		=> [
							$this->get_control_id( 'pagination!' ) => '',
							$this->get_control_id( 'pagination_type' ) => 'bullets',
						]
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

	}

	/**
	 * Before Loop
	 *
	 * Executes before the loop is started
	 *
	 * @since  2.2.28
	 * @return void
	 */
	public function before_loop() {
		parent::before_loop();

		add_filter( 'elementor_extras/widgets/posts/item_classes', [ $this, 'filter_item_classes' ], 10, 3 );
	}

	/**
	 * Before Loop
	 *
	 * Executes after the loop has ended
	 *
	 * @since  2.2.28
	 * @return void
	 */
	public function after_loop() {
		remove_filter( 'elementor_extras/widgets/posts/item_classes', [ $this, 'filter_item_classes' ] );

		parent::after_loop();
	}

	/**
	 * Filter for item classes
	 *
	 * @since  2.2.28
	 * @return array
	 */
	public function filter_item_classes( $classes, $post, $settings ) {
		$classes[] = 'ee-swiper__slide';
		$classes[] = 'swiper-slide';

		return $classes;
	}

	/**
	 * Render Loop Start
	 * 
	 * Function to render markup before the posts loop starts
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_loop_start() {
		$settings = $this->parent->get_settings();

		$this->parent->add_render_attribute( [
			'metas-separator' => [
				'class' => 'ee-post__meta__separator',
			],
			'terms-separator' => [
				'class' => [
					'ee-post__terms__separator',
				],
			],
			'swiper' => [
				'class' => [
					'ee-swiper',
				],
			],
			'swiper-container' => [
				'class' => [
					'ee-swiper__container',
					'swiper-container',
				],
			],
			'swiper-wrapper' => [
				'class' => [
					'ee-grid',
					'ee-swiper__wrapper',
					'swiper-wrapper',
				],
			],
		] );

		?>
		<div <?php echo $this->parent->get_render_attribute_string( 'swiper' ); ?>>
			<div <?php echo $this->parent->get_render_attribute_string( 'swiper-container' ); ?>>
				<div <?php echo $this->parent->get_render_attribute_string( 'swiper-wrapper' ); ?>>
		<?php
	}

	/**
	 * Render Loop End
	 *
	 * Outputs the markup for the end of the loop
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_loop_end() {
				?></div><!-- .ee-swiper__wrapper -->
				<?php
					
					// if ( 'outside' !== $this->parent->get_settings( $this->get_control_id( 'arrows_placement' ) ) )
					// 	$this->render_swiper_navigation();

					if ( 'outside' !== $this->parent->get_settings( $this->get_control_id( 'pagination_position' ) ) )
						$this->render_swiper_pagination();

				?>
			</div><!-- .ee-swiper__container -->
		</div><!-- .ee-swiper --><?php

		// if ( 'outside' === $this->parent->get_settings( $this->get_control_id( 'arrows_placement' ) ) ) {
			$this->render_swiper_navigation();
		// }

		if ( 'outside' === $this->parent->get_settings( $this->get_control_id( 'pagination_position' ) ) ) {
			$this->render_swiper_pagination();
		}
	}

	/**
	 * Render Swiper Navigation
	 *
	 * Outputs markup for the swiper navigation
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_swiper_navigation() {
		$this->parent->add_render_attribute( [
			'navigation' => [
				'class' => [
					'ee-arrows',
					'ee-arrows--' . $this->parent->get_settings( $this->get_control_id( 'direction' ) ),
					'ee-swiper__navigation',
					'ee-swiper__navigation--' . $this->parent->get_settings( $this->get_control_id( 'arrows_placement' ) ),
					'ee-swiper__navigation--' . $this->parent->get_settings( $this->get_control_id( 'arrows_position' ) ),
					'ee-swiper__navigation--' . $this->parent->get_settings( $this->get_control_id( 'arrows_position_vertical' ) ),
				],
			],
		] );

		// if ( '' !== $settings[ $this->get_control_id('arrows') ] ) {
		// 	$this->parent->add_render_attribute( 'swiper', [
		// 		'class' => [
		// 				'ee-swiper-arrows-placement--' . $settings[ $this->get_control_id('arrows_placement') ],
		// 				'ee-swiper-arrows-position--' . $settings[ $this->get_control_id('arrows_position') ],
		// 			],
		// 		],
		// 	);
		// }

		?><div <?php echo $this->parent->get_render_attribute_string( 'navigation' ); ?>><?php
			$this->render_swiper_arrows();
		?></div><?php
	}

	/**
	 * Render Swiper Pagination
	 *
	 * Outputs markup for the swiper bullets pagination
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function render_swiper_pagination() {
		if ( '' === $this->parent->get_settings( $this->get_control_id( 'pagination' ) ) )
			return;

		$this->parent->add_render_attribute( 'pagination', 'class', [
			'ee-swiper__pagination',
			'ee-swiper__pagination--' . $this->parent->get_settings( $this->get_control_id( 'direction' ) ),
			'ee-swiper__pagination--' . $this->parent->get_settings( $this->get_control_id( 'pagination_position' ) ),
			'ee-swiper__pagination-' . $this->parent->get_id(),
			'swiper-pagination',
		] );

		?><div <?php echo $this->parent->get_render_attribute_string( 'pagination' ); ?>></div><?php
	}

	/**
	 * Render Swiper Arrows
	 *
	 * Outputs markup for the swiper arrows navigation
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_swiper_arrows() {
		if ( '' === $this->parent->get_settings( $this->get_control_id( 'arrows' ) ) )
			return;

		$prev = is_rtl() ? 'right' : 'left';
		$next = is_rtl() ? 'left' : 'right';

		$this->parent->add_render_attribute( [
			'button-prev' => [
				'class' => [
					'ee-swiper__button',
					'ee-swiper__button--prev',
					'ee-arrow',
					'ee-arrow--prev',
					'ee-swiper__button--prev-' . $this->parent->get_id(),
				],
			],
			'button-prev-icon' => [
				'class' => 'eicon-chevron-' . $prev,
			],
			'button-next' => [
				'class' => [
					'ee-swiper__button',
					'ee-swiper__button--next',
					'ee-arrow',
					'ee-arrow--next',
					'ee-swiper__button--next-' . $this->parent->get_id(),
				],
			],
			'button-next-icon' => [
				'class' => 'eicon-chevron-' . $next,
			],
		] );

		?><div <?php echo $this->parent->get_render_attribute_string( 'button-prev' ); ?>>
			<i <?php echo $this->parent->get_render_attribute_string( 'button-prev-icon' ); ?>></i>
		</div>
		<div <?php echo $this->parent->get_render_attribute_string( 'button-next' ); ?>>
			<i <?php echo $this->parent->get_render_attribute_string( 'button-next-icon' ); ?>></i>
		</div><?php
	}

	/**
	 * Render Sizer
	 * 
	 * Stop rendering markup for masonry sizer
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_sizer() {}
}