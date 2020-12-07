<?php
namespace ElementorExtras\Modules\Breadcrumbs\Widgets;

// Extras for Elementor Classes
use ElementorExtras\Base\Extras_Widget;
use ElementorExtras\Utils;

// Elementor Classes
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
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
 * Breadcrumbs
 *
 * @since 1.2.0
 */
class Breadcrumbs extends Extras_Widget {

	/**
	 * Crumbs
	 *
	 * @since  2.2.8
	 * @var    string
	 */
	protected $crumbs = [];

	/**
	 * Separator
	 *
	 * @since  1.2.0
	 * @var    string
	 */
	private $_separator = null;

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  1.2.0
	 * @return string
	 */
	public function get_name() {
		return 'ee-breadcrumbs';
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
		return __( 'Breadcrumbs', 'elementor-extras' );
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
		return 'nicon nicon-breadcrumbs';
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
				'label' => __( 'Display', 'elementor-extras' ),
			]
		);

			$this->add_control(
				'source',
				[
					'label' 	=> __( 'Query Source', 'elementor-extras' ),
					'type'		=> Controls_Manager::SELECT,
					'default'	=> '',
					'options'	=> [
						''		=> __( 'Current Query', 'elementor-extras' ),
						'id'	=> __( 'Custom Selection', 'elementor-extras' ),
					]
				]
			);

			$this->add_control(
				'source_id',
				[
					'label' 		=> __( 'Page or Post', 'elementor-extras' ),
					'type' 			=> 'ee-query',
					'query_type' 	=> 'posts',
					'label_block' 	=> false,
					'multiple' 		=> false,
					'condition'		=> [
						'source'	=> 'id',
					],
				]
			);

			$this->add_control(
				'show_home',
				[
					'label' 		=> __( 'Show Home', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'yes',
				]
			);

			$this->add_control(
				'show_current',
				[
					'label' 		=> __( 'Show Current', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'return_value' 	=> 'yes',
				]
			);

			$this->add_control(
				'structured_data',
				[
					'label' 		=> __( 'Add Structured Data', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
				]
			);

			$this->add_control(
				'home_text',
				[
					'label' 		=> __( 'Home Text', 'elementor-extras' ),
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> __( 'Homepage', 'elementor-extras' ),
					'dynamic'		=> [
						'active'	=> true,
						'categories' => [ TagsModule::POST_META_CATEGORY ]
					],
					'condition'		=> [
						'show_home' => 'yes'
					],
				]
			);

		$this->end_controls_section();

		$post_types = Utils::get_public_post_types_options( true, false );

		foreach ( $post_types as $post_type => $label ) {

			$this->start_controls_section(
				'section_single_' . $post_type,
				[
					'label' => sprintf( __( 'Single %s', 'elementor-extras' ), $label ),
				]
			);

				$this->add_control(
					'single_' . $post_type . '_show_home',
					[
						'label' 		=> __( 'Show Home', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SWITCHER,
						'default' 		=> 'yes',
						'label_on' 		=> __( 'Yes', 'elementor-extras' ),
						'label_off' 	=> __( 'No', 'elementor-extras' ),
						'return_value' 	=> 'yes',
						'condition' 	=> [
							'show_home!' => '',
						],
					]
				);

				$this->add_control(
					'single_' . $post_type . '_show_cpt',
					[
						'label' 		=> __( 'Show Post Type', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SWITCHER,
						'default' 		=> 'yes',
						'label_on' 		=> __( 'Yes', 'elementor-extras' ),
						'label_off' 	=> __( 'No', 'elementor-extras' ),
						'return_value' 	=> 'yes',
					]
				);

				$taxonomies_options = Utils::get_taxonomies_options( $post_type );

				foreach ( $taxonomies_options as $taxonomy => $label ) {
					if ( $taxonomy && ! is_taxonomy_hierarchical( $taxonomy ) ) {
						unset( $taxonomies_options[ $taxonomy ] );
					}
				}

				$tax_options_default = ( $taxonomies_options && ! empty( $taxonomies_options[0] ) ) ? array_keys( $taxonomies_options )[0] : '';

				$this->add_control(
					'single_' . $post_type . '_show_terms',
					[
						'label'		=> __( 'Taxonomy', 'elementor-extras' ),
						'type' 		=> Controls_Manager::SELECT,
						'default' 	=> $tax_options_default,
						'options' 	=> array_merge( [
							'' => __( 'None', 'elementor-extras' ),
						], $taxonomies_options ),
					]
				);

				if ( is_post_type_hierarchical( $post_type ) ) {
					$this->add_control(
						'single_' . $post_type . '_show_parents',
						[
							'label' 		=> __( 'Show Parents', 'elementor-extras' ),
							'type' 			=> Controls_Manager::SWITCHER,
							'default' 		=> 'yes',
							'label_on' 		=> __( 'Yes', 'elementor-extras' ),
							'label_off' 	=> __( 'No', 'elementor-extras' ),
							'return_value' 	=> 'yes',
						]
					);
				}

				$this->add_control(
					'single_' . $post_type . '_show_current',
					[
						'label' 		=> __( 'Show Current', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SWITCHER,
						'default' 		=> 'yes',
						'label_on' 		=> __( 'Yes', 'elementor-extras' ),
						'label_off' 	=> __( 'No', 'elementor-extras' ),
						'return_value' 	=> 'yes',
						'condition' 	=> [
							'show_current!' => '',
						],
					]
				);

			$this->end_controls_section();

		}

		$taxonomies = Utils::get_taxonomies_options();

		foreach ( $taxonomies as $taxonomy => $label ) {

			$this->start_controls_section(
				'section_taxonomy_' . $taxonomy,
				[
					'label' => sprintf( __( '%s Archive', 'elementor-extras' ), $label ),
				]
			);

				$this->add_control(
					'taxonomy_' . $taxonomy . '_show_home',
					[
						'label' 		=> __( 'Show Home', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SWITCHER,
						'default' 		=> 'yes',
						'label_on' 		=> __( 'Yes', 'elementor-extras' ),
						'label_off' 	=> __( 'No', 'elementor-extras' ),
						'return_value' 	=> 'yes',
						'condition' 	=> [
							'show_home!' => '',
						],
					]
				);

				$this->add_control(
					'taxonomy_' . $taxonomy . '_show_cpt',
					[
						'label' 		=> __( 'Show Post Type', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SWITCHER,
						'default' 		=> 'category' === $taxonomy || 'post_tag' === $taxonomy ? '' : 'yes',
						'label_on' 		=> __( 'Yes', 'elementor-extras' ),
						'label_off' 	=> __( 'No', 'elementor-extras' ),
						'return_value' 	=> 'yes',
					]
				);

				$this->add_control(
					'taxonomy_' . $taxonomy . '_show_taxonomy',
					[
						'label' 		=> __( 'Show Taxonomy', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SWITCHER,
						'default' 		=> 'category' === $taxonomy || 'post_tag' === $taxonomy ? 'yes' : '',
						'label_on' 		=> __( 'Yes', 'elementor-extras' ),
						'label_off' 	=> __( 'No', 'elementor-extras' ),
						'return_value' 	=> 'yes',
					]
				);

				$this->add_control(
					'taxonomy_' . $taxonomy . '_taxonomy_link',
					[
						'label' 		=> __( 'Taxonomy Link', 'elementor-extras' ),
						'type' 			=> Controls_Manager::URL,
						'placeholder' 	=> esc_url( home_url( '/' ) ),
						'dynamic'		=> [ 'active' => true ],
						'label_block' 	=> false,
						'condition'		=> [
							'taxonomy_' . $taxonomy . '_show_taxonomy!' => '',
						],
					]
				);

				if ( is_taxonomy_hierarchical( $taxonomy ) ) {
					$this->add_control(
						'taxonomy_' . $taxonomy . '_show_parents',
						[
							'label' 		=> sprintf( __( 'Show Parent %s', 'elementor-extras' ), $label ),
							'type' 			=> Controls_Manager::SWITCHER,
							'default' 		=> 'yes',
							'label_on' 		=> __( 'Yes', 'elementor-extras' ),
							'label_off' 	=> __( 'No', 'elementor-extras' ),
							'return_value' 	=> 'yes',
						]
					);
				}

				$this->add_control(
					'taxonomy_' . $taxonomy . '_show_current',
					[
						'label' 		=> __( 'Show Current', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SWITCHER,
						'default' 		=> 'yes',
						'label_on' 		=> __( 'Yes', 'elementor-extras' ),
						'label_off' 	=> __( 'No', 'elementor-extras' ),
						'return_value' 	=> 'yes',
						'condition' 	=> [
							'show_current!' => '',
						],
					]
				);

			$this->end_controls_section();

		}

		$custom_post_types = Utils::get_public_post_types_options( true, false, [
			'public'   => true,
       		'_builtin' => false,
		] );

		if ( $custom_post_types ) {
			foreach ( $custom_post_types as $post_type => $label ) {

				$this->start_controls_section(
					'section_cpt_' . $post_type,
					[
						'label' => sprintf( __( '%s Archive', 'elementor-extras' ), $label ),
					]
				);

					$this->add_control(
						'cpt_' . $post_type . '_show_home',
						[
							'label' 		=> __( 'Show Home', 'elementor-extras' ),
							'type' 			=> Controls_Manager::SWITCHER,
							'default' 		=> 'yes',
							'label_on' 		=> __( 'Yes', 'elementor-extras' ),
							'label_off' 	=> __( 'No', 'elementor-extras' ),
							'return_value' 	=> 'yes',
							'condition' 	=> [
								'show_home!' => '',
							],
						]
					);

					$this->add_control(
						'cpt_' . $post_type . '_show_current',
						[
							'label' 		=> __( 'Show Current', 'elementor-extras' ),
							'type' 			=> Controls_Manager::SWITCHER,
							'default' 		=> 'yes',
							'label_on' 		=> __( 'Yes', 'elementor-extras' ),
							'label_off' 	=> __( 'No', 'elementor-extras' ),
							'return_value' 	=> 'yes',
							'condition' 	=> [
								'show_current!' => '',
							],
						]
					);

				$this->end_controls_section();

			}
		}

		$this->start_controls_section(
			'section_separator',
			[
				'label' => __( 'Separator', 'elementor-extras' ),
			]
		);

			$this->add_control(
				'separator_type',
				[
					'label'		=> __( 'Type', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'icon',
					'options' 	=> [
						'text' 		=> __( 'Text', 'elementor-extras' ),
						'icon' 		=> __( 'Icon', 'elementor-extras' ),
					],
				]
			);

			$this->add_control(
				'separator_text',
				[
					'label' 		=> __( 'Text', 'elementor-extras' ),
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> __( '>', 'elementor-extras' ),
					'condition'		=> [
						'separator_type' => 'text'
					],
				]
			);

			$this->add_control(
				'selected_separator_icon',
				[
					'label' => __( 'Icon', 'elementor-extras' ),
					'type' => Controls_Manager::ICONS,
					'fa4compatibility' => 'separator_icon',
					'condition'		=> [
						'separator_type' => 'icon'
					],
					'default' => [
						'value' => 'fas fa-angle-right',
						'library' => 'fa-solid',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_item_style',
			[
				'label' 	=> __( 'Crumbs', 'elementor-extras' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'items_align',
				[
					'label' 		=> __( 'Align Crumbs', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> '',
					'options' 		=> [
						'left' 			=> [
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
						'stretch' 		=> [
							'title' 	=> __( 'Stretch', 'elementor-extras' ),
							'icon' 		=> 'eicon-h-align-stretch',
						],
					],
					'prefix_class' 	=> 'ee-breadcrumbs-align%s-',
				]
			);

			$this->add_responsive_control(
				'items_text_align',
				[
					'label' 		=> __( 'Align Text', 'elementor-extras' ),
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
						'{{WRAPPER}} .ee-breadcrumbs' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'item_spacing',
				[
					'label' 	=> __( 'Spacing', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default'	=> [
						'size'	=> 12
					],
					'range' 	=> [
						'px' 	=> [
							'max' => 36,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-breadcrumbs' => 'margin-left: -{{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ee-breadcrumbs__item' => 'margin-left: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ee-breadcrumbs__separator' => 'margin-left: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'item_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-breadcrumbs__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'allowed_dimensions' => [ 'right', 'left' ],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'item_border',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'selector' 	=> '{{WRAPPER}} .ee-breadcrumbs__item',
				]
			);

			$this->add_control(
				'item_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-breadcrumbs__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'item_typography',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-breadcrumbs__text',
				]
			);

			$this->start_controls_tabs( 'crumb_style' );

			$this->start_controls_tab( 'crumb_default', [ 'label' => __( 'Default', 'elementor-extras' ) ] );

				$this->add_control(
					'item_background_color',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-breadcrumbs__item' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'item_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'global' => [
							'default' => Global_Colors::COLOR_ACCENT,
						],
						'selectors' => [
							'{{WRAPPER}} .ee-breadcrumbs__item' => 'color: {{VALUE}};',
							'{{WRAPPER}} .ee-breadcrumbs__item a' => 'color: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'crumb_hover', [ 'label' => __( 'Hover', 'elementor-extras' ) ] );

				$this->add_control(
					'item_background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-breadcrumbs__item:hover' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'item_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-breadcrumbs__item:hover' => 'color: {{VALUE}};',
							'{{WRAPPER}} .ee-breadcrumbs__item:hover a' => 'color: {{VALUE}};',
						],
					]
				);
			
			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_separator_style',
			[
				'label' 	=> __( 'Separators', 'elementor-extras' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'separator_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-breadcrumbs__separator' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'allowed_dimensions' => [ 'right', 'left' ],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'separator_border',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'selector' 	=> '{{WRAPPER}} .ee-breadcrumbs__separator',
				]
			);

			$this->add_control(
				'separator_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-breadcrumbs__separator' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'separator_background_color',
				[
					'label' 	=> __( 'Background Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ee-breadcrumbs__separator' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'separator_color',
				[
					'label' 	=> __( 'Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'{{WRAPPER}} .ee-breadcrumbs__separator' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'separator_typography',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'selector' 	=> '{{WRAPPER}} .ee-breadcrumbs__separator',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_current_style',
			[
				'label' 	=> __( 'Current', 'elementor-extras' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'current_border',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'selector' 	=> '{{WRAPPER}} .ee-breadcrumbs__item--current',
				]
			);

			$this->add_control(
				'current_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-breadcrumbs__item--current' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'current_background_color',
				[
					'label' 	=> __( 'Background Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ee-breadcrumbs__item--current' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'current_color',
				[
					'label' 	=> __( 'Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'global' => [
						'default' => Global_Colors::COLOR_TEXT,
					],
					'selectors' => [
						'{{WRAPPER}} .ee-breadcrumbs__item--current' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'current_typography',
					'label' 	=> __( 'Typography', 'elementor-extras' ),
					'selector' 	=> '{{WRAPPER}} .ee-breadcrumbs__item--current .ee-breadcrumbs__text',
				]
			);

		$this->end_controls_section();

	}

	/**
	 * Get Query
	 *
	 * @since  1.2.0
	 * @return \WP_Query|bool
	 */
	protected function get_query() {
		global $post;

		$settings 	= $this->get_settings_for_display();
		$_id 		= null;
		$_post_type = 'post';

		if ( 'id' === $settings['source'] && '' !== $settings['source_id'] ) {

			$_id = $settings['source_id'];
			$_post_type = 'any';

			$_args = array(
				'p' 		=> $_id,
				'post_type' => $_post_type,
			);

			// Create custom query
			$_post_query = new \WP_Query( $_args );

			return $_post_query;
		}

		return false;
	}

	/**
	 * Set Separator
	 *
	 * Sets the markup for the breadcrumbs separator
	 *
	 * @since  1.2.0
	 * @return string
	 */
	protected function set_separator() {

		$settings = $this->get_settings_for_display();
		$separator = '';
		$has_icon = ! empty( $settings['separator_icon'] ) || ! empty( $settings['selected_separator_icon']['value'] );

		if ( 'icon' === $settings['separator_type'] && $has_icon ) {
			$migrated = isset( $settings['__fa4_migrated']['selected_separator_icon'] );
			$is_new = empty( $settings['icon'] ) && Icons_Manager::is_migration_allowed();

			$this->add_render_attribute( 'icon-wrapper', 'class', [
				'ee-icon',
				'ee-icon-support--svg',
			] );

			$separator .= '<span ' . $this->get_render_attribute_string( 'icon-wrapper' ) . '>';
			
			if ( $is_new || $migrated ) {
				ob_start();
				Icons_Manager::render_icon( $settings['selected_separator_icon'], [ 'aria-hidden' => 'true' ] );
				$separator .= ob_get_clean();
			} else {
				$this->add_render_attribute( 'icon', [
					'class' => $settings['separator_icon'],
					'aria-hidden' => 'true',
				] );
				$separator .= '<i '. $this->get_render_attribute_string('icon') . '></i>';
			}

			$separator .= '</span>';
		} else {
			$this->add_inline_editing_attributes( 'separator_text' );
			$this->add_render_attribute( 'separator_text', 'class', 'ee-breadcrumbs__separator__text' );

			/**
			 * Separator Text filter
			 *
			 * Filters the separator text if the separator is set as text
			 * in the widget settings
			 *
			 * @since 2.2.0
			 * @param string 			$separator_text 	The separator text setting
			 */
			$separator_text = apply_filters( 'elementor_extras/widgets/breadcrumbs/separator/text', $settings['separator_text'] );
			$separator = '<span ' . $this->get_render_attribute_string( 'separator_text' ) . '>' . $separator_text . '</span>';
		}

		$this->_separator = $separator;
	}

	/**
	 * Get Separator
	 *
	 * @since  1.2.0
	 * @return var\string
	 */
	protected function get_separator() {
		return $this->_separator;
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

		$settings 	= $this->get_settings_for_display();
		$_query 	= $this->get_query();

		$this->set_separator();
		$this->add_render_attribute( 'breadcrumbs', 'class', 'ee-breadcrumbs' );

		if ( $settings['structured_data'] ) {
			$this->add_render_attribute( 'breadcrumbs', [
				'itemscope' => "",
				'itemtype' => "http://schema.org/BreadcrumbList",
			] );
		}

		if ( $_query ) {
			if ( $_query->have_posts() ) {

				// Setup post
				$_query->the_post();

				/**
				 * Post Query Filter
				 *
				 * Filters the post query when breadcrumbs are set for a specific page
				 *
				 * @since 2.2.0
				 * @param WP_Query 	$_query 	The current query
				 */
				$_query = apply_filters( 'elementor_extras/widgets/breadcrumbs/query', $_query );

				// Render using the new query
				$this->render_breadcrumbs( $_query );

				// Reset post data to original query
				wp_reset_postdata();
				wp_reset_query();

			} else {

				_e( 'Post or page not found', 'elementor-extras' );

			}
		} else {
			// Render using the original query
			$this->render_breadcrumbs();
		}
	}

	/**
	 * Render Separator
	 * 
	 * The markup for the separator item
	 *
	 * @since  1.2.0
	 * @param  bool $output Wether to echo or not the markup
	 * @return void
	 */
	protected function render_separator( $output = true ) {

		$this->add_render_attribute( 'separator', [
			'class' => [
				'ee-breadcrumbs__separator',
			],
		] );

		$separator = $this->get_separator();

		/**
		 * Separator filter
		 *
		 * Filters the separator
		 *
		 * @since 2.2.0
		 * @param string 			$post_title 	The markup for the separator
		 */
		$separator = apply_filters( 'elementor_extras/widgets/breadcrumbs/separator', $separator );
		$markup = sprintf( '<li %1$s>%2$s</li>', $this->get_render_attribute_string( 'separator' ), $separator );

		if ( $output === true ) {
			echo $markup;
		} else {
			return $markup;
		}
	}

	/**
	 * Render Breadcrumbs
	 * 
	 * Identifies and outputs all the breadcrumbs
	 *
	 * @access protected
	 *
	 * @since  	1.2.0
	 * @param 	WP_Query|bool $query Query used to render the breadcrumbs
	 * @return 	void
	 */
	protected function render_breadcrumbs( $query = false ) {

		global $post, $wp_query;

		if ( $query === false ) {

			// Reset post data to parent query
			$wp_query->reset_postdata();

			// Set active query to native query
			$query = $wp_query;
		}

		if ( $query->is_front_page() || $query->is_home() ) {
			return;
		}

		$this->set_crumbs( $query );

		?><ul <?php echo $this->get_render_attribute_string( 'breadcrumbs' ); ?>><?php
			$this->render_crumbs();
		?></ul><?php
	}

	/**
	 * Set Crumbs
	 * 
	 * Checks current query to determine which
	 * crumbs need to be added
	 *
	 * @access private
	 *
	 * @since  2.2.8
	 * @param  WP_Query 	$query 	The query to check
	 * @return void
	 */
	private function set_crumbs( $query ) {

		$is_custom_archive = $query->is_archive() && ! $query->is_tax() && ! $query->is_category() && ! $query->is_tag() && ! $query->is_date() && ! $query->is_author() && ! $query->is_post_type_archive();

		if ( $is_custom_archive ) {

			$this->add_custom_archive();

		} else if ( $query->is_post_type_archive() ) {

			$this->add_post_type_archive();
			
		} else if ( $query->is_archive() && ( $query->is_tax() || $query->is_category() || $query->is_tag() ) ) {

			$this->add_taxonomy_archive();
			
		} else if ( $query->is_single() || $query->is_page() ) {

			$this->add_single( false, $query );
		
		} else if ( $query->is_day() ) {

			$this->add_day();
			
		} else if ( $query->is_month() ) {

			$this->add_month();
			
		} else if ( $query->is_year() ) {

			$this->add_year();
			
		} else if ( $query->is_author() ) {
			
			$this->add_author();
			
		} else if ( $query->is_search() ) {

			$this->add_search();
		
		} elseif ( $query->is_404() ) {

			$this->add_404();

		}
	}

	/**
	 * Add Crumbs
	 * 
	 * Adds a new crumbs to the crumbs list
	 *
	 * @since  2.2.8
	 * @param  string 	$name The name of the crumb
	 * @param  array 	$args The crumbs settings
	 * @return void
	 */
	private function add_crumb( $name, $args = [] ) {
		if ( ! $name ) {
			return;
		}

		$this->crumbs[ $name ] = $args;
	}

	/**
	 * Get Crumb
	 * 
	 * Gets a crumb by its name
	 *
	 * @since  2.2.8
	 * @param  string 	$name The name of the crumb
	 * @return void
	 */
	private function get_crumb( $name ) {
		if ( ! $name ) {
			return;
		}

		return $this->crumbs[ $name ];
	}

	/**
	 * Add Home Link Crumb
	 * 
	 * The markup for the home link crumb
	 *
	 * @since  1.2.0
	 * @return void
	 */
	protected function add_home_link() {
		$settings = $this->get_settings_for_display();

		if ( 'yes' !== $settings['show_home'] ) {
			return;
		}

		$this->add_crumb( 'home', [
			'key' 		=> 'home',
			'content' 	=> $settings['home_text'],
			'link' 		=> get_home_url(),
		] );
	}

	/**
	 * Add Custom Archive Crumbs
	 *
	 * @since  2.2.5
	 * @return void
	 */
	protected function add_custom_archive() {
		$this->add_crumb( 'archive', [
			'key' 		=> 'archive',
			'content' 	=> post_type_archive_title( '', false ),
		] );
	}

	/**
	 * Add Custom Taxonomy Archive Crumbs
	 *
	 * @since  2.2.5
	 * @return void
	 */
	protected function add_taxonomy_archive() {
		
		$term 		= get_queried_object();
		$term_name 	= $term->name;
		$taxonomy 	= $term->taxonomy;
		$prefix 	= 'taxonomy_' . $taxonomy;
		$settings 	= $this->get_settings();

		if ( '' !== $settings[ $prefix . '_show_home'] ) {
			$this->add_home_link();
		}

		if ( 'post' === get_post_type() ) {
			$this->add_blog();
		}

		if ( '' !== $settings[ $prefix . '_show_cpt'] ) {
			$this->add_post_type_archive_link();
		}

		if ( '' !== $settings[ $prefix . '_show_taxonomy'] ) {
			$taxonomy_object = get_taxonomy( $taxonomy );
			$taxonomy_link = $settings[ $prefix . '_taxonomy_link' ];

			$this->add_crumb( 'taxonomy', [
				'key' 		=> 'taxonomy',
				'ids' 		=> [ $taxonomy, $taxonomy_object->name ],
				'content' 	=> $taxonomy_object->labels->name,
				'link'		=> $taxonomy_link['url'] ? $taxonomy_link['url'] : '',
			] );
		}

		if ( ! empty( $settings[ $prefix . '_show_parents'] ) && '' !== $settings[ $prefix . '_show_parents'] ) {
			$this->add_archive_term_parents( $term->term_id, $term->taxonomy );
		}

		/**
		 * Before Specific Last Taxonomy Term
		 *
		 * Fires right before the last specific custom taxonomy archive crumb
		 *
		 * The dynamic portion of the hook name, `$term_name`, refers to the slug of the term.
		 *
		 * @since 2.2.0
		 * @param WP_Term 			$term 	The last term
		 * @param Extras_Widget 	$this 	The current widget instance
		 */
		do_action( "elementor_extras/widgets/breadcrumbs/before/crumb/term/last/{$term_name}", $term, $this );

		if ( '' !== $settings[ $prefix . '_show_current'] ) {
			$this->add_crumb( 'taxonomy-archive', [
				'key' 		=> 'taxonomy-archive',
				'ids' 		=> [ $term->term_id, $term->slug ],
				'content' 	=> $term->name,
				'link'		=> '',
			] );
		}
	}

	/**
	 * Add Single Crumbs
	 *
	 * @since  2.2.5
	 * @return void
	 */
	protected function add_single( $post = false, $query ) {
		if ( ! $post ) { global $post; }

		$settings 	= $this->get_settings();
		$post_type 	= get_post_type();
		$post_types = get_post_types( [ 'show_in_nav_menus' => true, ], 'names' );
		$prefix 	= 'single_' . $post_type;

		if ( ! in_array( $post_type, $post_types ) ) {
			return;
		}

		if ( array_key_exists( $prefix . '_show_home', $settings ) && $settings[ $prefix . '_show_home' ] ) {
			$this->add_home_link();
		}

		/**
		 * Before Single
		 *
		 * Fires right before any single crumb
		 *
		 * @since 2.2.0
		 * @param WP_Post 			$post 		The queried single post
		 * @param Extras_Widget 	$this 		The current widget instance
		 */
		do_action( 'elementor_extras/widgets/breadcrumbs/before/crumb/single', $post, $this );

		if ( 'post' === $post_type ) {
			$this->add_blog();
		}

		if ( '' !== $settings[ $prefix . '_show_cpt'] ) {
			$this->add_post_type_archive_link( $post );
		}

		if ( get_object_taxonomies( $post_type, 'objects' ) ) {
			if ( '' !== $settings[ $prefix . '_show_terms'] ) {
				$this->add_single_terms( $post );
			}
		}

		if ( is_post_type_hierarchical( $post_type ) ) {
			if ( '' !== $settings[ $prefix . '_show_parents'] ) {
				$this->add_single_parents( $post );
			}
		}

		/**
		 * Before Single
		 *
		 * Fires right before any single crumb
		 *
		 * @since 2.2.0
		 * @param WP_Post 			$post 		The queried single post
		 * @param Extras_Widget 	$this 		The current widget instance
		 */
		do_action( 'elementor_extras/widgets/breadcrumbs/before/crumb/single', $post, $this );

		if (  array_key_exists( $prefix . '_show_current', $settings ) && '' !== $settings[ $prefix . '_show_current'] ) {
			$this->add_crumb( 'single', [
				'key' 		=> 'single',
				'ids' 		=> [ $post->ID ],
				'content' 	=> get_the_title(),
			] );
		}
	}

	/**
	 * Add Single Parents Crumbs
	 *
	 * @since  2.2.8
	 * @return void
	 */
	protected function add_single_parents( $post = false ) {
		if ( ! $post ) { global $post; }

		if ( ! $post->post_parent ) {
			return;
		}
				
		$parents = get_post_ancestors( $post->ID );
		$parents = array_reverse( $parents );
			
		if ( ! isset( $parents ) ) $parents = null;

		foreach ( $parents as $parent ) {

			/**
			 * Before Page Crumb
			 *
			 * Fires right before any page crumb.
			 *
			 * @since 2.2.0
			 * @param WP_Post 			$parent 	The page object
			 * @param Extras_Widget 	$this 		The current widget instance
			 */
			do_action( "elementor_extras/widgets/breadcrumbs/before/crumb/page", $parent, $this );

			$this->add_crumb( 'ancestor', [
				'key' 		=> 'ancestor-' . $parent,
				'ids' 		=> [ $parent ],
				'content' 	=> get_the_title( $parent ),
				'link'		=> get_permalink( $parent ),
			] );
		}
	}

	/**
	 * Add Blog Crumb
	 *
	 * @since  2.2.6
	 * @return void
	 */
	protected function add_blog() {
		$posts_page_id = get_option( 'page_for_posts' );

		if ( $posts_page_id ) {
			$blog = get_post( $posts_page_id );

			/**
			 * Before Blog Crumb
			 *
			 * Fires right before a blog main page crumb is rendered
			 *
			 * @since 2.2.0
			 * @param WP_Post 			$blog 	The blog page
			 * @param Extras_Widget 	$this 	The current widget instance
			 */
			do_action( "elementor_extras/widgets/breadcrumbs/before/crumb/single/blog", $blog, $this );

			$this->add_crumb( 'blog', [
				'key' 		=> 'blog',
				'ids' 		=> [ $blog->ID ],
				'content' 	=> $blog->post_title,
				'link'		=> get_permalink( $blog->ID ),
			] );
		}
	}

	/**
	 * Add Post Type Archive Crumbs
	 *
	 * @since  2.2.5
	 * @return void
	 */
	protected function add_post_type_archive( $post = false ) {
		$post_type = get_post_type( $post );
		$settings = $this->get_settings();

		if ( '' !== $settings['cpt_' . $post_type . '_show_home'] ) {
			$this->add_home_link();
		}

		if ( '' !== $settings['cpt_' . $post_type . '_show_current'] ) {
			$this->add_post_type_archive_link( $post );
		}
	}

	/**
	 * Add Post Type Archive Link Crumb
	 *
	 * @since  2.2.8
	 * @return void
	 */
	protected function add_post_type_archive_link( $post = false ) {
		if ( ! $post ) { global $post; }

		$queried_object = get_queried_object();

		if ( is_post_type_archive() ) {
			$post_type = $queried_object->name;
		} elseif ( is_archive() && ( is_tax() || is_category() || is_tag() ) ) {
			$post_type = get_taxonomy( $queried_object->taxonomy )->object_type[0];
		} elseif ( is_single() || is_page() ) {
			$post_type = get_post_type( $post );
		} else {
			return;
		}

		$post_type_object = get_post_type_object( $post_type );

		/**
		 * Before Post Type Archive
		 *
		 * Fires right before a custom post type archive crumb
		 *
		 * @since 2.2.0
		 * @param object 			$post_type_object 	The queried post type object
		 * @param Extras_Widget 	$this 				The current widget instance
		 */
		do_action( "elementor_extras/widgets/breadcrumbs/before/crumb/post_type/{$post_type}", $post_type_object, $this );

		switch ( $post_type ) {
			case 'post':
				$page_for_posts = get_option( 'page_for_posts' );
				$link = $page_for_posts ? get_permalink( get_option( 'page_for_posts' ) ) : esc_url( home_url( '/' ) );
				break;
			case 'page':
				$link = esc_url( home_url( '/' ) );
				break;
			default:
				$link = get_post_type_archive_link( $post_type );
				break;
		}

		$this->add_crumb( 'post-type-archive', [
			'key' 		=> 'post-type-archive',
			'ids' 		=> [ $post_type ],
			'content' 	=> $post_type_object->labels->name,
			'link'		=> $link,
		] );
	}

	/**
	 * Render Single Terms
	 *
	 * @since  2.2.6
	 * @return void
	 */
	protected function add_single_terms( $post = false ) {
		if ( ! $post ) { global $post; }

		$terms 		= [];
		$taxonomies = get_post_taxonomies( $post->ID );
		$settings 	= $this->get_settings();
		$show_tax 	= $settings['single_' . get_post_type() . '_show_terms'];

		foreach ( $taxonomies as $index => $taxonomy ) {

			if ( '' !== $show_tax && $show_tax !== $taxonomy ) {
				continue;
			}

			$taxonomy_terms = wp_get_post_terms( $post->ID, $taxonomy, [
				'hide_empty' => false,
			] );

			Utils::sort_terms_hierarchicaly( $taxonomy_terms, $terms );
		}

		if ( $terms ) {
			$this->add_terms_recursive( $terms );
		}
	}

	/**
	 * Render Single Terms
	 *
	 * @since  2.2.6
	 * @return void
	 */
	protected function add_archive_term_parents( $term_id, $taxonomy ) {
		$parents = get_ancestors( $term_id, $taxonomy );

		if ( $parents )  {
			$ordered_parents 	= [];
			$parent_terms 		= get_terms( [
				'taxonomy' 	=> $taxonomy,
				'include' 	=> $parents,
			] );

			Utils::sort_terms_hierarchicaly( $parent_terms, $ordered_parents );

			if ( $ordered_parents ) {
				$this->add_terms_recursive( $ordered_parents );
			}
		}
	}

	/**
	 * Add Terms Crumbs Recursively
	 *
	 * @since  2.2.5
	 * @return void
	 */
	protected function add_terms_recursive( $terms ) {
		foreach ( $terms as $term_id => $term ) {
			/**
			 * Before Taxonomy Term
			 *
			 * Fires right before a custom taxonomy term that is not
			 * the current or last one.
			 * 
			 * The dynamic portion of the hook name, `$term_slug`, refers to the slug of the term.
			 *
			 * @since 2.2.0
			 * @param WP_Term 			$term 	The queried term
			 * @param Extras_Widget 	$this 	The current widget instance
			 */
			do_action( "elementor_extras/widgets/breadcrumbs/before/crumb/term/{$term->slug}", $term, $this );

			$this->add_crumb( 'taxonomy-terms', [
				'key' 		=> 'term-' . $term_id,
				'ids' 		=> [ $term->term_id, $term->slug ],
				'content' 	=> $term->name,
				'link'		=> get_term_link( $term_id ),
			] );

			if ( $term->children ) {
				$this->add_terms_recursive( $term->children );
			}
		}
	}

	/**
	 * Add Tag Crumb
	 *
	 * @since  2.2.5
	 * @return void
	 */
	protected function add_tag() {
		$term_id 		= get_query_var('tag_id');
		$taxonomy 		= 'post_tag';
		$args 			= 'include=' . $term_id;
		$terms 			= get_terms([
			'taxonomy' 	=> $taxonomy,
			'include'	=> $term_id,
		]);
		$tag 			= $terms[0];

		/**
		 * Before Tag Crumb
		 *
		 * Fires right before a tag crumb.
		 *
		 * @since 2.2.0
		 * @param WP_Post 			$post 		The page object
		 * @param Extras_Widget 	$this 		The current widget instance
		 */
		do_action( "elementor_extras/widgets/breadcrumbs/before/crumb/tag", $tag, $this );

		$this->add_crumb( 'tag', [
			'key' 		=> 'tag',
			'ids' 		=> [ $tag->term_id, $tag->slug ],
			'content' 	=> sprintf( __( 'Tag: %s', 'elementor-extras' ), $tag->name ),
		] );
	}

	/**
	 * Add Day Crumb
	 *
	 * @since  2.2.5
	 * @return void
	 */
	protected function add_day() {
		
		$this->add_year( false );
		$this->add_month( false );

		/**
		 * Before Day Crumb
		 *
		 * Fires right before a day crumb.
		 *
		 * @since 2.2.0
		 * @param Extras_Widget 	$this 		The current widget instance
		 */
		do_action( "elementor_extras/widgets/breadcrumbs/before/crumb/day", $this );

		$this->add_crumb( 'day', [
			'key' 		=> 'day',
			'ids' 		=> [ get_the_time('j') ],
			'content' 	=> sprintf( __( '%1$s %2$s Archives', 'elementor-extras' ), get_the_time('F'), get_the_time('jS') ),
		] );
	}

	/**
	 * Add Month Crumb
	 *
	 * @since  2.2.5
	 * @return void
	 */
	protected function add_month() {
		
		$this->add_year( false );

		/**
		 * Before Month Crumb
		 *
		 * Fires right before a month crumb.
		 *
		 * @since 2.2.0
		 * @param Extras_Widget 	$this 		The current widget instance
		 */
		do_action( "elementor_extras/widgets/breadcrumbs/before/crumb/month", $this );

		$this->add_crumb( 'month', [
			'key' 		=> 'month',
			'ids' 		=> [ get_the_time('m') ],
			'content' 	=> sprintf( __( '%s Archives', 'elementor-extras' ), get_the_time('F') ),
		] );
	}

	/**
	 * Add Year Crumb
	 *
	 * @since  2.2.5
	 * @return void
	 */
	protected function add_year() {
		/**
		 * Before Year Crumb
		 *
		 * Fires right before a year crumb.
		 *
		 * @since 2.2.0
		 * @param Extras_Widget 	$this 		The current widget instance
		 */
		do_action( "elementor_extras/widgets/breadcrumbs/before/crumb/year", $this );

		$this->add_crumb( 'year', [
			'key' 		=> 'year',
			'ids' 		=> [ get_the_time('Y') ],
			'content' 	=> sprintf( __( '%s Archives', 'elementor-extras' ), get_the_time('Y') ),
		] );
	}

	/**
	 * Add Author Crumb
	 *
	 * @since  2.2.5
	 * @return void
	 */
	protected function add_author() {
		global $author;

		$userdata = get_userdata( $author );

		/**
		 * Before Author Crumb
		 *
		 * Fires right before an author crumb.
		 *
		 * @since 2.2.0
		 * @param WP_User 			$post 		The queried author
		 * @param Extras_Widget 	$this 		The current widget instance
		 */
		do_action( "elementor_extras/widgets/breadcrumbs/before/crumb/author", $author, $this );

		$this->add_crumb( 'author', [
			'key' 		=> 'author',
			'ids' 		=> [ $userdata->user_nicename ],
			'content' 	=> sprintf( __( 'Author: %s', 'elementor-extras' ), $userdata->display_name ),
		] );
	}

	/**
	 * Add Search Crumb
	 *
	 * @since  2.2.5
	 * @return void
	 */
	protected function add_search() {
		/**
		 * Before Search Query Crumb
		 *
		 * Fires right before an author crumb.
		 *
		 * @since 2.2.0
		 * @param Extras_Widget 	$this 		The current widget instance
		 */
		do_action( "elementor_extras/widgets/breadcrumbs/before/crumb/search", $this );

		$this->add_crumb( 'search', [
			'current' 	=> true,
			'separator'	=> false,
			'key' 		=> 'search',
			'content' 	=> sprintf( __( 'Search results for: %s', 'elementor-extras' ), get_search_query() ),
		] );
	}

	/**
	 * Add 404 crumb
	 *
	 * @since  2.2.5
	 * @return void
	 */
	protected function add_404() {
		/**
		 * Before 404 Crumb
		 *
		 * Fires right before a 404 crumb.
		 *
		 * @since 2.2.0
		 * @param Extras_Widget 	$this 		The current widget instance
		 */
		do_action( "elementor_extras/widgets/breadcrumbs/before/crumb/404", $this );

		$this->add_crumb( '404', [
			'current' 	=> true,
			'separator'	=> false,
			'key' 		=> '404',
			'content' 	=> __( 'Page not found', 'elementor-extras' ),
		] );
	}

	/**
	 * Render Item
	 * 
	 * Gets the markup for a breadcrumb item
	 *
	 * @since  1.2.0
	 * @param  slug|string
	 * @param  args|array
	 * @return void
	 */
	protected function render_crumb( $slug, $index, $args ) {

		global $wp;

		$defaults = [
			'key' 			=> false,
			'ids'			=> [],
			'content'		=> '',
			'link'			=> false,
			'current' 		=> $index === count( $this->crumbs ) - 1,
		];

		$args = wp_parse_args( $args, $defaults );

		if ( $args['current'] && '' === $this->get_settings( 'show_current' ) ) {
			return;
		}

		$item_key 	= $args['key'] . '-item';
		$text_key 	= $args['key'] . '-text';
		$pos_key	= $args['key'] . '-position';

		$link_key 	= $args['key'] . '-link';
		$link_tag 	= ( ! $args['current'] ) ? 'a' : 'link';
		$classes 	= [];

		if ( '' !== $args['link'] ) {
			$this->add_render_attribute( $link_key, 'href', $args['link'] );
		} else {
			$this->add_render_attribute( $link_key, 'href', home_url( $wp->request ) );
		}

		if ( $args['current'] ) {
			$classes[] = 'ee-breadcrumbs__item--current';
		} else {
			$classes[] = 'ee-breadcrumbs__item--parent';
		}

		if ( $slug )
			$classes[] = 'ee-breadcrumbs__item--' . $slug;

		if ( $args['ids'] ) {
			foreach( $args['ids'] as $id ) {
				if ( $slug ) {
					$classes[] = 'ee-breadcrumbs__item--' . $slug . '-' . $id;
				} else { $classes[] = 'ee-breadcrumbs__item--' . $id; }
			}
		}

		$this->add_item_render_attribute( $item_key, $index );
		$this->add_render_attribute( [
			$item_key => [
				'class' => $classes,
			],
			$text_key => [
				'class' => 'ee-breadcrumbs__text',
			],
			$pos_key => [
				'content' => $index,
			],
		] );

		$this->add_link_render_attribute( $link_key );

		if ( $this->get_settings('structured_data') ) {
			$this->add_render_attribute( [
				$text_key => [
					'itemprop' 	=> 'name',
				],
				$pos_key => [
					'itemprop' 	=> 'position',
				],
			] );
		}

		/**
		 * Before Crumb
		 *
		 * Fires right before any breadcrumb
		 *
		 * @since 2.2.0
		 * @param Extras_Widget 	$this 		The current widget instance
		 */
		do_action( "elementor_extras/widgets/breadcrumbs/before/crumb", $this );

		?><li <?php echo $this->get_render_attribute_string( $item_key ); ?>>
			<<?php echo $link_tag; ?> <?php echo $this->get_render_attribute_string( $link_key ); ?>>
				<span <?php echo $this->get_render_attribute_string( $text_key ); ?>>
					<?php echo $args['content']; ?>
				</span>
			</<?php echo $link_tag; ?>>
			<meta <?php echo $this->get_render_attribute_string( $pos_key ); ?>>
		</li><?php

		if ( ! $args['current'] )
		/**
		 * After Crumb
		 *
		 * Fires right after any breadcrumb
		 *
		 * @since 2.2.0
		 * @param Extras_Widget 	$this 		The current widget instance
		 */
		do_action( "elementor_extras/widgets/breadcrumbs/after/crumb", $this );

		/**
		 * Before Separator
		 *
		 * Fires right before any separator
		 *
		 * @since 2.2.0
		 * @param Extras_Widget 	$this 		The current widget instance
		 */
		do_action( "elementor_extras/widgets/breadcrumbs/before/separator", $this );

		if ( false === $args['current'] ) {
			$this->render_separator();
		}

		/**
		 * After Separator
		 *
		 * Fires right after any separator
		 *
		 * @since 2.2.0
		 * @param Extras_Widget 	$this 		The current widget instance
		 */
		do_action( "elementor_extras/widgets/breadcrumbs/after/separator", $this );
	}

	/**
	 * Render Crumbs
	 * 
	 * Adds the render attributes for a specified item
	 *
	 * @access private
	 *
	 * @since  2.2.8
	 * @return void
	 */
	private function render_crumbs() {
		$index = 0;

		foreach ( $this->crumbs as $name => $args ) {
			$this->render_crumb( $name, $index, $args );
			$index++;
		}
	}

	/**
	 * Add Item Render Attribute
	 * 
	 * Adds the render attributes for a specified item
	 *
	 * @since  1.2.0
	 * @param  key|string 	The render attribute key for the item
	 * @param. index|int 	The index of the item. Defaults to 0 
	 * @return void
	 */
	protected function add_item_render_attribute( $key, $index = 0 ) {

		$this->add_render_attribute( $key, 'class', 'ee-breadcrumbs__item' );

		if ( $this->get_settings('structured_data') ) {
			$this->add_render_attribute( $key, [
				'itemprop' 	=> 'itemListElement',
				'itemscope' => '',
				'itemtype' 	=> 'http://schema.org/ListItem',
			] );
		}
	}

	/**
	 * Add Link Render Attribute
	 * 
	 * Adds the render attributes for the item link
	 *
	 * @since  1.2.0
	 * @param  key|string 	The render attribute key for the item
	 * @return void
	 */
	protected function add_link_render_attribute( $key ) {
		$this->add_render_attribute( $key, 'class', 'ee-breadcrumbs__crumb' );

		if ( $this->get_settings('structured_data') ) {
			$this->add_render_attribute( $key, 'itemprop', 'item' );
		}
	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering. None in this case
	 *
	 * @since  1.2.0
	 * @return void
	 */
	protected function _content_template() {}
}
