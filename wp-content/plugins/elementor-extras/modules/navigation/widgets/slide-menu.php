<?php
namespace ElementorExtras\Modules\Navigation\Widgets;

// Extras for Elementor Classes
use ElementorExtras\Base\Extras_Widget;
use ElementorExtras\Modules\Navigation\Skins;
use ElementorExtras\Modules\Navigation\Module as Module;
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
 * Slide_Menu
 *
 * @since 2.0.0
 */
class Slide_Menu extends Extras_Widget {

	/**
	 * Has template content
	 *
	 * @since  2.0.0
	 * @var    bool
	 */
	protected $_has_template_content = false;

	/**
	 * Nav Menu Index
	 *
	 * @since  2.0.0
	 * @var    int
	 */
	protected $nav_menu_index = 1;

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_name() {
		return 'ee-slide-menu';
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
		return __( 'Slide Menu', 'elementor-extras' );
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
		return 'nicon nicon-slide-menu';
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
			'slide-menu',
		];
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
	 * Get Nav Menu Index
	 *
	 * @since  2.0.0
	 * @return int
	 */
	protected function get_nav_menu_index() {
		return $this->nav_menu_index++;
	}

	/**
	 * Get Available Menu
	 *
	 * Return the list of available WP menus
	 *
	 * @since  2.0.0
	 * @return array
	 */
	private function get_available_menus() {
		$menus = wp_get_nav_menus();

		$options = [];

		foreach ( $menus as $menu ) {
			$options[ $menu->slug ] = $menu->name;
		}

		return $options;
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

		// Style tab
		$this->register_menu_style_controls();
		$this->register_links_style_controls();
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

			$menus = $this->get_available_menus();

			if ( ! empty( $menus ) ) {
				$this->add_control(
					'menu',
					[
						'label' => __( 'Menu', 'elementor-extras' ),
						'type' => Controls_Manager::SELECT,
						'options' => $menus,
						'default' => array_keys( $menus )[0],
						'save_default' => true,
						'separator' => 'after',
						'description' => sprintf( __( 'Go to the <a href="%s" target="_blank">Menus screen</a> to manage your menus.', 'elementor-extras' ), admin_url( 'nav-menus.php' ) ),
					]
				);
			} else {
				$this->add_control(
					'menu',
					[
						'type' => Controls_Manager::RAW_HTML,
						'raw' => sprintf( __( '<strong>There are no menus in your site.</strong><br>Go to the <a href="%s" target="_blank">Menus screen</a> to create one.', 'elementor-extras' ), admin_url( 'nav-menus.php?action=edit&menu=0' ) ),
						'separator' => 'after',
						'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					]
				);
			}

			$this->add_control(
				'back_text',
				[
					'label' 		=> __( 'Back Label', 'elementor-extras' ),
					'type' 			=> Controls_Manager::TEXT,
					'dynamic'		=> [ 'active' => true ],
					'default' 		=> __( 'Back', 'elementor-extras' ),
					'label_block' 	=> false,
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'effect',
				[
					'label' 	=> __( 'Effect', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'options' 	=> [
						'overlay'	=> __( 'Overlay', 'elementor-extras' ),
						'push'		=> __( 'Push', 'elementor-extras' ),
						// 'shift'		=> __( 'Shift', 'elementor-extras' ),
					],
					'default' 		=> 'overlay',
					'prefix_class'	=> 'ee-slide-menu-effect--',
				]
			);

			$this->add_control(
				'direction',
				[
					'label' 	=> __( 'Direction', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'options' 	=> [
						'left'		=> __( 'Left', 'elementor-extras' ),
						'right'		=> __( 'Right', 'elementor-extras' ),
						'bottom'	=> __( 'Bottom', 'elementor-extras' ),
						'top'		=> __( 'Top', 'elementor-extras' ),
					],
					'default' 		=> 'left',
					'prefix_class'	=> 'ee-slide-menu-direction--',
				]
			);

			$this->add_responsive_control(
				'duration',
				[
					'label' 		=> __( 'Transition Duration', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 3,
							'step'=> 0.1,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-menu__sub-menu,
						 {{WRAPPER}} .ee-menu__menu' => 'transition-duration: {{SIZE}}s;',
					],
				]
			);

			$this->add_control(
				'link_navigation',
				[
					'label' 		=> __( 'Link Navigation', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'description' 	=> __( 'Allow navigating to sub-menus by clicking the links instead of just the arrows.', 'elementor-extras' ),
					'return_value' 	=> 'yes',
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();

	}

	/**
	 * Register Menu Style Controls
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function register_menu_style_controls() {
		$this->start_controls_section(
			'section_menu_style',
			[
				'label' => __( 'Menu', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'width',
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
						'{{WRAPPER}} .ee-slide-menu' => 'max-width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'fixed_height',
				[
					'label' 		=> __( 'Fixed Height', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'description' 	=> __( 'If turned off, the menu will adjust its height based on the currently active sub-menu.', 'elementor-extras' ),
					'default'		=> 'yes',
				]
			);

			$this->add_responsive_control(
				'height',
				[
					'label' 		=> __( 'Min. Height', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 100,
							'max' => 1000,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-slide-menu,
						 {{WRAPPER}} .ee-slide-menu__sub-menu' => 'min-height: {{SIZE}}px;',
					],
					'condition'		=> [
						'fixed_height!' => '',
					],
				]
			);

			$this->add_control(
				'align',
				[
					'label' 		=> __( 'Align', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'left',
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
					'selectors' => [
						'{{WRAPPER}}' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'background',
				[
					'label' 	=> __( 'Background', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'global' => [
						'default' => Global_Colors::COLOR_PRIMARY,
					],
					'default'	=> '',
					'selectors' => [
						'{{WRAPPER}} .ee-slide-menu,
						 {{WRAPPER}} .ee-menu__sub-menu' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'navigation',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'selector' 	=> '{{WRAPPER}} .ee-slide-menu',
				]
			);

			$this->add_control(
				'border_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'selectors' 	=> [
						'{{WRAPPER}} .ee-slide-menu,
						 {{WRAPPER}} .ee-slide-menu__sub-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'navigation_box_shadow',
					'selector' 	=> '{{WRAPPER}} .ee-slide-menu',
					'separator'	=> '',
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
					'selector' 	=> '{{WRAPPER}} .ee-slide-menu .ee-menu__item a',
				]
			);

		$this->end_controls_section();

	}

	/**
	 * Register Links Style Controls
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function register_links_style_controls() {
		$this->start_controls_section(
			'section_links_style',
			[
				'label' => __( 'Links', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'links_spacing',
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
						'{{WRAPPER}} .ee-menu__item:not(:last-child)' => 'margin-bottom: {{SIZE}}px;',
					],
				]
			);

			$this->add_responsive_control(
				'links_separator_thickness',
				[
					'label' 		=> __( 'Separator Thickness', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'separator'		=> 'before',
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 50,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-menu__item' => 'border-bottom-width: {{SIZE}}px; border-bottom-style: solid;',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 		=> 'links',
					'selector' 	=> '{{WRAPPER}} .ee-menu__item__link,
									{{WRAPPER}} .ee-menu__arrow',
					'separator'	=> '',
				]
			);

			$this->start_controls_tabs( 'links_type' );

			$this->start_controls_tab( 'links_regular', [ 'label' => __( 'Default', 'elementor-extras' ) ] );

				$this->add_control(
					'links_padding',
					[
						'label' 		=> __( 'Padding', 'elementor-extras' ),
						'type' 			=> Controls_Manager::DIMENSIONS,
						'selectors' 	=> [
							'{{WRAPPER}} .ee-menu__item__link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							'{{WRAPPER}} .ee-menu__arrow' => 'padding-top: {{TOP}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'links_text_align',
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
						'selectors' => [
							'{{WRAPPER}} .ee-menu__item__link' => 'text-align: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'links_back', [ 'label' => __( 'Back', 'elementor-extras' ) ] );

				$this->add_control(
					'links_back_padding',
					[
						'label' 		=> __( 'Padding', 'elementor-extras' ),
						'type' 			=> Controls_Manager::DIMENSIONS,
						'selectors' 	=> [
							'{{WRAPPER}} .ee-menu__back .ee-menu__item__link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							'{{WRAPPER}} .ee-menu__back .ee-menu__arrow' => 'padding-top: {{TOP}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'links_back_text_align',
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
						'selectors' => [
							'{{WRAPPER}} .ee-menu__back .ee-menu__item__link' => 'text-align: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->start_controls_tabs( 'links' );

			$this->start_controls_tab( 'links_default', [ 'label' => __( 'Default', 'elementor-extras' ) ] );

				$this->add_control(
					'links_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-menu__item__link' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'links_separator_color',
					[
						'label' 	=> __( 'Separator Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-menu__item' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'links_background',
					[
						'label' 	=> __( 'Background', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-menu__item__link' => 'background-color: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'links_hover', [ 'label' => __( 'Hover', 'elementor-extras' ) ] );

				$this->add_control(
					'links_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-menu__item__link:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'links_separator_color_hover',
					[
						'label' 	=> __( 'Separator Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-menu__item:hover' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'links_background_hover',
					[
						'label' 	=> __( 'Background', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-menu__item__link:hover' => 'background-color: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'links_current', [ 'label' => __( 'Current', 'elementor-extras' ) ] );

				$this->add_control(
					'links_color_current',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-menu__item__link.ee-menu__item__link--current,
							 {{WRAPPER}} .ee-menu__item__link.ee-menu__item__link--current:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'links_separator_color_current',
					[
						'label' 	=> __( 'Separator Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-menu__item.ee-menu__item--current' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'links_background_current',
					[
						'label' 	=> __( 'Background', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-menu__item__link.ee-menu__item__link--current,
							 {{WRAPPER}} .ee-menu__item__link.ee-menu__item__link--current:hover' => 'background-color: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'heading_arrows',
				[
					'type'		=> Controls_Manager::HEADING,
					'label' 	=> __( 'Arrows', 'elementor-extras' ),
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'arrows_separator_thickness',
				[
					'label' 		=> __( 'Separator Thickness', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'separator'		=> 'after',
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 50,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-menu__item--has-children > .ee-menu__arrow' => 'border-left-width: {{SIZE}}px; border-left-style: solid;',
						'{{WRAPPER}} .ee-menu__back > .ee-menu__arrow' => 'border-right-width: {{SIZE}}px; border-right-style: solid;',
					],
				]
			);

			$this->start_controls_tabs( 'arrows_type' );

			$this->start_controls_tab( 'arrows_regular', [ 'label' => __( 'Default', 'elementor-extras' ) ] );

				$this->add_responsive_control(
					'arrows_size',
					[
						'label' 		=> __( 'Size', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SLIDER,
						'default'		=> [
							'size'		=> 1,
						],
						'range' 		=> [
							'px' 		=> [
								'min' => 0.1,
								'max' => 3,
								'step'=> 0.1,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .ee-menu__arrow i' => 'font-size: {{SIZE}}em;',
						],
					]
				);

				$this->add_control(
					'arrows_padding',
					[
						'label' 		=> __( 'Padding', 'elementor-extras' ),
						'type' 			=> Controls_Manager::DIMENSIONS,
						'allowed_dimensions' => [ 'right', 'left' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ee-menu__arrow' => 'padding-right: {{RIGHT}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'arrows_back', [ 'label' => __( 'Back', 'elementor-extras' ) ] );

				$this->add_responsive_control(
					'arrows_back_size',
					[
						'label' 		=> __( 'Size', 'elementor-extras' ),
						'type' 			=> Controls_Manager::SLIDER,
						'default'		=> [
							'size'		=> 1,
						],
						'range' 		=> [
							'px' 		=> [
								'min' => 0.1,
								'max' => 3,
								'step'=> 0.1,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .ee-menu__back .ee-menu__arrow i' => 'font-size: {{SIZE}}em;',
						],
					]
				);

				$this->add_control(
					'arrows_back_padding',
					[
						'label' 		=> __( 'Padding', 'elementor-extras' ),
						'type' 			=> Controls_Manager::DIMENSIONS,
						'allowed_dimensions' => [ 'right', 'left' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ee-menu__back .ee-menu__arrow' => 'padding-right: {{RIGHT}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->start_controls_tabs( 'arrows' );

			$this->start_controls_tab( 'arrows_default', [ 'label' => __( 'Default', 'elementor-extras' ) ] );

				$this->add_control(
					'arrows_color',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-menu__arrow' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'arrows_separator_color',
					[
						'label' 	=> __( 'Separator Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-menu__arrow' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'arrows_background',
					[
						'label' 	=> __( 'Background', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-menu__arrow' => 'background-color: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'arrows_hover', [ 'label' => __( 'Hover', 'elementor-extras' ) ] );

				$this->add_control(
					'arrows_color_hover',
					[
						'label' 	=> __( 'Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-menu__arrow:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'arrows_separator_color_hover',
					[
						'label' 	=> __( 'Separator Color', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-menu__arrow:hover' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'arrows_background_hover',
					[
						'label' 	=> __( 'Background', 'elementor-extras' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-menu__arrow:hover' => 'background-color: {{VALUE}};',
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
	public function render() {

		$available_menus = $this->get_available_menus();

		if ( ! $available_menus ) {
			return;
		}

		$settings = $this->get_active_settings();

		$args = [
			'echo' 			=> false,
			'menu' 			=> $settings['menu'],
			'menu_class' 	=> 'ee-menu__menu ee-slide-menu__menu',
			'menu_id' 		=> 'menu-' . $this->get_nav_menu_index() . '-' . $this->get_id(),
			'fallback_cb' 	=> '__return_empty_string',
			'before'  		=> '<span class="ee-menu__arrow"><i class="fa fa-angle-right"></i></span>',
			'container' 	=> '',
		];

		add_filter( 'walker_nav_menu_start_el', [ $this, 'filter_nav_menu_start_el' ], 10, 4 );
		add_filter( 'wp_nav_menu_items', [ $this, 'handle_menu_items' ] );
		add_filter( 'nav_menu_link_attributes', [ $this, 'handle_link_classes' ], 10, 4 );
		add_filter( 'nav_menu_submenu_css_class', [ $this, 'handle_sub_menu_classes' ] );
		add_filter( 'nav_menu_css_class', [ $this, 'handle_menu_item_classes' ] );

		// General Menu.
		$menu_html = wp_nav_menu( $args );

		remove_filter( 'wp_nav_menu_items', [ $this, 'handle_menu_items' ] );
		remove_filter( 'nav_menu_link_attributes', [ $this, 'handle_link_classes' ] );
		remove_filter( 'nav_menu_submenu_css_class', [ $this, 'handle_sub_menu_classes' ] );
		remove_filter( 'nav_menu_css_class', [ $this, 'handle_menu_item_classes' ] );
		remove_filter( 'walker_nav_menu_start_el', [ $this, 'filter_nav_menu_start_el' ] );

		$this->add_render_attribute( [
			'wrapper' => [
				'class' => [
					'ee-menu',
					'ee-slide-menu',
				],
			],
		] );

		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<?php echo $menu_html; ?>
		</div><?php
	}

	/**
	 * Filter for walker_nav_menu_start_el
	 *
	 * @since  2.0.0
	 * @return void
	 */
	function filter_nav_menu_start_el( $item_output, $item, $depth, $args ) {
		/**
		 * Walkter Nav Menu Start Filter
		 *
		 * @since 2.2.16
		 */
		return apply_filters( 'elementor_extras/widgets/slide_menu/nav_menu/walker_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * Handle Link Classes
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function handle_link_classes( $atts, $item, $args, $depth ) {
		$classes = $depth ? 'ee-menu__item__link ee-menu__sub-item__link' : 'ee-menu__item__link';

		if ( in_array( 'current-menu-item', $item->classes ) ) {
			$classes .= '  ee-menu__item__link--current';
		}

		if ( empty( $atts['class'] ) ) {
			$atts['class'] = $classes;
		} else {
			$atts['class'] .= ' ' . $classes;
		}

		return $atts;
	}

	/**
	 * Handle Menu Items
	 *
	 * Filter for wp_nav_menu items
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function handle_menu_items( $items ) {
		return $items;
	}
	
	/**
	 * Handle Menu Items Classes
	 *
	 * Filter for wp_nav_menu menu items classes
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function handle_menu_item_classes( $classes ) {
		$classes[] = 'ee-menu__item';

		if ( in_array( 'menu-item-has-children', $classes ) ) {
			$classes[] = 'ee-menu__item--has-children';
		}

		if ( in_array( 'current-menu-item', $classes ) ) {
			$classes[] = 'ee-menu__item--current';
		}

		return $classes;
	}

	/**
	 * Handle Sub-Menu Items Classes
	 *
	 * Filter for wp_nav_menu sub-menu items classes
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function handle_sub_menu_classes( $classes ) {
		$classes[] = 'ee-slide-menu__sub-menu';
		$classes[] = 'ee-menu__sub-menu';

		return $classes;
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