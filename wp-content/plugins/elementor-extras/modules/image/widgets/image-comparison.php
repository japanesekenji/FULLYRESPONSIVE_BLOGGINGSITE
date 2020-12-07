<?php
namespace ElementorExtras\Modules\Image\Widgets;

// Extras for Elementor Classes
use ElementorExtras\Base\Extras_Widget;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Image_Comparison
 *
 * @since 0.1.0
 */
class Image_Comparison extends Extras_Widget {

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  0.1.0
	 * @return string
	 */
	public function get_name() {
		return 'image-comparison';
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
		return __( 'Image Comparison', 'elementor-extras' );
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
		return 'nicon nicon-image-comparison';
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
			'image-comparison',
			'jquery-mobile',
			'gsap-js',
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
			'section_buttons',
			[
				'label' => __( 'Images & Labels', 'elementor-extras' ),
			]
		);

			$this->add_control(
				'original_image',
				[
					'label' 	=> __( 'Choose Original Image', 'elementor-extras' ),
					'type' 		=> Controls_Manager::MEDIA,
					'dynamic'	=> [ 'active' => true ],
					'default' 	=> [
						'url' 	=> Utils::get_placeholder_image_src(),
					],
				]
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name' 		=> 'original_image', // Actually its `original_image_size`.
					'label' 	=> __( 'Image Size', 'elementor-extras' ),
					'default' 	=> 'full',
				]
			);

			$this->add_control(
				'modified_image',
				[
					'label' 	=> __( 'Choose Modified Image', 'elementor-extras' ),
					'type' 		=> Controls_Manager::MEDIA,
					'dynamic'	=> [ 'active' => true ],
					'default' 	=> [
						'url' 	=> Utils::get_placeholder_image_src(),
					],
					'condition'		=> [
						'original_image[url]!' => '',
					]
				]
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name' 		=> 'modified_image', // Actually its `modified_image_size`.
					'label' 	=> __( 'Image Size', 'elementor-extras' ),
					'default' 	=> 'full',
				]
			);

			$this->add_control(
				'original_label',
				[
					'label'			=> __( 'Original Label', 'elementor-extras' ),
					'type' 			=> Controls_Manager::TEXT,
					'default'		=> __( 'Original', 'elementor-extras' ),
					'placeholder'	=> __( 'Original', 'elementor-extras' ),
					'dynamic'		=> [ 'active' => true ],
					'condition'		=> [
						'original_image[url]!' => '',
					]
				]
			);

			$this->add_control(
				'modified_label',
				[
					'label'			=> __( 'Modified Label', 'elementor-extras' ),
					'type' 			=> Controls_Manager::TEXT,
					'default'		=> __( 'Modified', 'elementor-extras' ),
					'placeholder'	=> __( 'Modified', 'elementor-extras' ),
					'dynamic'		=> [ 'active' => true ],
					'condition'		=> [
						'modified_image[url]!' => '',
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

			$this->add_control(
				'entrance_animation',
				[
					'label' 		=> __( 'Animate on entrance', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'yes',
					'frontend_available' => true
				]
			);

			$this->add_control(
				'click_to_move',
				[
					'label' 		=> __( 'Enable click', 'elementor-extras' ),
					'description'	=> __( 'Click to move separator to a particular position.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'yes',
					'frontend_available' => true
				]
			);

			$this->add_control(
				'click_labels',
				[
					'label' 		=> __( 'Click Labels', 'elementor-extras' ),
					'description'	=> __( 'Click on a label to uncover the corresponding image.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'yes',
					'condition'		=> [
						'click_to_move!' => ''
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'click_animate',
				[
					'label' 		=> __( 'Animate Move', 'elementor-extras' ),
					'description'	=> __( 'Animate separator position on click or change position instantly.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'yes',
					'condition'		=> [
						'click_to_move!' => ''
					],
					'frontend_available' => true
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_images_style',
			[
				'label' => __( 'Images', 'elementor-extras' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs( 'tabs_images_style' );

			$this->start_controls_tab( 'tab_image_original', [ 'label' => __( 'Original', 'elementor-extras' ) ] );

				$this->add_group_control(
					Group_Control_Css_Filter::get_type(),
					[
						'name' => 'image_original_css_filters',
						'selector' => '{{WRAPPER}} .ee-image-comparison > img',
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'tab_image_modified', [ 'label' => __( 'Modified', 'elementor-extras' ) ] );

				$this->add_group_control(
					Group_Control_Css_Filter::get_type(),
					[
						'name' => 'image_modified_css_filters',
						'selector' => '{{WRAPPER}} .ee-image-comparison__image img',
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_labels_style',
			[
				'label' => __( 'Labels', 'elementor-extras' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'labels_spacing',
				[
					'label' => __( 'Distance (em)', 'elementor-extras' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 0,
					],
					'range' => [
						'px' => [
							'min' 	=> 0,
							'max' 	=> 10,
							'step'	=> 0.1
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-image-comparison__label' => 'margin: 0 {{SIZE}}em {{SIZE}}em 0;',
						'{{WRAPPER}} .ee-image-comparison__image .ee-image-comparison__label' => 'margin: 0 0 {{SIZE}}em {{SIZE}}em',

						'{{WRAPPER}}.ee-image-comparison--middle .ee-image-comparison__label' => 'margin: 0 {{SIZE}}em 0 0;',
						'{{WRAPPER}}.ee-image-comparison--middle .ee-image-comparison__image .ee-image-comparison__label' => 'margin: 0 0 0 {{SIZE}}em',

						'{{WRAPPER}}.ee-image-comparison--top .ee-image-comparison__label' => 'margin: {{SIZE}}em {{SIZE}}em 0 0;',
						'{{WRAPPER}}.ee-image-comparison--top .ee-image-comparison__image .ee-image-comparison__label' => 'margin: {{SIZE}}em 0 0 {{SIZE}}em',
					],
				]
			);

			$this->add_control(
				'labels_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-image-comparison__label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'labels_vertical_align',
				[
					'label' 		=> __( 'Vertical Alignment', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> '',
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
					],
					'prefix_class'		=> 'ee-image-comparison--'
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'labels_border',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'selector' 	=> '{{WRAPPER}} .ee-image-comparison .ee-image-comparison__label',
				]
			);

			$this->add_control(
				'labels_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-image-comparison .ee-image-comparison__label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				[
					'name' 		=> 'labels_shadow',
					'selector' 	=> '{{WRAPPER}} .ee-image-comparison .ee-image-comparison__label',
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'labels_box_shadow',
					'selector' => '{{WRAPPER}} .ee-image-comparison .ee-image-comparison__label',
					'separator'	=> '',
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'labels_typography',
					'selector' 	=> '{{WRAPPER}} .ee-image-comparison .ee-image-comparison__label',
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
				]
			);

			$this->start_controls_tabs( 'tabs_label_style' );

			$this->start_controls_tab( 'tab_label_original', [ 'label' => __( 'Original', 'elementor-extras' ) ] );

				$this->add_control(
					'label_original_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-image-comparison__label--original' => 'color: {{VALUE}};',
						],
						'default'	=> '#FFFFFF',
					]
				);

				$this->add_control(
					'label_original_background',
					[
						'label' 	=> __( 'Background color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-image-comparison__label--original' => 'background-color: {{VALUE}};',
						],
						'global' => [
							'default' => Global_Colors::COLOR_PRIMARY,
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'tab_label_modified', [ 'label' => __( 'Modified', 'elementor-extras' ) ] );

				$this->add_control(
					'label_modified_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-image-comparison__label--modified' => 'color: {{VALUE}};',
						],
						'default'	=> '#FFFFFF',
					]
				);

				$this->add_control(
					'label_modified_background',
					[
						'label' 	=> __( 'Background color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-image-comparison__label--modified' => 'background-color: {{VALUE}};',
						],
						'default'	=> '',
						'global' => [
							'default' => Global_Colors::COLOR_PRIMARY,
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_separator_style',
			[
				'label' => __( 'Separator', 'elementor-extras' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'separator_position',
				[
					'label' => __( 'Position', 'elementor-extras' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 50,
					],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'frontend_available' => true,
					'selectors' => [
						'{{WRAPPER}} .is--visible .ee-image-comparison__image' => 'width: {{SIZE}}%;',
						'{{WRAPPER}} .is--visible .ee-image-comparison__handle' => 'left: {{SIZE}}%;',
					],
				]
			);

			$this->add_control(
				'separator_size',
				[
					'label' => __( 'Size (%)', 'elementor-extras' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 100,
						'unit' => '%',
					],
					'size_units' => [ '%' ],
					'range' => [
						'%' => [
							'min' => 1,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-image-comparison__separator' => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' 		=> 'separator_background_color',
					'types' 	=> [ 'classic', 'gradient' ],
					'selector' 	=> '{{WRAPPER}} .ee-image-comparison__separator',
					'default'	=> 'classic',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_handle_style',
			[
				'label' => __( 'Handle', 'elementor-extras' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->start_controls_tabs( 'tabs_handle_style' );

			$this->start_controls_tab(
				'tab_handle_normal',
				[
					'label' => __( 'Normal', 'elementor-extras' ),
				]
			);

				$this->add_control(
					'handle_background_color',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-image-comparison__handle' => 'background-color: {{VALUE}};',
						],
						'global' => [
							'default' => Global_Colors::COLOR_PRIMARY,
						],
					]
				);

				$this->add_control(
					'handle_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-image-comparison__handle' => 'color: {{VALUE}};',
						],
						'default'	=> '#FFFFFF',
					]
				);

				$this->add_control(
					'handle_size',
					[
						'label' 	=> __( 'Size (%)', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SLIDER,
						'default' 	=> [
							'size' 	=> 44,
						],
						'range' 	=> [
							'px' 	=> [
								'min' 	=> 10,
								'max' 	=> 100,
							],
						],
						'separator'		=> 'none',
						'selectors' 	=> [
							'{{WRAPPER}} .ee-image-comparison__handle' => 'font-size: {{SIZE}}px; width: {{SIZE}}px; height: {{SIZE}}px; margin-left: calc(-{{SIZE}}px/2); margin-top: calc(-{{SIZE}}px/2);',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_handle_hover',
				[
					'label' => __( 'Hover', 'elementor-extras' ),
				]
			);

				$this->add_control(
					'handle_hover_background_color',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default' 	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-image-comparison__handle:hover' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'handle_hover_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default' 	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-image-comparison__handle:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'handle_size_hover',
					[
						'label' 	=> __( 'Size (%)', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SLIDER,
						'default' 	=> [
							'size' 	=> 44,
						],
						'range' 	=> [
							'px' 	=> [
								'min' 	=> 10,
								'max' 	=> 100,
							],
						],
						'separator'		=> 'none',
						'selectors' 	=> [
							'{{WRAPPER}} .ee-image-comparison__handle:hover' => 'font-size: {{SIZE}}px; width: {{SIZE}}px; height: {{SIZE}}px; margin-left: calc(-{{SIZE}}px/2); margin-top: calc(-{{SIZE}}px/2);',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_handle_dragged',
				[
					'label' => __( 'Dragged', 'elementor-extras' ),
				]
			);

				$this->add_control(
					'handle_dragged_background_color',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default' 	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-image-comparison__handle.draggable' => 'background-color: {{VALUE}};',
						],
						'global' => [
							'default' => Global_Colors::COLOR_SECONDARY,
						],
					]
				);

				$this->add_control(
					'handle_dragged_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default' 	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-image-comparison__handle.draggable' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'handle_size_dragged',
					[
						'label' 	=> __( 'Size (%)', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SLIDER,
						'default' 	=> [
							'size' 	=> 44,
						],
						'range' 	=> [
							'px' 	=> [
								'min' 	=> 10,
								'max' 	=> 100,
							],
						],
						'separator'		=> 'none',
						'selectors' 	=> [
							'{{WRAPPER}} .ee-image-comparison__handle.draggable' => 'font-size: {{SIZE}}px; width: {{SIZE}}px; height: {{SIZE}}px; margin-left: calc(-{{SIZE}}px/2); margin-top: calc(-{{SIZE}}px/2);',
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
	 * @since  0.1.0
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( [
			'wrapper' => [
				'class' => 'ee-image-comparison',
			],
			'modified-image-wrapper' => [
				'class' => 'ee-image-comparison__image',
			],
			'original-label' => [
				'class' => [
					'ee-image-comparison__label',
					'ee-image-comparison__label--original',
				],
				'data-type' => 'original',
			],
			'modified-label' => [
				'class' => [
					'ee-image-comparison__label',
					'ee-image-comparison__label--modified',
				],
				'data-type' => 'modified',
			],
			'separator' => [
				'class' => [
					'ee-image-comparison__separator',
					'ee-center-vertical',
				],
			],
			'handle' => [
				'class' => [
					'ee-image-comparison__handle',
					'nicon',
					'nicon-resize-horizontal-filled',
				],
			],
		] );

		if ( '' === $settings['original_label'] ) {
			$this->add_render_attribute( 'original-label', 'class', 'ee-image-comparison__label--empty' );
		}

		if ( 'yes' === $settings['click_labels'] ) {
			$this->add_render_attribute( 'original-label', 'class', 'ee-image-comparison__label--clickable' );
		}

		if ( '' === $settings['modified_label'] ) {
			$this->add_render_attribute( 'modified-label', 'class', 'ee-image-comparison__label--empty' );
		}

		if ( 'yes' === $settings['click_labels'] ) {
			$this->add_render_attribute( 'modified-label', 'class', 'ee-image-comparison__label--clickable' );
		}

		?><figure <?php echo $this->get_render_attribute_string( 'wrapper' ) ?>>
			
			<?php if ( ! empty( $settings['original_image']['url'] ) ) {
				echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'original_image' );
			} ?>

			<span <?php echo $this->get_render_attribute_string( 'original-label' ) ?>>
				<?php if ( '' !== $settings['original_label'] ) { echo $settings['original_label']; } ?>
			</span>
		
			<div <?php echo $this->get_render_attribute_string( 'modified-image-wrapper' ) ?>>
				
				<?php if ( ! empty( $settings['modified_image']['url'] ) ) {
					echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'modified_image' );
				} ?>

				<span <?php echo $this->get_render_attribute_string( 'modified-label' ) ?>>
					<?php if ( '' !== $settings['modified_label'] ) { echo $settings['modified_label']; } ?>
				</span>

				<span <?php echo $this->get_render_attribute_string( 'separator' ) ?>></span>
			</div>
		
			<span <?php echo $this->get_render_attribute_string( 'handle' ) ?>></span>
		</figure><?php
	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering
	 *
	 * @since  0.1.0
	 * @return void
	 */
	protected function _content_template() { ?><#

		var original_image = {
				id: settings.original_image.id,
				url: settings.original_image.url,
				size: settings.original_image_size,
				dimension: settings.original_image_custom_dimension,
				model: view.getEditModel()
			},

			modified_image = {
				id: settings.modified_image.id,
				url: settings.modified_image.url,
				size: settings.modified_image_size,
				dimension: settings.modified_image_custom_dimension,
				model: view.getEditModel()
			};

		view.addRenderAttribute( {
			'wrapper' : {
				'class' : [
					'ee-image-comparison',
				],
			},
			'modified-image-wrapper' : {
				'class' : [
					'ee-image-comparison__image',
				],
			},
			'original-label' : {
				'class' : [
					'ee-image-comparison__label',
					'ee-image-comparison__label--original',
				],
				'data-type' : 'original',
			},
			'modified-label' : {
				'class' : [
					'ee-image-comparison__label',
					'ee-image-comparison__label--modified',
				],
				'data-type' : 'modified',
			},
			'separator' : {
				'class' : [
					'ee-image-comparison__separator',
					'ee-center-vertical',
				],
			},
			'handle' : {
				'class' : [
					'ee-image-comparison__handle',
					'nicon',
					'nicon-resize-horizontal-filled',
				],
			},
			'original-image' : {
				'src' : elementor.imagesManager.getImageUrl( original_image ),
			},
			'modified-image' : {
				'src' : elementor.imagesManager.getImageUrl( modified_image ),
			},
		} );

		if ( '' === settings.original_label ) {
			view.addRenderAttribute( 'original-label', 'class', 'ee-image-comparison__label--empty' );
		}

		if ( 'yes' === settings.click_labels ) {
			view.addRenderAttribute( 'original-label', 'class', 'ee-image-comparison__label--clickable' );
		}

		if ( '' === settings.modified_label ) {
			view.addRenderAttribute( 'modified-label', 'class', 'ee-image-comparison__label--empty' );
		}

		if ( 'yes' === settings.click_labels ) {
			view.addRenderAttribute( 'modified-label', 'class', 'ee-image-comparison__label--clickable' );
		}

		#><figure {{{ view.getRenderAttributeString( 'wrapper' ) }}}>

		<# if ( settings.original_image.url ) { #>
			<img {{{ view.getRenderAttributeString( 'original-image' ) }}}>

			<span {{{ view.getRenderAttributeString( 'original-label' ) }}}><# if ( '' !== settings.original_label ) { #>{{{ settings.original_label }}}<# } #></span>
		<# } #>

		<# if ( settings.modified_image.url ) { #>
			<div {{{ view.getRenderAttributeString( 'modified-image-wrapper' ) }}}>
				<img {{{ view.getRenderAttributeString( 'modified-image' ) }}}>

				<span {{{ view.getRenderAttributeString( 'modified-label' ) }}}><# if ( '' !== settings.modified_label ) { #>{{{ settings.modified_label }}}<# } #></span>

				<span {{{ view.getRenderAttributeString( 'separator' ) }}}></span>
			</div>
		<# } #>

			<span {{{ view.getRenderAttributeString( 'handle' ) }}}></span>
		</figure><?php
	}
}
