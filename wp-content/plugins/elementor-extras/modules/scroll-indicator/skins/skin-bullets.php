<?php
namespace ElementorExtras\Modules\ScrollIndicator\Skins;

// Extras for Elementor Classes
use ElementorExtras\Group_Control_Transition;

// Elementor Classes
use Elementor\Controls_Stack;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\ScrollIndicator\Skins
 *
 * @since  2.1.0
 */
class Skin_Bullets extends Skin_Base {

	/**
	 * Get ID
	 * 
	 * Gets the current skin ID
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_id() {
		return 'bullets';
	}

	/**
	 * Get Title
	 * 
	 * Gets the current skin title
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Bullets', 'elementor-extras' );
	}

	/**
	 * Register Controls Actions
	 * 
	 * Registers controls at specific points in the Controls Stack
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function _register_controls_actions() {
		parent::_register_controls_actions();

		add_action( 'elementor/element/ee-scroll-indicator/section_settings/after_section_end', [ $this, 'register_settings_controls' ] );
		add_action( 'elementor/element/ee-scroll-indicator/section_elements/after_section_end', [ $this, 'register_tooltip_content_controls' ] );
		
	}

	/**
	 * Register content controls
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function register_content_controls() {
		parent::register_content_controls();
	}

	/**
	 * Register style controls
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function register_style_controls() {
		$this->register_position_style_controls();
		$this->register_bullets_style_controls();
		$this->register_tooltip_style_controls();
	}

	/**
	 * Register settings controls
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function register_settings_controls() {

		$this->parent->start_injection( [
			'type' => 'section',
			'at' => 'start',
			'of' => 'section_settings',
		] );

			$this->add_control(
				'notice',
				[
					'type' 				=> Controls_Manager::RAW_HTML,
					'raw' 				=> sprintf( __( '%1$sImportant note:%2$s You can position the bullets as fixed on the page using the Elementor Custom Positioning controls.', 'elementor-extras' ), '<strong>', '</strong>' ),
					'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-info',
				]
			);

		$this->parent->end_injection();

		$this->parent->start_injection( [
			'type' => 'section',
			'at' => 'end',
			'of' => 'section_settings',
		] );

			$this->add_control(
				'tooltips',
				[
					'label' 		=> __( 'Enable Tooltips', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

		$this->parent->end_injection();

		$this->parent->start_injection( [
			'of' => '_skin',
		] );

			$this->add_responsive_control(
				'direction',
				[
					'label' 	=> __( 'Direction', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'vertical',
					'options'	=> [
						'vertical' 		=> __( 'Vertical', 'elementor-extras' ),
						'horizontal' 	=> __( 'Horizontal', 'elementor-extras' ),
					],
					'prefix_class' => 'ee-scroll-indicator-direction%s--',
				]
			);

		$this->parent->end_injection();
	}

	/**
	 * Register height style controls
	 * that depend on custom positioning
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function register_position_style_controls() {

		$this->start_controls_section(
			'section_position',
			[
				'label' 	=> __( 'Custom Positioning', 'elementor-extras' ),
				'tab'   	=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'_position' => 'fixed',
					$this->get_control_id( 'direction' ) => 'vertical',
				],
			]
		);

			$this->add_control(
				'notice_fixed',
				[
					'type' 	=> Controls_Manager::RAW_HTML,
					'raw' 	=> __( 'You have chosen to set the position of the widget to fixed. Here you can modify the height of widget for better positioning.', 'elementor-extras' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					'condition' => [
						'_position' => 'fixed',
						$this->get_control_id( 'direction' ) => 'vertical',
					],
				]
			);

			$this->add_responsive_control(
				'wrapper_height',
				[
					'label' 	=> __( 'Height', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '',
					'options' 	=> [
						'' 			=> __( 'Default', 'elementor-extras' ),
						'inherit' 	=> __( 'Full Height', 'elementor-extras' ) . ' (100%)',
						'auto' 		=> __( 'Inline', 'elementor-extras' ) . ' (auto)',
						'initial' 	=> __( 'Custom', 'elementor-extras' ),
					],
					'selectors_dictionary' => [
						'inherit' 	=> '100%',
					],
					'prefix_class' 	=> 'elementor-widget%s__height-',
					'selectors' 	=> [
						'{{WRAPPER}}' => 'height: {{VALUE}}; max-height: {{VALUE}}',
					],
					'condition' 	=> [
						$this->get_control_id( 'direction' ) => 'vertical',
						'_position' => 'fixed',
					]
				]
			);

			$this->add_responsive_control(
				'custom_height',
				[
					'label' 	=> __( 'Custom Height', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'max' 	=> 1000,
							'step' 	=> 1,
						],
						'%' 	=> [
							'max' 	=> 100,
							'step' 	=> 1,
						],
					],
					'condition' => [
						$this->get_control_id( 'direction' ) => 'vertical',
						$this->get_control_id( 'wrapper_height' ) => 'initial',
						'_position' => 'fixed',
					],
					'device_args' => [
						Controls_Stack::RESPONSIVE_TABLET => [
							'condition' => [
								'height_tablet' => [ 'initial' ],
							],
						],
						Controls_Stack::RESPONSIVE_MOBILE => [
							'condition' => [
								'height_mobile' => [ 'initial' ],
							],
						],
					],
					'size_units' => [ 'px', '%', 'vh' ],
					'selectors' => [
						'{{WRAPPER}}' => 'height: {{SIZE}}{{UNIT}}; max-height: {{SIZE}}{{UNIT}}',
					],
				]
			);

		$this->end_controls_section();

	}

	/**
	 * Register bullets style controls
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function register_bullets_style_controls() {

		$this->start_controls_section(
			'section_bullets_style',
			[
				'label' => __( 'Bullets', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'square',
				[
					'label' 		=> __( 'Square', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
				]
			);

			$this->add_responsive_control(
				'width',
				[
					'label' 	=> __( 'Width (px)', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 1,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-scroll-indicator__element__wrapper' => 'width: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						$this->get_control_id( 'square' ) => '',
					],
				]
			);

			$this->add_responsive_control(
				'height',
				[
					'label' 	=> __( 'Height (px)', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 1,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-scroll-indicator__element__wrapper' => 'height: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						$this->get_control_id( 'square' ) => '',
					],
				]
			);

			$this->add_responsive_control(
				'size',
				[
					'label' 	=> __( 'Size (px)', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 2,
							'max' => 50,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-scroll-indicator__element__wrapper' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						$this->get_control_id( 'square!' ) => '',
					],
				]
			);

			$this->add_control(
				'border_radius',
				[
					'label' 	=> __( 'Border Radius', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'em' 	=> [
							'min' 	=> 0,
							'max' 	=> 5,
							'step' 	=> 0.1,
						],
						'rem' => [
							'min' 	=> 0,
							'max' 	=> 5,
							'step' 	=> 0.1,
						],
						'px' => [
							'min' 	=> 0,
							'max' 	=> 50,
							'step' 	=> 1,
						],
						'%' => [
							'min' 	=> 0,
							'max' 	=> 100,
							'step' 	=> 1,
						],
					],
					'size_units' 	=> [ 'px', '%', 'em', 'rem' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-scroll-indicator__element__wrapper' => 'border-radius: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$this->add_control(
				'spacing',
				[
					'label' 	=> __( 'Spacing (px)', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default'	=> [
						'size' 	=> 0,
					],
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}}.ee-scroll-indicator-direction--vertical .ee-scroll-indicator__element:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}}.ee-scroll-indicator-direction--horizontal .ee-scroll-indicator__element' => 'margin-left: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}}.ee-scroll-indicator-direction--horizontal .ee-scroll-indicator__menu' => 'margin-left: -{{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'padding',
				[
					'label' 		=> __( 'Padding (px)', 'elementor-extras' ),
					'description' 	=> __( 'Padding makes the hoverable area bigger which is useful for smaller bullets', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' 	=> 0,
							'max' 	=> 100,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-scroll-indicator__element__link' => 'padding: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'bullets',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'exclude'	=> [ 'color' ],
					'selector' 	=> '{{WRAPPER}} .ee-scroll-indicator__element__wrapper',
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 		=> 'bullets',
					'selector' 	=> '{{WRAPPER}} .ee-scroll-indicator__element__wrapper',
					'separator'	=> '',
				]
			);

			$this->start_controls_tabs( 'indicators' );

			$this->start_controls_tab( 'indicators_default', [ 'label' => __( 'Default', 'elementor-extras' ) ] );

				$this->add_control(
					'background_color',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__wrapper' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'border_color',
					[
						'label' 	=> __( 'Border Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__wrapper' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'bullets_border' ) . '!' => '',
						]
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'indicators_hover', [ 'label' => __( 'Hover', 'elementor-extras' ) ] );

				$this->add_control(
					'background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link:hover .ee-scroll-indicator__element__wrapper' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'border_color_hover',
					[
						'label' 	=> __( 'Border Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link:hover .ee-scroll-indicator__element__wrapper' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'bullets_border' ) . '!' => '',
						]
					]
				);

				$this->add_control(
					'scale_hover',
					[
						'label' 	=> __( 'Scale', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SLIDER,
						'range' 	=> [
							'px' 	=> [
								'min' => 1,
								'max' => 2,
								'step' => 10,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link:hover .ee-scroll-indicator__element__wrapper' => 'transform: scale({{SIZE}});',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'indicators_reading', [ 'label' => __( 'Reading', 'elementor-extras' ) ] );

				$this->add_control(
					'background_color_reading',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link.is--reading .ee-scroll-indicator__element__wrapper' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'border_color_reading',
					[
						'label' 	=> __( 'Border Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link.is--reading .ee-scroll-indicator__element__wrapper' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'bullets_border' ) . '!' => '',
						]
					]
				);

				$this->add_control(
					'scale_reading',
					[
						'label' 	=> __( 'Scale', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SLIDER,
						'range' 	=> [
							'px' 	=> [
								'min' => 1,
								'max' => 10,
								'step' => 0.01,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link.is--reading .ee-scroll-indicator__element__wrapper' => 'transform: scale({{SIZE}});',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'indicators_read', [ 'label' => __( 'Read', 'elementor-extras' ) ] );

				$this->add_control(
					'background_color_read',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'global' => [
							'default' => Global_Colors::COLOR_ACCENT,
						],
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link.is--read .ee-scroll-indicator__element__wrapper' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'border_color_read',
					[
						'label' 	=> __( 'Border Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link.is--read .ee-scroll-indicator__element__wrapper' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'bullets_border' ) . '!' => '',
						]
					]
				);

				$this->add_control(
					'scale_read',
					[
						'label' 	=> __( 'Scale', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SLIDER,
						'range' 	=> [
							'px' 	=> [
								'min' => 1,
								'max' => 2,
								'step' => 10,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link.is--read .ee-scroll-indicator__element__wrapper' => 'transform: scale({{SIZE}});',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'progress_heading',
				[
					'label'		=> __( 'Progress', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator'	=> 'before',
				]
			);

			$this->add_control(
				'background_color_progress',
				[
					'label' 	=> __( 'Background Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'global' => [
						'default' => Global_Colors::COLOR_PRIMARY,
					],
					'selectors' => [
						'{{WRAPPER}} .ee-scroll-indicator__element__progress' => 'background-color: {{VALUE}};',
					],
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Get default nav class
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function get_nav_class() {
		return 'ee-nav ee-nav--flush';
	}

	/**
	 * Render element item content
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_element_content( $index, $section ) {
		$settings 				= $this->parent->get_settings();
		$wrapper_key 			= $this->parent->_get_repeater_setting_key( 'wrapper', 'sections', $index );
		$link_key 				= $this->parent->_get_repeater_setting_key( 'link', 'sections', $index );
		$progress_key 			= $this->parent->_get_repeater_setting_key( 'progress', 'sections', $index );
		$tooltip_content_key 	= $this->parent->_get_repeater_setting_key( 'tooltip_content', 'sections', $index );
		$content_id 			= $this->parent->get_id() . '_' . $section['_id'];

		$this->parent->add_render_attribute( [
			$wrapper_key => [
				'class' => [
					'ee-scroll-indicator__element__wrapper',
				],
			],
			$link_key => [
				'class' => [
					'ee-scroll-indicator__element__link',
				],
			],
			$progress_key => [
				'class' => [
					'ee-scroll-indicator__element__progress',
					'ee-cover',
				]
			]
		] );

		if ( '' !== $this->get_instance_value( 'tooltips' ) ) {
			$this->parent->add_render_attribute( [
				$link_key => [
					'class' => 'hotip',
					'data-hotips-content' => '#hotip-content-' . $content_id,
					'data-hotips-class' => [
						'ee-global',
						'ee-tooltip',
						'ee-tooltip-' . $this->parent->get_id(),
					],
				],
				$tooltip_content_key => [
					'class' => 'hotip-content',
					'id' => 'hotip-content-' . $content_id,
				],
			] );
		}

		if ( 'yes' === $settings['click'] ) {
			$this->parent->add_render_attribute( $link_key, 'class', 'has--cursor' );
		}

		?>
		<a <?php echo $this->parent->get_render_attribute_string( $link_key ); ?>>
			<div <?php echo $this->parent->get_render_attribute_string( $wrapper_key ); ?>>
				<div <?php echo $this->parent->get_render_attribute_string( $progress_key ); ?>></div>

				<?php if ( '' !== $this->get_instance_value( 'tooltips' ) ) { ?>
				<span <?php echo $this->parent->get_render_attribute_string( $tooltip_content_key ); ?>>
					<?php echo $this->parent->_parse_text_editor( $section['title'] ); ?>
				</span>
				<?php } ?>
			</div>
		</a>
		<?php
	}
}