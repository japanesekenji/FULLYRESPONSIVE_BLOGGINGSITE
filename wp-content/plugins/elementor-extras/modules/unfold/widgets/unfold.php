<?php
namespace ElementorExtras\Modules\Unfold\Widgets;

// Extras for Elementor Classes
use ElementorExtras\Utils;
use ElementorExtras\Base\Extras_Widget;

// Elementor Classes
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Unfold
 *
 * @since 1.2.0
 */
class Unfold extends Extras_Widget {

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  1.2.0
	 * @return string
	 */
	public function get_name() {
		return 'unfold';
	}

	/**
	 * Get Title
	 * 
	 * Get the title of the widget
	 *
	 * @since  1.2.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Unfold', 'elementor-extras' );
	}

	/**
	 * Get Icon
	 * 
	 * Get the icon of the widget
	 *
	 * @since  1.2.0
	 * @return string
	 */
	public function get_icon() {
		return 'nicon nicon-unfold';
	}

	/**
	 * Get Script Depends
	 * 
	 * A list of scripts that the widgets is depended in
	 *
	 * @since  1.2.0
	 * @return array
	 */
	public function get_script_depends() {
		return [
			'unfold',
			'gsap-js',
			'jquery-visible',
		];
	}

	/**
	 * Register Widget Controls
	 *
	 * @since  1.2.0
	 * @return void
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'elementor-extras' ),
			]
		);

			$this->add_control(
				'content',
				[
					'label' 	=> '',
					'type' 		=> Controls_Manager::WYSIWYG,
					'dynamic' 	=> [ 'active' => true ],
					'default' 	=> __( 'A Cultural Response to Cimate Change profiles the work of the artists in the Unfold exhibition and also proposes a number of creative and innovative responses to climate change aimed at stimulating discourse and a wider engagement with the climate debate. The texts by Gerald Bast, Steve Kapelke, Chris Rapley, David Buckland, Chris Wainwright and Helga Kromp-Kolb provoke, within an educational context, a discussion around what are the legitimate agendas for arts education and arts practitioners, in relation to some of the most pressing and urgent issues of our times.', 'elementor-extras' ),
				]
			);

			$this->add_responsive_control(
				'text_align',
				[
					'label' 	=> __( 'Text Align', 'elementor-extras' ),
					'type' 		=> Controls_Manager::CHOOSE,
					'options' 	=> [
						'left'    	=> [
							'title' 	=> __( 'Left', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-left',
						],
						'center' 	=> [
							'title' 	=> __( 'Center', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-center',
						],
						'right' 	=> [
							'title' 	=> __( 'Right', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-right',
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-unfold__content' => 'text-align: {{VALUE}}',
					],
					'default' 		=> '',
				]
			);

			$this->add_control(
				'visible_type',
				[
					'label' 	=> __( 'Visible', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '',
					'options' 	=> [
						'' 		=> __( 'Percentage', 'elementor-extras' ),
						'lines' => __( 'Lines', 'elementor-extras' ),
					],
					'frontend_available' => 'true'
				]
			);

			$this->add_control(
				'visible_percentage',
				[
					'label' 	=> __( 'Visible Amount (%)', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'dynamic' 	=> [ 'active' => true ],
					'default'	=> [
						'size' 	=> 50,
					],
					'range' 	=> [
						'px' 	=> [
							'max' => 100,
							'min' => 10,
						],
					],
					'condition' => [
						'visible_type' => ''
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'visible_lines',
				[
					'label' 	=> __( 'Visible Amount (lines)', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'dynamic' 	=> [ 'active' => true ],
					'default'	=> [
						'size' 	=> 3,
					],
					'range' 	=> [
						'px' 	=> [
							'max' => 50,
							'min' => 1,
						],
					],
					'condition' => [
						'visible_type' => 'lines'
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'content_valid_warning',
				[
					'type' 				=> Controls_Manager::RAW_HTML,
					'raw' 				=> __( 'Make sure your WYSIWYG content is valid HTML (no unclosed tags) in order for the widget to calculate the number of lines shown correctly.', 'elementor-extras' ),
					'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-warning',
					'condition'			=> [
						'visible_type' => 'lines'
					]
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_settings',
			[
				'label' => __( 'Settings', 'elementor-extras' ),
			]
		);

			$this->start_controls_tabs( 'tabs_folds' );

			$this->start_controls_tab(
				'tab_unfold',
				[
					'label' => __( 'Unfold', 'elementor-extras' ),
				]
			);

				$this->add_control(
					'duration_unfold',
					[
						'label' 	=> __( 'Duration', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SLIDER,
						'dynamic' 	=> [ 'active' => true ],
						'default'	=> [
							'size' 	=> 0.5,
						],
						'range' 	=> [
							'px' 	=> [
								'max' => 2,
								'min' => 0.1,
								'step'=> 0.1,
							],
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'animation_unfold',
					[
						'label'		=> __( 'Animation', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SELECT,
						'default' 	=> 'Power4',
						'options' 	=> [
							'Power0' 		=> __( 'Linear', 'elementor-extras' ),
							'Power4' 		=> __( 'Break', 'elementor-extras' ),
							'Back' 			=> __( 'Back', 'elementor-extras' ),
							'Elastic' 		=> __( 'Elastic', 'elementor-extras' ),
							'Bounce' 		=> __( 'Bounce', 'elementor-extras' ),
							'SlowMo' 		=> __( 'SlowMo', 'elementor-extras' ),
							'SteppedEase' 	=> __( 'Step', 'elementor-extras' ),
						],
						'frontend_available' => true
					]
				);

				$this->add_control(
					'easing_unfold',
					[
						'label'		=> __( 'Easing', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SELECT,
						'default' 	=> 'easeInOut',
						'options' 	=> [
							'easeInOut' 			=> __( 'Ease In Out', 'elementor-extras' ),
							'easeIn' 				=> __( 'Ease In', 'elementor-extras' ),
							'easeOut' 				=> __( 'Ease Out', 'elementor-extras' ),
						],
						'condition' => [
							'animation_unfold!' => [ 'SlowMo', 'SteppedEase' ]
						],
						'frontend_available' => true
					]
				);

				$this->add_control(
					'steps_unfold',
					[
						'label' 	=> __( 'Steps', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SLIDER,
						'dynamic' 	=> [ 'active' => true ],
						'default'	=> [
							'size' 	=> 10,
						],
						'range' 	=> [
							'px' 	=> [
								'max' => 20,
								'min' => 5,
							],
						],
						'condition' => [
							'animation_unfold' => 'SteppedEase'
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'slow_unfold',
					[
						'label' 	=> __( 'Slow Amount', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SLIDER,
						'dynamic' 	=> [ 'active' => true ],
						'default'	=> [
							'size' 	=> 0.7,
						],
						'range' 	=> [
							'px' 	=> [
								'max' => 1,
								'min' => 0.1,
								'step'=> 0.1,
							],
						],
						'condition' => [
							'animation_unfold' => 'SlowMo'
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'focus_open',
					[
						'label' 		=> __( 'Keep Focus', 'elementor-extras' ),
						'description'	=> __( 'When unfolding, keep focus on top of content or the scroll position at the time of starting the unfold.', 'elementor-extras' ),
						'options'		=> [
							''			=> __( 'Default', 'elementor-extras' ),
							'top'		=> __( 'Top of Content', 'elementor-extras' ),
							'scroll'	=> __( 'Scroll Position', 'elementor-extras' ),
						],
						'type' 			=> Controls_Manager::SELECT,
						'default' 		=> '',
						'frontend_available' => true,
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_fold',
				[
					'label' => __( 'Fold', 'elementor-extras' ),
				]
			);

				$this->add_control(
					'duration_fold',
					[
						'label' 	=> __( 'Duration', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SLIDER,
						'dynamic' 	=> [ 'active' => true ],
						'default'	=> [
							'size' 	=> 0.5,
						],
						'range' 	=> [
							'px' 	=> [
								'max' => 2,
								'min' => 0.1,
								'step'=> 0.1,
							],
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'animation_fold',
					[
						'label'		=> __( 'Animation', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SELECT,
						'default' 	=> 'Power4',
						'options' 	=> [
							'Power0' 		=> __( 'Linear', 'elementor-extras' ),
							'Power4' 		=> __( 'Break', 'elementor-extras' ),
							'Back' 			=> __( 'Back', 'elementor-extras' ),
							'Elastic' 		=> __( 'Elastic', 'elementor-extras' ),
							'Bounce' 		=> __( 'Bounce', 'elementor-extras' ),
							'SlowMo' 		=> __( 'SlowMo', 'elementor-extras' ),
							'SteppedEase' 	=> __( 'Step', 'elementor-extras' ),
						],
						'frontend_available' => true
					]
				);

				$this->add_control(
					'easing_fold',
					[
						'label'		=> __( 'Easing', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SELECT,
						'default' 	=> 'easeInOut',
						'options' 	=> [
							'easeInOut' 			=> __( 'Ease In Out', 'elementor-extras' ),
							'easeIn' 				=> __( 'Ease In', 'elementor-extras' ),
							'easeOut' 				=> __( 'Ease Out', 'elementor-extras' ),
						],
						'condition' => [
							'animation_fold!' => [ 'SlowMo', 'SteppedEase' ]
						],
						'frontend_available' => true
					]
				);

				$this->add_control(
					'steps_fold',
					[
						'label' 	=> __( 'Steps', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SLIDER,
						'dynamic' 	=> [ 'active' => true ],
						'default'	=> [
							'size' 	=> 10,
						],
						'range' 	=> [
							'px' 	=> [
								'max' => 20,
								'min' => 5,
							],
						],
						'condition' => [
							'animation_fold' => 'SteppedEase'
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'slow_fold',
					[
						'label' 	=> __( 'Slow Amount', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SLIDER,
						'dynamic' 	=> [ 'active' => true ],
						'default'	=> [
							'size' 	=> 0.7,
						],
						'range' 	=> [
							'px' 	=> [
								'max' => 1,
								'min' => 0.1,
								'step'=> 0.1,
							],
						],
						'condition' => [
							'animation_fold' => 'SlowMo'
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'focus_close',
					[
						'label' 		=> __( 'Keep Focus', 'elementor-extras' ),
						'description'	=> __( 'When folding, keep focus on content', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SWITCHER,
						'default' 		=> '',
						'return_value' 	=> 'yes',
						'frontend_available' => true,
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_separator_content',
			[
				'label' => __( 'Separator', 'elementor-extras' ),
			]
		);

			$this->add_control(
				'separator',
				[
					'label' 		=> __( 'Hide Separator', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'return_value' 	=> 'yes',
					'selectors' 	=> [
						"{{WRAPPER}} .ee-unfold__separator" => 'display: none',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_trigger',
			[
				'label' => __( 'Button', 'elementor-extras' ),
			]
		);

			$this->start_controls_tabs( 'tabs_trigger_content' );

			$this->start_controls_tab(
				'tab_trigger_closed', [ 'label' => __( 'Folded', 'elementor-extras' ), ]
			);

				$this->add_control(
					'text_closed',
					[
						'label' 		=> __( 'Label', 'elementor-extras' ),
						'type' 			=> Controls_Manager::TEXT,
						'dynamic' 		=> [ 'active' => true ],
						'default' 		=> __( 'Read more', 'elementor-extras' ),
						'placeholder' 	=> __( 'Read more', 'elementor-extras' ),
					]
				);

				$this->add_control(
					'selected_icon',
					[
						'label' => __( 'Icon', 'elementor-extras' ),
						'type' => Controls_Manager::ICONS,
						'fa4compatibility' => 'icon',
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_trigger_open', [ 'label' => __( 'Unfolded', 'elementor-extras' ), ]
			);

				$this->add_control(
					'text_open',
					[
						'label' 		=> __( 'Label', 'elementor-extras' ),
						'type' 			=> Controls_Manager::TEXT,
						'dynamic' 		=> [ 'active' => true ],
						'default' 		=> __( 'Read less', 'elementor-extras' ),
						'placeholder' 	=> __( 'Read less', 'elementor-extras' ),
					]
				);

				$this->add_control(
					'selected_icon_open',
					[
						'label' => __( 'Icon', 'elementor-extras' ),
						'type' => Controls_Manager::ICONS,
						'label_block' => true,
						'fa4compatibility' => 'icon_open',
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_responsive_control(
				'align',
				[
					'label' 	=> __( 'Alignment', 'elementor-extras' ),
					'type' 		=> Controls_Manager::CHOOSE,
					'options' 	=> [
						'left'    	=> [
							'title' 	=> __( 'Left', 'elementor-extras' ),
							'icon' 		=> 'eicon-h-align-left',
						],
						'center' 	=> [
							'title' 	=> __( 'Center', 'elementor-extras' ),
							'icon' 		=> 'eicon-h-align-center',
						],
						'right' 	=> [
							'title' 	=> __( 'Right', 'elementor-extras' ),
							'icon' 		=> 'eicon-h-align-right',
						],
						'justify' 	=> [
							'title' 	=> __( 'Stretch', 'elementor-extras' ),
							'icon' 		=> 'eicon-h-align-stretch',
						],
					],
					'prefix_class' 	=> 'ee-trigger%s-align--',
					'default' 		=> '',
					'separator'		=> 'before',
				]
			);

			$this->add_control(
				'size',
				[
					'label' 	=> __( 'Size', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'sm',
					'options' 	=> Utils::get_button_sizes(),
				]
			);

			$this->add_control(
				'icon_align',
				[
					'label' 	=> __( 'Icon Position', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'left',
					'options' 	=> [
						'left' 		=> __( 'Before', 'elementor-extras' ),
						'right' 	=> __( 'After', 'elementor-extras' ),
					],
					'conditions'=> [
						'relation' => 'or',
						'terms' => [
							[
								'name' => 'selected_icon[value]',
								'operator' => '!==',
								'value' => '',
							],
							[
								'name' => 'selected_icon_open[value]',
								'operator' => '!==',
								'value' => '',
							],
						]
					],
				]
			);

			$this->add_control(
				'icon_indent',
				[
					'label' 	=> __( 'Icon Spacing', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'dynamic' 	=> [ 'active' => true ],
					'range' 	=> [
						'px' 	=> [
							'max' => 50,
						],
					],
					'condition' => [
						'icon!' => '',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-button .ee-icon--right' => 'margin-left: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ee-button .ee-icon--left' => 'margin-right: {{SIZE}}{{UNIT}};',
					],
					'conditions'=> [
						'relation' => 'or',
						'terms' => [
							[
								'name' => 'selected_icon[value]',
								'operator' => '!==',
								'value' => '',
							],
							[
								'name' => 'selected_icon_open[value]',
								'operator' => '!==',
								'value' => '',
							],
						]
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Content', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$text_columns = range( 1, 10 );
			$text_columns = array_combine( $text_columns, $text_columns );
			$text_columns[''] = __( 'Default', 'elementor-extras' );

			$this->add_responsive_control(
				'text_columns',
				[
					'label' => __( 'Columns', 'elementor-extras' ),
					'type' => Controls_Manager::SELECT,
					'separator' => 'before',
					'options' => $text_columns,
					'selectors' => [
						'{{WRAPPER}} .ee-unfold__content' => 'columns: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'column_gap',
				[
					'label' => __( 'Columns Gap', 'elementor-extras' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%', 'em', 'vw' ],
					'range' => [
						'px' => [
							'max' => 100,
						],
						'%' => [
							'max' => 10,
							'step' => 0.1,
						],
						'vw' => [
							'max' => 10,
							'step' => 0.1,
						],
						'em' => [
							'max' => 10,
							'step' => 0.1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-unfold__content' => 'column-gap: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'content_color',
				[
					'label' 	=> __( 'Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ee-unfold__content' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'content_background',
				[
					'label' 	=> __( 'Background Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ee-unfold__content' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'content_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-unfold__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'content_typography',
					'selector' 	=> '{{WRAPPER}} .ee-unfold__content',
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_separator_style',
			[
				'label' 	=> __( 'Separator', 'elementor-extras' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'separator!' => 'yes'
				]
			]
		);

			$this->add_control(
				'separator_height',
				[
					'label' 	=> __( 'Height', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'dynamic' 	=> [ 'active' => true ],
					'default'	=> [
						'size' 	=> 48,
					],
					'range' 	=> [
						'px' 	=> [
							'max' => 100,
							'min' => 0,
						],
						'%' 	=> [
							'max' => 100,
							'min' => 0,
						],
					],
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-unfold__separator' => 'height: {{SIZE}}{{UNIT}}'
					],
					'condition' => [
						'separator!' => 'yes'
					]
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' 		=> 'gradient',
					'types' 	=> [ 'gradient', 'classic' ],
					'selector' 	=> '{{WRAPPER}} .ee-unfold__separator',
					'default'	=> 'gradient',
					'condition' => [
						'separator!' => 'yes'
					]
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_trigger_style',
			[
				'label' => __( 'Button', 'elementor-extras' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'trigger_distance',
				[
					'label' 	=> __( 'Distance', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'dynamic' 	=> [ 'active' => true ],
					'default'	=> [
						'size' 	=> 24,
					],
					'range' 	=> [
						'px' 	=> [
							'max' => 96,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-unfold__trigger' => 'margin-top: {{SIZE}}px',
					]
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'typography',
					'label' => __( 'Typography', 'elementor-extras' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' => '{{WRAPPER}} a.ee-button, {{WRAPPER}} .ee-button',
				]
			);

			$this->start_controls_tabs( 'tabs_button_style' );

			$this->start_controls_tab(
				'tab_button_normal',
				[
					'label' => __( 'Normal', 'elementor-extras' ),
				]
			);

			$this->add_control(
				'button_text_color',
				[
					'label' => __( 'Text Color', 'elementor-extras' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} a.ee-button, {{WRAPPER}} .ee-button' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'background_color',
				[
					'label' => __( 'Background Color', 'elementor-extras' ),
					'type' => Controls_Manager::COLOR,
					'global' => [
						'default' => Global_Colors::COLOR_SECONDARY,
					],
					'selectors' => [
						'{{WRAPPER}} a.ee-button, {{WRAPPER}} .ee-button' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_button_hover',
				[
					'label' => __( 'Hover', 'elementor-extras' ),
				]
			);

			$this->add_control(
				'hover_color',
				[
					'label' => __( 'Text Color', 'elementor-extras' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} a.ee-button:hover, {{WRAPPER}} .ee-button:hover' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'button_background_hover_color',
				[
					'label' => __( 'Background Color', 'elementor-extras' ),
					'type' => Controls_Manager::COLOR,					
					'selectors' => [
						'{{WRAPPER}} a.ee-button:hover, {{WRAPPER}} .ee-button:hover' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'button_hover_border_color',
				[
					'label' => __( 'Border Color', 'elementor-extras' ),
					'type' => Controls_Manager::COLOR,
					'condition' => [
						'border_border!' => '',
					],
					'selectors' => [
						'{{WRAPPER}} a.ee-button:hover, {{WRAPPER}} .ee-button:hover' => 'border-color: {{VALUE}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'border',
					'label' => __( 'Border', 'elementor-extras' ),
					'placeholder' => '1px',
					'default' => '1px',
					'selector' => '{{WRAPPER}} .ee-button',
					'separator' => 'before',
				]
			);

			$this->add_control(
				'border_radius',
				[
					'label' => __( 'Border Radius', 'elementor-extras' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} a.ee-button, {{WRAPPER}} .ee-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'button_box_shadow',
					'selector' => '{{WRAPPER}} .ee-button',
				]
			);

			$this->add_control(
				'text_padding',
				[
					'label' => __( 'Text Padding', 'elementor-extras' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} a.ee-button, {{WRAPPER}} .ee-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator' => 'before',
				]
			);

		$this->end_controls_section();
		
	}

	/**
	 * Render
	 * 
	 * Render widget contents on frontend
	 *
	 * @since  1.2.0
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['content'] ) ) {
			return;
		}

		$this->add_render_attribute( [
			'wrapper' => [
				'class' => 'ee-unfold',
			],
			'mask' => [
				'class' => 'ee-unfold__mask',
			],
			'button' => [
				'class' => [
					'ee-button',
				],
			],
			'button-wrapper' => [
				'class' => 'ee-button-wrapper',
			],
			'separator' => [
				'class' => 'ee-unfold__separator',
			],
			'content' => [
				'class' => 'ee-unfold__content',
			],
			'trigger' => [
				'class' => 'ee-unfold__trigger',
			],
		] );

		$this->add_inline_editing_attributes( 'content', 'advanced' );

		if ( ! empty( $settings['size'] ) ) {
			$this->add_render_attribute( 'button', 'class', 'ee-size-' . $settings['size'] );
		}

		?><div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'mask' ); ?>>
				<div <?php echo $this->get_render_attribute_string( 'content' ); ?>>
					<?php echo $this->parse_text_editor( $settings['content'] ); ?>
				</div>
				<div <?php echo $this->get_render_attribute_string( 'separator' ); ?>></div>
			</div>
			<div <?php echo $this->get_render_attribute_string( 'trigger' ); ?>>
				<span <?php echo $this->get_render_attribute_string( 'button-wrapper' ); ?>>
					<span <?php echo $this->get_render_attribute_string( 'button' ); ?>>
						<?php $this->render_text(); ?>
					</span>
				</span>
			</div>
		</div><?php
	}

	/**
	 * Render text
	 * 
	 * Renders the markup for content of the unfold
	 *
	 * @since  1.2.0
	 * @return void
	 */
	protected function render_text() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( [
			'content-wrapper' => [
				'class' => 'ee-button-content-wrapper'
			],
			'text' => [
				'class' => 'ee-button-text',
				'data-close-label' 	=> $settings['text_open'],
				'data-open-label' 	=> $settings['text_closed'],
			],
		] );

		?><span <?php echo $this->get_render_attribute_string( 'content-wrapper' ); ?>><?php

				$this->render_icon( 'icon', 'closed' );
				$this->render_icon( 'icon_open', 'open' );

			?><span <?php echo $this->get_render_attribute_string( 'text' ); ?>><?php echo $settings['text_closed']; ?></span>
		</span>
		<?php
	}

	protected function render_icon( $setting_key, $type = 'closed' ) {
		$settings = $this->get_settings();

		if ( empty( $settings[ $setting_key ] ) && empty( $settings[ 'selected_' . $setting_key ]['value'] ) ) {
			return;
		}

		$migrated = isset( $settings['__fa4_migrated']['selected_' . $setting_key ] );
		$is_new = empty( $settings[ $setting_key ] ) && Icons_Manager::is_migration_allowed();

		$this->add_render_attribute( [
			'icon-wrapper-' . $type => [
				'class' => [
					'ee-icon',
					'ee-icon-support--svg',
					'ee-icon--' . $settings['icon_align'],
					'ee-button-icon',
					'ee-unfold__icon',
					'ee-unfold__icon--' . $type,
				],
			],
		] );

		if ( ! empty( $settings[ $setting_key ] ) ) {
			$this->add_render_attribute( [
				'icon-' . $type => [
					'class' => $settings[ $setting_key ],
					'aria-hidden' => 'true',
				],
			] );
		}

		?><span <?php echo $this->get_render_attribute_string( 'icon-wrapper-' . $type ); ?>><?php
			if ( $is_new || $migrated ) {
				Icons_Manager::render_icon( $settings['selected_' . $setting_key ], [ 'aria-hidden' => 'true' ] );
			} else {
				?><i <?php echo $this->get_render_attribute_string( 'icon-' . $type ); ?>></i><?php
			}	
		?></span><?php
	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering.
	 *
	 * @since  1.2.0
	 * @return void
	 */
	protected function _content_template() { ?><#

		if ( ! settings.content ) {
			return;
		}

		view.addRenderAttribute( {
			'wrapper' : {
				'class' : 'ee-unfold',
			},
			'mask' : {
				'class' : 'ee-unfold__mask',
			},
			'button' : {
				'class' : [
					'ee-button',
				],
			},
			'button-wrapper' : {
				'class' : 'ee-button-wrapper',
			},
			'separator' : {
				'class' : 'ee-unfold__separator',
			},
			'content' : {
				'class' : 'ee-unfold__content',
			},
			'trigger' : {
				'class' : 'ee-unfold__trigger',
			},
		} );

		view.addInlineEditingAttributes( 'content', 'advanced' );

		if ( '' !== settings.size ) {
			view.addRenderAttribute( 'button', 'class', 'ee-size-' + settings.size );
		}

		#><div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<div {{{ view.getRenderAttributeString( 'mask' ) }}}>
				<div {{{ view.getRenderAttributeString( 'content' ) }}}>
					{{{ settings.content }}}
				</div>
				<div {{{ view.getRenderAttributeString( 'separator' ) }}}></div>
			</div>
			<div {{{ view.getRenderAttributeString( 'trigger' ) }}}>
				<span {{{ view.getRenderAttributeString( 'button-wrapper' ) }}}>
					<span {{{ view.getRenderAttributeString( 'button' ) }}}>
						<?php echo $this->_text_template(); ?>
					</span>
				</span>
			</div>
		</div><?php
	}

	/**
	 * Text Template
	 *
	 * @since  1.7.0
	 * @return void
	 */
	protected function _text_template() { ?><#

		var iconHTML = elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' ),
			iconMigrated = elementor.helpers.isIconMigrated( settings, 'selected_icon' );

		var openIconHTML = elementor.helpers.renderIcon( view, settings.selected_icon_open, { 'aria-hidden': true }, 'i' , 'object' ),
			openIconMigrated = elementor.helpers.isIconMigrated( settings, 'selected_icon_open' );

		view.addRenderAttribute( {
			'content-wrapper' : {
				'class' : 'ee-button-content-wrapper'
			},
			'icon-wrapper-closed' : {
				'class' : [
					'ee-icon',
					'ee-icon-support--svg',
					'ee-icon--' + settings.icon_align,
					'ee-button-icon',
					'ee-unfold__icon',
					'ee-unfold__icon--closed',
				],
			},
			'icon-wrapper-open' : {
				'class' : [
					'ee-icon',
					'ee-icon-support--svg',
					'ee-icon--' + settings.icon_align,
					'ee-button-icon',
					'ee-unfold__icon',
					'ee-unfold__icon--open',
				],
			},
			'icon-closed' : {
				'class' : settings.icon,
				'aria-hidden' : 'true',
			},
			'icon-open' : {
				'class' : settings.icon_open,
				'aria-hidden' : 'true',
			},
			'text' : {
				'class' : 'ee-button-text',
				'data-close-label' : settings.text_open,
				'data-open-label' : settings.text_closed,
			},
		} );

		#><span {{{ view.getRenderAttributeString( 'content-wrapper' ) }}}>
			
			<# if ( settings.icon || settings.selected_icon.value ) { #>
			<span {{{ view.getRenderAttributeString( 'icon-wrapper-closed' ) }}}>
				<# if ( ( iconMigrated || ! settings.icon ) && iconHTML.rendered ) { #>
					{{{ iconHTML.value }}}
				<# } else { #>
					<i {{{ view.getRenderAttributeString( 'icon-closed' ) }}}></i>
				<# } #>
			</span>
			<# } #>

			<# if ( settings.icon_open || settings.selected_icon_open.value ) { #>
			<span {{{ view.getRenderAttributeString( 'icon-wrapper-open' ) }}}>
				<# if ( ( openIconMigrated || ! settings.icon_open ) && openIconHTML.rendered ) { #>
					{{{ openIconHTML.value }}}
				<# } else { #>
					<i {{{ view.getRenderAttributeString( 'icon-open' ) }}}></i>
				<# } #>
			</span>
			<# } #>

			<span {{{ view.getRenderAttributeString( 'text' ) }}}>{{{ settings.text_closed }}}</span>
		</span><?php
	}
}
