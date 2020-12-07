<?php
namespace ElementorExtras\Modules\ScrollIndicator\Skins;

// Extras for Elementor Classes
use ElementorExtras\Group_Control_Transition;

// Elementor Classes
use Elementor\Controls_Stack;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\ScrollIndicator\Skins
 *
 * @since  2.1.0
 */
class Skin_List extends Skin_Base {

	/**
	 * Get ID
	 * 
	 * Gets the current skin ID
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_id() {
		return 'list';
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
		return __( 'List', 'elementor-extras' );
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
		add_action( 'elementor/element/ee-scroll-indicator/section_settings/after_section_end', [ $this, 'register_elements_style_controls' ] );
		add_action( 'elementor/element/ee-scroll-indicator/section_settings/after_section_end', [ $this, 'register_numbers_style_controls' ] );
		add_action( 'elementor/element/ee-scroll-indicator/section_settings/after_section_end', [ $this, 'register_text_style_controls' ] );
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
					'raw' 				=> sprintf( __( '%1$sImportant note:%2$s Use the Elementor or Extras sticky functionality to keep the list in view.', 'elementor-extras' ), '<strong>', '</strong>' ),
					'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-info',
				]
			);

		$this->parent->end_injection();

		$this->parent->start_injection( [
			'of' => '_skin',
		] );

			$this->add_control(
				'direction',
				[
					'label' 	=> __( 'Direction', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'vertical',
					'options'	=> [
						'vertical' 		=> __( 'Vertical', 'elementor-extras' ),
						'horizontal' 	=> __( 'Horizontal', 'elementor-extras' ),
					],
					'prefix_class' => 'ee-scroll-indicator-direction--',
				]
			);

			$this->add_control(
				'show',
				[
					'label' 	=> __( 'Show', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '',
					'options'	=> [
						''				=> __( 'Numbers & Text', 'elementor-extras' ),
						'numbers' 		=> __( 'Numbers', 'elementor-extras' ),
						'text' 			=> __( 'Text', 'elementor-extras' ),
					],
				]
			);

			$this->add_control(
				'title_html_tag',
				[
					'label' => __( 'Title HTML Tag', 'elementor-extras' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'h4',
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
					'condition' <> [
						$this->get_control_id( 'show!' ) => [ '', 'text' ],
					],
				]
			);

			$this->add_control(
				'subtitle_html_tag',
				[
					'label' => __( 'Subtitle HTML Tag', 'elementor-extras' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'h6',
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
					'condition' <> [
						$this->get_control_id( 'show!' ) => [ '', 'text' ],
					],
				]
			);

		$this->parent->end_injection();
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
	 * Register elements style controls
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function register_elements_style_controls() {

		$this->start_controls_section(
			'section_elements_style',
			[
				'label' => __( 'Elements', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'spacing',
				[
					'label' 	=> __( 'Spacing (px)', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}}:not(.ee-scroll-indicator-direction--horizontal) .ee-scroll-indicator__element:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}}.ee-scroll-indicator-direction--horizontal .ee-scroll-indicator__element' => 'margin-left: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}}.ee-scroll-indicator-direction--horizontal .ee-scroll-indicator__menu' => 'margin-left: -{{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-scroll-indicator__element__link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 		=> 'items',
					'selector' 	=> '{{WRAPPER}} .ee-scroll-indicator__element__link,
									{{WRAPPER}} .ee-scroll-indicator__element__number,
									{{WRAPPER}} .ee-scroll-indicator__element__bullet,
									{{WRAPPER}} .ee-scroll-indicator__element__title,
									{{WRAPPER}} .ee-scroll-indicator__element__subtitle',
					'separator'	=> '',
				]
			);

			$this->start_controls_tabs( 'items' );

			$this->start_controls_tab( 'item_default', [ 'label' => __( 'Default', 'elementor-extras' ) ] );

				$this->add_control(
					'background_color',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'number_background_color',
					[
						'label' 	=> __( 'Number Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link .ee-scroll-indicator__element__bullet' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'show' ) => [ '', 'numbers' ],
						],
					]
				);

				$this->add_control(
					'number_border_color',
					[
						'label' 	=> __( 'Number Border Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link .ee-scroll-indicator__element__bullet' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'show' ) => [ '', 'numbers' ],
							$this->get_control_id( 'numbers_border!' ) => '',
						],
					]
				);

				$this->add_control(
					'number_color',
					[
						'label' 	=> __( 'Number Text Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__number' => 'color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'show' ) => [ '', 'numbers' ],
						],
					]
				);

				$this->add_control(
					'title_color',
					[
						'label' 	=> __( 'Title Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__title' => 'color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'show' ) => [ '', 'text' ],
						],
					]
				);

				$this->add_control(
					'subtitle_color',
					[
						'label' 	=> __( 'Subtitle Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__subtitle' => 'color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'show' ) => [ '', 'text' ],
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'item_hover', [ 'label' => __( 'Hover', 'elementor-extras' ) ] );

				$this->add_control(
					'background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link:hover' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'show' ) => [ '', 'numbers' ],
						],
					]
				);

				$this->add_control(
					'number_background_color_hover',
					[
						'label' 	=> __( 'Number Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link:hover .ee-scroll-indicator__element__bullet' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'show' ) => [ '', 'numbers' ],
						],
					]
				);

				$this->add_control(
					'number_border_color_hover',
					[
						'label' 	=> __( 'Number Border Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link:hover .ee-scroll-indicator__element__bullet' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'show' ) => [ '', 'numbers' ],
							$this->get_control_id( 'numbers_border!' ) => '',
						],
					]
				);

				$this->add_control(
					'number_color_hover',
					[
						'label' 	=> __( 'Number Text Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link:hover .ee-scroll-indicator__element__number' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'title_color_hover',
					[
						'label' 	=> __( 'Title Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link:hover .ee-scroll-indicator__element__title' => 'color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'show' ) => [ '', 'text' ],
						],
					]
				);

				$this->add_control(
					'subtitle_color_hover',
					[
						'label' 	=> __( 'Subtitle Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link:hover .ee-scroll-indicator__element__subtitle' => 'color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'show' ) => [ '', 'text' ],
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'item_reading', [ 'label' => __( 'Reading', 'elementor-extras' ) ] );

				$this->add_control(
					'background_color_reading',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link.is--reading' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'number_background_color_reading',
					[
						'label' 	=> __( 'Number Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link.is--reading .ee-scroll-indicator__element__bullet' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'show' ) => [ '', 'numbers' ],
						],
					]
				);

				$this->add_control(
					'number_border_color_reading',
					[
						'label' 	=> __( 'Number Border Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link.is--reading .ee-scroll-indicator__element__bullet' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'show' ) => [ '', 'numbers' ],
							$this->get_control_id( 'numbers_border!' ) => '',
						],
					]
				);

				$this->add_control(
					'number_color_reading',
					[
						'label' 	=> __( 'Number Text Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'global' => [
							'default' => Global_Colors::COLOR_PRIMARY,
						],
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link.is--reading  .ee-scroll-indicator__element__number' => 'color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'show' ) => [ '', 'numbers' ],
						],
					]
				);

				$this->add_control(
					'progress_color_reading',
					[
						'label' 	=> __( 'Progress Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'global' => [
							'default' => Global_Colors::COLOR_PRIMARY,
						],
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link.is--reading .ee-scroll-indicator__element__circle circle' => 'stroke: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'show' ) => [ '', 'numbers' ],
						],
					]
				);

				$this->add_control(
					'title_color_reading',
					[
						'label' 	=> __( 'Title Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'global' => [
							'default' => Global_Colors::COLOR_PRIMARY,
						],
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link.is--reading .ee-scroll-indicator__element__title' => 'color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'show' ) => [ '', 'text' ],
						],
					]
				);

				$this->add_control(
					'subtitle_color_reading',
					[
						'label' 	=> __( 'Subtitle Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'global' => [
							'default' => Global_Colors::COLOR_PRIMARY,
						],
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link.is--reading .ee-scroll-indicator__element__subtitle' => 'color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'show' ) => [ '', 'text' ],
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'item_read', [ 'label' => __( 'Read', 'elementor-extras' ) ] );

				$this->add_control(
					'background_color_read',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link.is--read' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'number_background_color_read',
					[
						'label' 	=> __( 'Number Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link.is--read .ee-scroll-indicator__element__bullet' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'show' ) => [ '', 'numbers' ],
						],
					]
				);

				$this->add_control(
					'number_border_color_read',
					[
						'label' 	=> __( 'Number Border Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link.is--read .ee-scroll-indicator__element__bullet' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'show' ) => [ '', 'numbers' ],
							$this->get_control_id( 'numbers_border!' ) => '',
						],
					]
				);

				$this->add_control(
					'number_color_read',
					[
						'label' 	=> __( 'Number Text Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'global' => [
							'default' => Global_Colors::COLOR_ACCENT,
						],
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link.is--read .ee-scroll-indicator__element__number' => 'color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'show' ) => [ '', 'numbers' ],
						],
					]
				);

				$this->add_control(
					'progress_color_read',
					[
						'label' 	=> __( 'Progress Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'global' => [
							'default' => Global_Colors::COLOR_ACCENT,
						],
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link.is--read .ee-scroll-indicator__element__circle circle' => 'stroke: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'show' ) => [ '', 'numbers' ],
						],
					]
				);

				$this->add_control(
					'title_color_read',
					[
						'label' 	=> __( 'Title Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'global' => [
							'default' => Global_Colors::COLOR_ACCENT,
						],
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link.is--read .ee-scroll-indicator__element__title' => 'color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'show' ) => [ '', 'text' ],
						],
					]
				);

				$this->add_control(
					'subtitle_color_read',
					[
						'label' 	=> __( 'Subtitle Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'global' => [
							'default' => Global_Colors::COLOR_ACCENT,
						],
						'condition' => [
							$this->get_control_id( 'show' ) => [ '', 'text' ],
						],
						'selectors' => [
							'{{WRAPPER}} .ee-scroll-indicator__element__link.is--read .ee-scroll-indicator__element__subtitle' => 'color: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'separators',
				[
					'label' 		=> __( 'Separators', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default'		=> '',
					'return_value' 	=> 'yes',
					'separator'		=> 'before',
				]
			);

			$this->add_responsive_control(
				'separator_thickness',
				[
					'label' 	=> __( 'Separator Thickness', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 1,
							'max' => 5,
						],
					],
					'condition' => [
						$this->get_control_id( 'separators' ) => 'yes',
					],
					'selectors' => [
						'{{WRAPPER}}.ee-scroll-indicator-direction--vertical .ee-scroll-indicator__element:not(:last-child)' => 'border-bottom: {{SIZE}}px solid;',
						'{{WRAPPER}}.ee-scroll-indicator-direction--horizontal .ee-scroll-indicator__element:not(:last-child)' => 'border-right: {{SIZE}}px solid;',
					],
				]
			);

			$this->add_control(
				'separator_color',
				[
					'label' 	=> __( 'Separators Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}}.ee-scroll-indicator-direction--vertical .ee-scroll-indicator__element:not(:last-child)' => 'border-bottom-color: {{VALUE}};',
						'{{WRAPPER}}.ee-scroll-indicator-direction--horizontal .ee-scroll-indicator__element:not(:last-child)' => 'border-right-color: {{VALUE}};',
					],
					'condition' => [
						$this->get_control_id( 'separators' ) => 'yes',
					],
				]
			);

		$this->end_controls_section();

	}

	/**
	 * Register numbers style controls
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function register_numbers_style_controls() {

		$this->start_controls_section(
			'section_numbers_style',
			[
				'label' => __( 'Numbers', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					$this->get_control_id( 'show' ) => [ '', 'numbers' ],
				],
			]
		);

			$this->add_control(
				'numbers_align',
				[
					'label' 		=> __( 'Position', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'label_block'	=> false,
					'default' 		=> '',
					'options' 		=> [
						'left' 		=> [
							'title' => __( 'Left', 'elementor-extras' ),
							'icon' 	=> 'eicon-h-align-left',
						],
						'top' 		=> [
							'title' => __( 'Top', 'elementor-extras' ),
							'icon' 	=> 'eicon-v-align-top',
						],
						'right' 	=> [
							'title' => __( 'Right', 'elementor-extras' ),
							'icon' 	=> 'eicon-h-align-right',
						],
						'bottom' 	=> [
							'title' => __( 'Bottom', 'elementor-extras' ),
							'icon' 	=> 'eicon-v-align-bottom',
						],
					],
					'prefix_class'	=> 'ee-scroll-indicator-numbers--',
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'numbers',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
					],
					'selector' 	=> '{{WRAPPER}} .ee-scroll-indicator__element__number',
				]
			);

			$this->add_responsive_control(
				'numbers_size',
				[
					'label' 	=> __( 'Size', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 2,
							'max' => 10,
							'step' => 0.1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-scroll-indicator__element__bullet' => 'width: {{SIZE}}em; height: {{SIZE}}em;',
					],
				]
			);

			$this->add_control(
				'numbers_spacing',
				[
					'label' 	=> __( 'Spacing (px)', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}}.ee-scroll-indicator-numbers--left .ee-scroll-indicator__element__bullet' => 'margin-right: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}}.ee-scroll-indicator-numbers--right .ee-scroll-indicator__element__bullet' => 'margin-left: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}}.ee-scroll-indicator-numbers--top .ee-scroll-indicator__element__bullet' => 'margin-bottom: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}}.ee-scroll-indicator-numbers--bottom .ee-scroll-indicator__element__bullet' => 'margin-top: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						$this->get_control_id( 'show' ) => '',
					],
				]
			);

			$this->add_responsive_control(
				'progress_thickness',
				[
					'label' 	=> __( 'Progress Thickness', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 1,
							'max' => 4,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-scroll-indicator__element__circle circle' => 'stroke-width: {{SIZE}}px;',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'numbers',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'exclude'	=> [ 'width', 'color' ],
					'selector' 	=> '{{WRAPPER}} .ee-scroll-indicator__element__bullet',
				]
			);

			$this->add_responsive_control(
				'numbers_border_width',
				[
					'label' 	=> __( 'Border Width', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 1,
							'max' => 4,
						],
					],
					'condition' => [
						$this->get_control_id( 'numbers_border!' ) => '',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-scroll-indicator__element__bullet' => 'border-width: {{SIZE}}px;',
					],
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Register text style controls
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function register_text_style_controls() {

		$this->start_controls_section(
			'section_text_style',
			[
				'label' => __( 'Text', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					$this->get_control_id( 'show' ) => [ '', 'text' ],
				],
			]
		);

			$this->add_responsive_control(
				'text_align',
				[
					'label' 		=> __( 'Align Text', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> '',
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
						'{{WRAPPER}} .ee-scroll-indicator__element__text' => 'text-align: {{VALUE}};',
					]
				]
			);

			$this->add_control(
				'title_heading',
				[
					'label'		=> __( 'Title', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator'	=> 'before',
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'title',
					'label' 	=> __( 'Title', 'elementor-extras' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
					],
					'selector' 	=> '{{WRAPPER}} .ee-scroll-indicator__element__title',
				]
			);

			$this->add_responsive_control(
				'title_spacing',
				[
					'label' 	=> __( 'Spacing', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default'	=> [
						'size' 	=> 0,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 100,
							'step'	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-scroll-indicator__element__title' => 'margin-bottom: {{SIZE}}px;',
					],
				]
			);

			$this->add_control(
				'subtitle_heading',
				[
					'label'		=> __( 'Subtitle', 'elementor-extras' ),
					'type' 		=> \Elementor\Controls_Manager::HEADING,
					'separator'	=> 'before',
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'subtitle',
					'label' 	=> __( 'Subtitle', 'elementor-extras' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_ACCENT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-scroll-indicator__element__subtitle',
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Render widget content
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render() {

		$this->parent->add_render_attribute( 'wrapper', 'class', 'ee-scroll-indicator-show--' . $this->get_instance_value( 'show' ) );

		parent::render();
	}

	/**
	 * Get default nav class
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function get_nav_class() {
		return '';
	}

	/**
	 * Render element item content
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_element_content( $index, $section ) {

		parent::render_element_content( $index, $section );

		$settings 	= $this->parent->get_settings();
		$link_key 	= $this->parent->_get_repeater_setting_key( 'link', 'sections', $index );

		$this->parent->add_render_attribute( [
			$link_key => [
				'class' => [
					'ee-scroll-indicator__element__link',
				],
			],
		] );

		if ( 'yes' === $settings['click'] ) {
			$this->parent->add_render_attribute( $link_key, 'class', 'has--cursor' );
		}

		?>
		<a <?php echo $this->parent->get_render_attribute_string( $link_key ); ?>>
			<?php
				$this->render_element_bullet( $index, $section );
				$this->render_element_text( $index, $section );
			?>
		</a>
		<?php
	}

	/**
	 * Render bullet markup
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_element_bullet( $index, $section ) {
		parent::render_element_bullet( $index, $section );
	}

	/**
	 * Render text markup
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_element_text( $index, $section ) {
		parent::render_element_text( $index, $section );
	}
}