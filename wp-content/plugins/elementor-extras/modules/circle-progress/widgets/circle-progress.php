<?php
namespace ElementorExtras\Modules\CircleProgress\Widgets;

// Extras for Elementor Classes
use ElementorExtras\Base\Extras_Widget;

// Elementor Classes
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Circle_Progress
 *
 * @since 0.1.0
 */
class Circle_Progress extends Extras_Widget {

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  0.1.0
	 * @return string
	 */
	public function get_name() {
		return 'circle-progress';
	}

	/**
	 * Get Title
	 * 
	 * Get the title of the widget
	 *
	 * @since  0.1.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Circle Progress', 'elementor-extras' );
	}

	/**
	 * Get Icon
	 * 
	 * Get the icon of the widget
	 *
	 * @since  0.1.0
	 * @return string
	 */
	public function get_icon() {
		return 'nicon nicon-circle-progress';
	}

	/**
	 * Get Script Depends
	 * 
	 * A list of scripts that the widgets is depended in
	 *
	 * @since  0.1.0
	 * @return array
	 */
	public function get_script_depends() {
		return [
			'circle-progress',
			'jquery-appear',
			'jquery-easing',
		];
	}

	/**
	 * Register Widget Controls
	 *
	 * @since  0.1.0
	 * @return void
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_circle',
			[
				'label' => __( 'Circle', 'elementor-extras' ),
			]
		);

			$this->add_control(
				'value_heading',
				[
					'label'			=> __( 'Value', 'elementor-extras' ),
					'type' 			=> Controls_Manager::HEADING,
					'separator'		=> 'before',
				]
			);

			$this->add_control(
				'value_progress',
				[
					'label' 		=> __( 'Progress Value', 'elementor-extras' ),
					'description'	=> __( 'Choose absolute if you want to manually define the maximum value and display the entered value instead of the percentage.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'percentage',
					'frontend_available' => true,
					'options' 		=> [
						'percentage'	=> __( 'Percentage', 'elementor-extras' ),
						'absolute' 		=> __( 'Absolute', 'elementor-extras' ),
					],
				]
			);

			$this->add_control(
				'value',
				[
					'label' 	=> __( 'Value', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'title'		=> __( 'Accepted value formats are: 50, 0.50, 0,50, 50/100', 'elementor-extras' ),
					'default' 	=> '75',
					'frontend_available' => true,
					'dynamic'	=> [
						'active'		=> true,
						'categories' 	=> [ TagsModule::POST_META_CATEGORY ],
					],
				]
			);

			$this->add_control(
				'value_decimal_move',
				[
					'label' 		=> __( 'Move Decimal', 'elementor-extras' ),
					'description'	=> __( 'Move the decimal point of the number shown, keeping the progress to the default value.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 0,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> -4,
							'max' 	=> 4,
							'step'	=> 1,
						],
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'value_max',
				[
					'label' 	=> __( 'Max. Value', 'elementor-extras' ),
					'type' 		=> Controls_Manager::NUMBER,
					'default' 	=> 100,
					'min'		=> 0,
					'step'		=> 1,
					'frontend_available' => true,
					'condition' => [
						'value_progress' => 'absolute',
					],
				]
			);

			$this->add_control(
				'value_position',
				[
					'label'			=> __( 'Value Position', 'elementor-extras' ),
					'description'	=> __( 'Position of the value relative to circle.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'inside',
					'options' 		=> [
						'inside' 	=> __( 'Inside', 'elementor-extras' ),
						'below' 	=> __( 'Below', 'elementor-extras' ),
						'hide' 		=> __( 'Hide', 'elementor-extras' ),
					],
				]
			);

			$this->add_control(
				'selected_icon',
				[
					'label' => __( 'Icon', 'elementor-extras' ),
					'type' => Controls_Manager::ICONS,
					'fa4compatibility' => 'icon',
					'separator'		=> 'before',
					'condition'		=> [
						'value_position!' => 'inside',
					],
				]
			);

			$this->add_control(
				'suffix_heading',
				[
					'label'			=> __( 'Suffix', 'elementor-extras' ),
					'type' 			=> Controls_Manager::HEADING,
					'separator'		=> 'before',
				]
			);

			$this->add_control(
				'suffix',
				[
					'type'		=> Controls_Manager::TEXT,
					'label' 	=> __( 'Text', 'elementor-extras' ),
					'default'	=> '%',
					'separator' => 'none'
				]
			);

			$this->add_control(
				'suffix_position',
				[
					'label'		=> __( 'Position', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'after',
					'options' 	=> [
						'after' 	=> __( 'After', 'elementor-extras' ),
						'before' 	=> __( 'Before', 'elementor-extras' ),
					],
					'prefix_class'	=> 'ee-circle-progress-suffix--'
				]
			);

			$this->add_responsive_control(
				'suffix_vertical_align',
				[
					'label' 		=> __( 'Alignment', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'top',
					'options' 		=> [
						'top'    		=> [
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
					'prefix_class'		=> 'ee-circle-progress-suffix--'
				]
			);

			$this->add_control(
				'suffix_top_adjustment',
				[
					'label' 		=> __( 'Top Offset', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '0.5',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 3,
							'step'	=> 0.01,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-circle-progress__value .suffix' => 'margin-top: {{SIZE}}em;',
					],
					'condition'	=> [
						'suffix_vertical_align' => 'top',
					]
				]
			);

			$this->add_control(
				'animation_heading',
				[
					'label'			=> __( 'Settings', 'elementor-extras' ),
					'type' 			=> Controls_Manager::HEADING,
					'separator'		=> 'before',
				]
			);

			$this->add_control(
				'animate',
				[
					'label' 		=> __( 'Animate', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'yes',
					'frontend_available' => true
				]
			);

			$this->add_control(
				'easing',
				[
					'label'		=> __( 'Easing', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'easeInOutCubic',
					'options' 	=> [
						'easeInQuad' 			=> __( 'easeInQuad', 'elementor-extras' ),
						'easeOutQuad' 			=> __( 'easeOutQuad', 'elementor-extras' ),
						'easeInOutQuad' 		=> __( 'easeInOutQuad', 'elementor-extras' ),
						'easeInCubic' 			=> __( 'easeInCubic', 'elementor-extras' ),
						'easeOutCubic' 			=> __( 'easeOutCubic', 'elementor-extras' ),
						'easeInOutCubic'		=> __( 'easeInOutCubic', 'elementor-extras' ),
						'easeInQuart' 			=> __( 'easeInQuart', 'elementor-extras' ),
						'easeOutQuart' 			=> __( 'easeOutQuart', 'elementor-extras' ),
						'easeInOutQuart' 		=> __( 'easeInOutQuart', 'elementor-extras' ),
						'easeInQuint' 			=> __( 'easeInQuint', 'elementor-extras' ),
						'easeOutQuint' 			=> __( 'easeOutQuint', 'elementor-extras' ),
						'easeInOutQuint' 		=> __( 'easeInOutQuint', 'elementor-extras' ),
						'easeInSine' 			=> __( 'easeInSine', 'elementor-extras' ),
						'easeOutSine' 			=> __( 'easeOutSine', 'elementor-extras' ),
						'easeInOutSine' 		=> __( 'easeInOutSine', 'elementor-extras' ),
						'easeInExpo' 			=> __( 'easeInExpo', 'elementor-extras' ),
						'easeOutExpo' 			=> __( 'easeOutExpo', 'elementor-extras' ),
						'easeInOutExpo' 		=> __( 'easeInOutExpo', 'elementor-extras' ),
						'easeInCirc' 			=> __( 'easeInCirc', 'elementor-extras' ),
						'easeOutCirc' 			=> __( 'easeOutCirc', 'elementor-extras' ),
						'easeInOutCirc' 		=> __( 'easeInOutCirc', 'elementor-extras' ),
						'easeInElastic' 		=> __( 'easeInElastic', 'elementor-extras' ),
						'easeOutElastic' 		=> __( 'easeOutElastic', 'elementor-extras' ),
						'easeInOutElastic' 		=> __( 'easeInOutElastic', 'elementor-extras' ),
						'easeInBack' 			=> __( 'easeInBack', 'elementor-extras' ),
						'easeOutBack' 			=> __( 'easeOutBack', 'elementor-extras' ),
						'easeInOutBack' 		=> __( 'easeInOutBack', 'elementor-extras' ),
						'easeInBounce' 			=> __( 'easeInBounce', 'elementor-extras' ),
						'easeOutBounce' 		=> __( 'easeOutBounce', 'elementor-extras' ),
						'easeInOutBounce' 		=> __( 'easeInOutBounce', 'elementor-extras' ),
					],
					'condition' 	=> [
						'animate!'	=> '',
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'reverse',
				[
					'label' 		=> __( 'Reverse', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'yes',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'duration',
				[
					'label' 		=> __( 'Duration (ms)', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 1,
							'max' 	=> 3000,
							'step'	=> 100,
						],
					],
					'condition' 	=> [
						'animate!'	=> '',
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'appear_offset',
				[
					'label' 		=> __( 'Appear Offset', 'elementor-extras' ),
					'description'	=> __( 'Specifies the offset, relative to when the widget enteres the viewport, after which the animation starts', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 10,
							'max' 	=> 1000,
						],
					],
					'condition' 	=> [
						'animate!'	=> '',
					],
				]
			);

			$this->add_control(
				'angle',
				[
					'label' 		=> __( 'Start Angle', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 2 * M_PI,
							'step'	=> 0.001,
						],
					],
					'frontend_available' => true
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_text',
			[
				'label' => __( 'Text', 'elementor-extras' ),
			]
		);

			$this->add_control(
			'text',
				[
					'label' => '',
					'type' => Controls_Manager::WYSIWYG,
					'default' => __( 'I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'elementor-extras' ),
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_circle_style',
			[
				'label' => __( 'Circle', 'elementor-extras' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'size',
				[
					'label' 		=> __( 'Size', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 100,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 10,
							'max' 	=> 1000,
						],
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'thickness',
				[
					'label' 		=> __( 'Thickness (%)', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 10,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 1,
							'max' 	=> 100,
						],
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'lineCap',
				[
					'label'		=> __( 'Line Cap', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'butt',
					'options' 	=> [
						'butt' 		=> __( 'Butt', 'elementor-extras' ),
						'round' 	=> __( 'Round', 'elementor-extras' ),
						'square' 	=> __( 'Square', 'elementor-extras' ),
					],
					'frontend_available' => true
				]
			);

			$gradient = new Repeater();

			$gradient->add_control(
				'color',
				[
					'label' 	=> __( 'Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
				]
			);

			$this->add_control(
				'fill',
				[
					'label' 	=> __( 'Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::REPEATER,
					'fields' 		=> $gradient->get_controls(),
					'title_field' 	=> '{{{ color }}}'
				]
			);

			$this->add_control(
				'gradient_angle',
				[
					'label'		=> __( 'Gradient Angle (&deg;)', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '2',
					'options' 	=> [
						'2' 	=> __( '0', 'elementor-extras' ),
						'4' 	=> __( '45', 'elementor-extras' ),
						'0.5' 	=> __( '90', 'elementor-extras' ),
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'emptyFill',
				[
					'label' 	=> __( 'Empty Fill', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'global' => [
						'default' => Global_Colors::COLOR_PRIMARY,
					],
					'frontend_available' => true
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_value_style',
			[
				'label' => __( 'Value', 'elementor-extras' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'value_color',
				[
					'label' 	=> __( 'Value Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default' 	=> '',
					'selectors' => [
						'{{WRAPPER}} .ee-circle-progress__value' => 'color: {{VALUE}};',
					],
					'global' => [
						'default' => Global_Colors::COLOR_TEXT,
					],
				]
			);

			$this->add_control(
				'suffix_color',
				[
					'label' 	=> __( 'Suffix Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default' 	=> '',
					'selectors' => [
						'{{WRAPPER}} .ee-circle-progress__value .suffix' => 'color: {{VALUE}};',
					],
					'global' => [
						'default' => Global_Colors::COLOR_TEXT,
					],
				]
			);

			$this->add_control(
				'value_spacing',
				[
					'label' 		=> __( 'Value Spacing', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 200,
						],
					],
					'condition'	=> [
						'value_position!'	=> 'inside'
					],
					'selectors'	=> [
						'{{WRAPPER}}.ee-circle-progress-position--below .ee-circle-progress__value' => 'margin-top: {{SIZE}}{{UNIT}}',
					]
				]
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				[
					'name' 		=> 'value_shadow',
					'selector' 	=> '{{WRAPPER}} .ee-circle-progress__value',
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'value_typography',
					'selector' 	=> '{{WRAPPER}} .ee-circle-progress__value',
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => __( 'Icon', 'elementor-extras' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
				'condition'		=> [
					'value_position!' 	=> 'inside',
					'icon!'				=> '',
				],
			]
		);

			$this->add_control(
				'icon_color',
				[
					'label' 	=> __( 'Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default' 	=> '',
					'selectors' => [
						'{{WRAPPER}} .ee-circle-progress__icon' => 'color: {{VALUE}};',
					],
					'global' => [
						'default' => Global_Colors::COLOR_TEXT,
					],
					'condition'		=> [
						'value_position!' 	=> 'inside',
						'icon!'				=> '',
					],
				]
			);

			$this->add_control(
				'icon_size',
				[
					'label' 		=> __( 'Size', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 9,
							'max' 	=> 100,
						],
					],
					'range' 	=> [
						'em' 	=> [
							'min' 	=> 1,
							'max' 	=> 10,
							'step'	=> 0.1,
						],
					],
					'range' 	=> [
						'rem' 	=> [
							'min' 	=> 1,
							'max' 	=> 10,
							'step'	=> 0.1,
						],
					],
					'size_units' 	=> [ 'px', 'em', 'rem' ],
					'condition'		=> [
						'value_position!' 	=> 'inside',
						'icon!'				=> '',
					],
					'selectors'	=> [
						'{{WRAPPER}} .ee-circle-progress__icon' => 'font-size: {{SIZE}}{{UNIT}}',
					]
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_text_style',
			[
				'label' => __( 'Text', 'elementor-extras' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'text_color',
				[
					'label' 	=> __( 'Text Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default' 	=> '',
					'selectors' => [
						'{{WRAPPER}} .ee-circle-progress__text' => 'color: {{VALUE}};',
					],
					'global' => [
						'default' => Global_Colors::COLOR_TEXT,
					],
				]
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				[
					'name' 		=> 'text_shadow',
					'selector' 	=> '{{WRAPPER}} .ee-circle-progress__text',
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'text_typography',
					'selector' 	=> '{{WRAPPER}} .ee-circle-progress__text',
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
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
	 * @since  0.1.0
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$circle_progress_fill = array();

		$this->add_render_attribute( [
			'wrapper' => [
				'class' => [
					'ee-circle-progress',
					'ee-circle-progress-position--' . $settings['value_position'],
				],
			],
		] );

		if( ! empty( $settings['suffix'] ) ) {
			$this->add_render_attribute( 'wrapper', 'data-suffix', $settings['suffix'] );
		}

		if ( $settings['appear_offset']['size'] ) {
			$this->add_render_attribute( 'wrapper', 'data-appear-top-offset', $settings['appear_offset']['size'] );
		}

		if ( count( $settings['fill'] ) > 0 ) {
			if ( count( $settings['fill'] ) === 1 ) {
				if ( ! empty( $settings['fill'][0]['color'] ) ) {
					$circle_progress_fill['color'] = $settings['fill'][0]['color'];
				}
			} else { // Gradient
				$circle_progress_fill['gradient'] = array();
				foreach (  $settings['fill'] as $fill ) {
					if ( ! empty( $fill['color'] ) ) {
						$circle_progress_fill['gradient'][] = $fill['color'];
					}
				}

				$gradient_angle = ( (int)$settings['gradient_angle'] > 0 ) ? (int)$settings['gradient_angle'] : 4;

				$circle_progress_fill['gradientAngle'] = M_PI / $gradient_angle;
			}

			if ( count( $circle_progress_fill ) > 0 ) {
				$circle_progress_settings['fill'] = json_encode( $circle_progress_fill );
				$this->add_render_attribute( 'wrapper', 'data-fill', $circle_progress_settings['fill'] );
			}
		}

		?><div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>><?php
			if ( ( ! empty( $settings['icon'] ) || ! empty( $settings['selected_icon']['value'] ) ) && 'inside' !== $settings['value_position'] ) { $this->render_icon(); }
			if ( 'inside' === $settings['value_position'] ) { $this->render_value( $settings ); }
		?></div><?php

		if ( 'below' === $settings['value_position'] ) { $this->render_value( $settings ); }
		if ( $settings['text'] ) { $this->render_text( $settings ); }
	}

	/**
	 * Render Icon
	 * 
	 * Markup for the icon
	 *
	 * @since  0.1.0
	 * @return void
	 */
	protected function render_icon() {
		$settings = $this->get_settings_for_display();

		$migrated = isset( $settings['__fa4_migrated']['selected_icon'] );
		$is_new = empty( $settings['icon'] ) && Icons_Manager::is_migration_allowed();

		$this->add_render_attribute( [
			'icon-wrapper' => [
				'class' => [
					'ee-circle-progress__icon',
					'ee-icon-support--svg',
					'ee-icon',
				],
			],
			'icon' => [
				'class' => esc_attr( $settings['icon'] ),
				'aria-hidden' => 'true',
			],
		] );

		?><span <?php echo $this->get_render_attribute_string( 'icon-wrapper' ); ?>><?php
			if ( $is_new || $migrated ) {
				Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] );
			} else {
				?><i <?php echo $this->get_render_attribute_string( 'icon' ); ?>></i><?php
			}
		?></span><?php
	}

	/**
	 * Render Value
	 * 
	 * Renders the template for the value
	 *
	 * @since  0.1.0
	 * @return void
	 */
	protected function render_value( $settings ) {

		$this->add_render_attribute( [
			'value-wrapper' => [
				'class' => [
					'ee-circle-progress__value',
				],
			],
			'value' => [
				'class' => 'value',
			],
			'suffix' => [
				'class' => 'suffix',
			],
		] );

		$this->add_inline_editing_attributes( 'suffix', 'basic' );

		if ( '' !== $settings['value'] ) {
			$this->add_render_attribute( 'value-wrapper', 'data-cp-value', $settings['value'] );
		}

		?><div <?php echo $this->get_render_attribute_string( 'value-wrapper' ); ?>>
			<span <?php echo $this->get_render_attribute_string( 'value' ); ?>></span><?php
			if ( $settings['suffix'] ) {
				?><span <?php echo $this->get_render_attribute_string( 'suffix' ); ?>>
					<?php echo $settings['suffix']; ?>
				</span><?php
			}
		?></div><?php
	}

	/**
	 * Render Text
	 * 
	 * Renders the template for the text
	 *
	 * @since  0.1.0
	 * @return void
	 */
	protected function render_text( $settings ) {

		$this->add_inline_editing_attributes( 'text', 'advanced' );
		$this->add_render_attribute( 'text', 'class', 'ee-circle-progress__text' );

		?><div <?php echo $this->get_render_attribute_string( 'text' ); ?>>
			<?php echo $this->parse_text_editor( $settings['text'] ); ?>
		</div><?php
	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering.
	 *
	 * @since  0.1.0
	 * @return void
	 */
	protected function _content_template() {
		?><#

		var circle_progress_fill = {},
			entityMap = {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#39;',
				'/': '&#x2F;',
				'`': '&#x60;',
				'=': '&#x3D;'
			};

		view.addRenderAttribute( {
			'wrapper' : {
				'class' : [
					'ee-circle-progress',
					'ee-circle-progress-position--' + settings.value_position,
				],
			},
		} );

		if ( settings.suffix ) {
			view.addRenderAttribute( 'wrapper', 'data-suffix', settings.suffix );
		}

		if ( settings.appear_offset ) {
			view.addRenderAttribute( 'wrapper', 'data-appear-top-offset', settings.appear_offset.size );
		}

		if ( settings.fill.length > 0 ) {
			if ( settings.fill.length === 1 ) {

				if ( settings.fill[0].color != '' ) {
					circle_progress_fill.color = settings.fill[0].color;
				}

			} else {

				circle_progress_fill.gradient = [];
				var gradient_angle = ( settings.gradient_angle > 0 ) ? parseInt(settings.gradient_angle) : 4;

				_.each( settings.fill, function( fill ) {
					if ( fill.color != '' ) circle_progress_fill.gradient.push( fill.color );
				});
				circle_progress_fill.gradientAngle = Math.PI / gradient_angle;
			}
		}

		if ( ! jQuery.isEmptyObject( circle_progress_fill ) ) {

			circle_progress_fill = JSON.stringify( circle_progress_fill );
			circle_progress_fill = circle_progress_fill.replace( /[&<>"'`=\/]/g, function (s) {
				return entityMap[s];
			});

			circle_progress_fill = jQuery('<textarea />').html( circle_progress_fill ).text();

			view.addRenderAttribute( 'wrapper', 'data-fill', circle_progress_fill );
		}

		#><div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>

			<# if ( ( settings.icon || settings.selected_icon ) && 'inside' !== settings.value_position ) { #>
				<?php $this->_icon_template(); ?>
			<# } #>

			<# if ( 'inside' === settings.value_position ) { #>
				<?php $this->_value_template(); ?>
			<# } #>

		</div>

		<# if ( 'below' === settings.value_position ) { #>
			<?php $this->_value_template(); ?>
		<# } #>

		<# if ( settings.text ) { #>
			<?php $this->_text_template(); ?>
		<# } #>

		<?php
	}

	/**
	 * Icon Template
	 * 
	 * JS template for the icon
	 *
	 * @since  0.1.0
	 * @return void
	 */
	protected function _icon_template() {
		?><#

		var iconHTML = elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' ),
			migrated = elementor.helpers.isIconMigrated( settings, 'selected_icon' );

		view.addRenderAttribute( {
			'icon-wrapper' : {
				'class' : [
					'ee-circle-progress__icon',
					'ee-icon-support--svg',
					'ee-icon',
				],
			},
			'icon' : {
				'class' : settings.icon,
				'aria-hidden' : 'true',
			},
		} );

		#><div {{{ view.getRenderAttributeString( 'icon-wrapper' ) }}}><#
			if ( ( migrated || ! settings.icon ) && iconHTML.rendered ) {
				#>{{{ iconHTML.value }}}<#
			} else {
				#><i {{{ view.getRenderAttributeString( 'icon' ) }}}></i><#
			}
		#></div><?php
	}

	/**
	 * Value Template
	 * 
	 * JS template for the value
	 *
	 * @since  0.1.0
	 * @return void
	 */
	protected function _value_template() {
		?><#

		view.addRenderAttribute( {
			'value-wrapper' : {
				'class' : [
					'ee-circle-progress__value',
				],
			},
			'value' : {
				'class' : 'value',
			},
			'suffix' : {
				'class' : 'suffix',
			},
		} );

		if ( '' !== settings.value ) {
			view.addRenderAttribute( 'value-wrapper', 'data-cp-value', settings.value );
		}

		view.addInlineEditingAttributes( 'suffix', 'basic' );

		#><div {{{ view.getRenderAttributeString( 'value-wrapper' ) }}}>

			<span {{{ view.getRenderAttributeString( 'value' ) }}}></span>

			<# if ( settings.suffix ) { #>
				<span {{{ view.getRenderAttributeString( 'suffix' ) }}}>{{{ settings.suffix }}}</span>
			<# } #>

		</div><?php
	}

	/**
	 * Text Template
	 * 
	 * JS template for the text
	 *
	 * @since  0.1.0
	 * @return void
	 */
	protected function _text_template() {
		?><#

		view.addRenderAttribute( 'text', 'class', 'ee-circle-progress__text' );
		view.addInlineEditingAttributes( 'text', 'advanced' );

		#><div {{{ view.getRenderAttributeString( 'text' ) }}}>{{{ settings.text }}}</div><?php
	}
}
