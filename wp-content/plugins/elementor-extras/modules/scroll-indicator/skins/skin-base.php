<?php
namespace ElementorExtras\Modules\ScrollIndicator\Skins;

// Extras for Elementor Classes
use ElementorExtras\Utils;
use ElementorExtras\Base\Extras_Widget;

// Elementor Classes
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Skin_Base as Elementor_Skin_Base;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\ScrollIndicator\Skins
 *
 * @since  2.1.0
 */
abstract class Skin_Base extends Elementor_Skin_Base {

	/**
	 * Get Parent Widget
	 *
	 * @since  2.1.0
	 * @return $widget Extras_Widget
	 */
	public function get_widget() {
		return $this->parent;
	}

	/**
	 * Register Container Class
	 *
	 * @since  2.1.0
	 * @return void
	 */
	public function get_container_class() {
		return 'ee-scroll-indicator--skin-' . $this->get_id();
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
		add_action( 'elementor/element/ee-scroll-indicator/section_settings/after_section_end', [ $this, 'register_controls' ] );
	}

	/**
	 * Register Controls
	 *
	 * @since  2.1.0
	 * @return void
	 * @param  $widget Extras_Widget
	 */
	public function register_controls( Extras_Widget $widget ) {
		$this->parent = $widget;

		$this->register_content_controls();
		$this->register_style_controls();
	}

	/**
	 * Register Content Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	public function register_content_controls() {}

	/**
	 * Register Style Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	public function register_style_controls() {}

	/**
	 * Register Tooltip Content Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	public function register_tooltip_content_controls() {
		$this->start_controls_section(
			'section_tooltips',
			[
				'label' => __( 'Tooltips', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
				'condition' => [
					$this->get_control_id( 'tooltips!' ) => '',
				]
			]
		);

			$this->add_control(
				'show_on_focus',
				[
					'label' 		=> __( 'Show On Focus', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'elementor-extras' ),
					'label_off' 	=> __( 'No', 'elementor-extras' ),
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				'trigger',
				[
					'label'		=> __( 'Trigger', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 			=> 'mouseenter',
					'tablet_default' 	=> 'click_target',
					'mobile_default' 	=> 'click_target',
					'options' 			=> [
						'' 				=> __( 'None', 'elementor-extras' ),
						'mouseenter' 	=> __( 'Mouse Over', 'elementor-extras' ),
						'click_target' 	=> __( 'Click Target', 'elementor-extras' ),
						'load' 			=> __( 'Page Load', 'elementor-extras' ),
					],
					'frontend_available' => true
				]
			);

			$this->add_responsive_control(
				'_hide',
				[
					'label'		=> __( 'Hide on', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 			=> 'mouseleave',
					'tablet_default' 	=> 'click_out',
					'mobile_default' 	=> 'click_out',
					'options' 	=> [
						'' 				=> __( 'None', 'elementor-extras' ),
						'mouseleave' 	=> __( 'Mouse Leave', 'elementor-extras' ),
						'click_out' 	=> __( 'Click Outside', 'elementor-extras' ),
						'click_target' 	=> __( 'Click Target', 'elementor-extras' ),
						'click_any' 	=> __( 'Click Anywhere', 'elementor-extras' ),
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'position',
				[
					'label'		=> __( 'Show to', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'left',
					'options' 	=> [
						'bottom' 	=> __( 'Bottom', 'elementor-extras' ),
						'left' 		=> __( 'Left', 'elementor-extras' ),
						'top' 		=> __( 'Top', 'elementor-extras' ),
						'right' 	=> __( 'Right', 'elementor-extras' ),
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'arrow_position_h',
				[
					'label'		=> __( 'Show at', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '',
					'options' 	=> [
						'' 			=> __( 'Center', 'elementor-extras' ),
						'left' 		=> __( 'Left', 'elementor-extras' ),
						'right' 	=> __( 'Right', 'elementor-extras' ),
					],
					'condition'		=> [
						$this->get_control_id( 'position' ) => [ 'top', 'bottom' ],
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'arrow_position_v',
				[
					'label'		=> __( 'Show at', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '',
					'options' 	=> [
						'' 			=> __( 'Center', 'elementor-extras' ),
						'top' 		=> __( 'Top', 'elementor-extras' ),
						'bottom' 	=> __( 'Bottom', 'elementor-extras' ),
					],
					'condition'		=> [
						$this->get_control_id( 'position' ) => [ 'left', 'right' ],
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'css_position',
				[
					'label' 		=> __( 'CSS Position', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'fixed',
					'options'		=> [
						'' 			=> 'Absolute',
						'fixed'		=> 'Fixed',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'disable',
				[
					'label'		=> __( 'Disable On', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '',
					'options' 	=> [
						'' 			=> __( 'None', 'elementor-extras' ),
						'tablet' 	=> __( 'Tablet & Mobile', 'elementor-extras' ),
						'mobile' 	=> __( 'Mobile', 'elementor-extras' ),
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'tooltips_arrow',
				[
					'label'		=> __( 'Arrow', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '""',
					'options' 	=> [
						'""' 	=> __( 'Show', 'elementor-extras' ),
						'none' 	=> __( 'Hide', 'elementor-extras' ),
					],
					'selectors' => [
						'.ee-tooltip.ee-tooltip-{{ID}}:after' => 'content: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'delay_in',
				[
					'label' 		=> __( 'Delay in (s)', 'elementor-extras' ),
					'description' 	=> __( 'Time until tooltips appear.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default'	=> [
						'size' 	=> 0,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 1,
							'step'	=> 0.1,
						],
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'delay_out',
				[
					'label' 		=> __( 'Delay out (s)', 'elementor-extras' ),
					'description' 	=> __( 'Time until tooltips dissapear.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default'	=> [
						'size' 	=> 0,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 1,
							'step'	=> 0.1,
						],
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'duration',
				[
					'label' 		=> __( 'Duration', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 2,
							'step'	=> 0.1,
						],
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'tooltips_distance',
				[
					'label' 		=> __( 'Distance', 'elementor-extras' ),
					'description' 	=> __( 'The distance between the tooltip and the hotspot. Defaults to 6px', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 0,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 100,
						],
					],
					'selectors'		=> [
						'.ee-tooltip.ee-tooltip-{{ID}}.to--top' 			=> 'transform: translateY(-{{SIZE}}{{UNIT}});',
						'.ee-tooltip.ee-tooltip-{{ID}}.to--bottom' 		=> 'transform: translateY({{SIZE}}{{UNIT}});',
						'.ee-tooltip.ee-tooltip-{{ID}}.to--left' 			=> 'transform: translateX(-{{SIZE}}{{UNIT}});',
						'.ee-tooltip.ee-tooltip-{{ID}}.to--right' 		=> 'transform: translateX({{SIZE}}{{UNIT}});',
					]
				]
			);

			$this->add_control(
				'tooltips_offset',
				[
					'label' 		=> __( 'Offset', 'elementor-extras' ),
					'description' 	=> __( 'Adjust offset to align arrow with target.', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 0,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> -100,
							'max' 	=> 100,
						],
					],
					'selectors'	=> [
						'.ee-tooltip.ee-tooltip-{{ID}}.to--top,
						 .ee-tooltip.ee-tooltip-{{ID}}.to--bottom' => 'margin-left: {{SIZE}}{{UNIT}};',
						'.ee-tooltip.ee-tooltip-{{ID}}.to--left,
						 .ee-tooltip.ee-tooltip-{{ID}}.to--right' => 'margin-top: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'tooltips_width',
				[
					'label' 		=> __( 'Maximum Width', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 350,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 500,
						],
					],
					'selectors'		=> [
						'.ee-tooltip.ee-tooltip-{{ID}}' => 'max-width: {{SIZE}}{{UNIT}};',
					]
				]
			);

			$this->add_control(
				'tooltips_zindex',
				[
					'label'			=> __( 'zIndex', 'elementor-extras' ),
					'description'   => __( 'Adjust the z-index of the tooltips. Defaults to 999', 'elementor-extras' ),
					'type'			=> Controls_Manager::NUMBER,
					'default'		=> '999',
					'min'			=> -9999999,
					'step'			=> 1,
					'selectors'		=> [
						'.ee-tooltip.ee-tooltip-{{ID}}' => 'z-index: {{SIZE}};',
					]
				]
			);

		$this->end_controls_section();

	}

	/**
	 * Register Tooltip Style Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	public function register_tooltip_style_controls() {

		$this->start_controls_section(
			'section_tooltips_style',
			[
				'label' => __( 'Tooltips', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					$this->get_control_id( 'tooltips!' ) => '',
				]
			]
		);

			$this->add_control(
				'tooltips_align',
				[
					'label' 	=> __( 'Alignment', 'elementor-extras' ),
					'type' 		=> Controls_Manager::CHOOSE,
					'options' 	=> [
						'left' 	=> [
							'title' 	=> __( 'Left', 'elementor-extras' ),
							'icon' 		=> 'fa fa-align-left',
						],
						'center' 	=> [
							'title' => __( 'Center', 'elementor-extras' ),
							'icon' 	=> 'fa fa-align-center',
						],
						'right' 	=> [
							'title' => __( 'Right', 'elementor-extras' ),
							'icon'	=> 'fa fa-align-right',
						],
					],
					'selectors' => [
						'.ee-tooltip.ee-tooltip-{{ID}}' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'tooltips_padding',
				[
					'label' 		=> __( 'Padding', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'.ee-tooltip.ee-tooltip-{{ID}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'tooltips_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'.ee-tooltip.ee-tooltip-{{ID}}' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'tooltips_background_color',
				[
					'label' 	=> __( 'Background Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => Utils::get_tooltip_background_selectors(),
				]
			);

			$this->add_control(
				'tooltips_color',
				[
					'label' 	=> __( 'Color', 'elementor-extras' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'.ee-tooltip.ee-tooltip-{{ID}}' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'tooltips_border',
					'label' 	=> __( 'Border', 'elementor-extras' ),
					'selector' 	=> '.ee-tooltip.ee-tooltip-{{ID}}',
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'tooltips_typography',
					'selector' 	=> '.ee-tooltip.ee-tooltip-{{ID}}',
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'separator' => '',
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'tooltips_box_shadow',
					'selector' 	=> '.ee-tooltip.ee-tooltip-{{ID}}',
					'separator'	=> '',
				]
			);

		$this->end_controls_section();

	}

	/**
	 * Render widget
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render() {

		$this->parent->render();

		$this->parent->add_render_attribute( [
			'wrapper' => [
				'class' => [
					'ee-scroll-indicator',
					$this->get_container_class(),
				],
			],
		] );

		?><div <?php echo $this->parent->get_render_attribute_string('wrapper'); ?>>
			<?php $this->render_content(); ?>
		</div><?php
	}

	/**
	 * Render content
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_content() {
		$this->render_elements();
	}

	/**
	 * Get default nav class
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function get_nav_class() {}

	/**
	 * Render elements loop
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_elements() {
		$sections = $this->parent->get_settings( 'sections' );
		$nav_class = $this->get_nav_class();

		?>
		<ul class="ee-scroll-indicator__menu ee-nav <?php echo $nav_class; ?>">
			<?php foreach ( $sections as $index => $section ) { ?>
				<?php $this->render_element( $index, $section ); ?>
			<?php } ?>
		</ul>
		<?php
	}

	/**
	 * Render element item
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_element( $index, $section ) {

		$section_key = $this->parent->_get_repeater_setting_key( 'element', 'sections', $index );

		$this->parent->add_render_attribute( [
			$section_key => [
				'class' 			=> 'ee-scroll-indicator__element',
				'data-selector'		=> $section['selector'],
				'data-start' 		=> $section['progress_start'],
				'data-start-offset' => $section['progress_start_offset']['size'],
				'data-end' 			=> $section['progress_end'],
				'data-end-offset' 	=> $section['progress_end_offset']['size'],
			],
		] );

		?>
		<li <?php echo $this->parent->get_render_attribute_string( $section_key ); ?>>
			<?php $this->render_element_content( $index, $section ); ?>
		</li>
		<?php
	}

	/**
	 * Render element item content
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_element_content( $index, $section ) {

		$settings 	= $this->parent->get_settings();
		$link_key 	= $this->parent->_get_repeater_setting_key( 'link', 'sections', $index );
	}

	/**
	 * Render bullet markup
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_element_bullet( $index, $section ) {
		$skin = $this->parent->get_current_skin();

		if ( ! in_array( $skin->get_instance_value( 'show' ), [ '', 'numbers' ] ) )
			return;

		$bullet_key 	= $this->parent->_get_repeater_setting_key( 'bullet', 'sections', $index );
		$number_key 	= $this->parent->_get_repeater_setting_key( 'number', 'sections', $index );
		$circle_key 	= $this->parent->_get_repeater_setting_key( 'circle', 'sections', $index );

		$this->parent->add_render_attribute( [
			$bullet_key => [
				'class' => 'ee-scroll-indicator__element__bullet',
			],
			$number_key => [
				'class' => 'ee-scroll-indicator__element__number ee-center',
			],
			$circle_key => [
				'class' => 'ee-scroll-indicator__element__circle',
			],
		] );

		?>
		<div <?php echo $this->parent->get_render_attribute_string( $bullet_key ); ?>>
			<div <?php echo $this->parent->get_render_attribute_string( $number_key ); ?>>
				<?php echo $index + 1; ?>
			</div>
			<div <?php echo $this->parent->get_render_attribute_string( $circle_key ); ?>>
				<?php $this->render_svg(); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render text markup
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_element_text( $index, $section ) {

		$skin = $this->parent->get_current_skin();

		if ( ! in_array( $skin->get_instance_value( 'show' ), [ '', 'text' ] ) || ( empty( $section['title'] ) && empty( $section['subtitle'] ) ) )
			return;

		$text_key 		= $this->parent->_get_repeater_setting_key( 'text', 'sections', $index );
		$title_key 		= $this->parent->_get_repeater_setting_key( 'title', 'sections', $index );
		$subtitle_key 	= $this->parent->_get_repeater_setting_key( 'subtitle', 'sections', $index );

		$title_tag 		= $skin->get_instance_value('title_html_tag');
		$subtitle_tag 	= $skin->get_instance_value('subtitle_html_tag');

		$this->parent->add_render_attribute( [
			$text_key => [
				'class' => 'ee-scroll-indicator__element__text',
			],
		] );

		if ( $section['title'] ) {
			$this->parent->add_render_attribute( [
				$title_key => [
					'class' => 'ee-scroll-indicator__element__title',
				],
			] );
		}

		if ( $section['subtitle'] ) {
			$this->parent->add_render_attribute( [
				$subtitle_key => [
					'class' => 'ee-scroll-indicator__element__subtitle',
				],
			] );
		}

		?>
		<div <?php echo $this->parent->get_render_attribute_string( $text_key ); ?>>
			<?php if ( $section['title'] ) { ?>
			<<?php echo $title_tag; ?> <?php echo $this->parent->get_render_attribute_string( $title_key ); ?>><?php echo $section['title']; ?></<?php echo $title_tag; ?>>
			<?php } ?>
			<?php if ( $section['subtitle'] ) { ?>
			<<?php echo $subtitle_tag; ?> <?php echo $this->parent->get_render_attribute_string( $subtitle_key ); ?>><?php echo $section['subtitle']; ?></<?php echo $subtitle_tag; ?>>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Render circle svg
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_svg() {
		?><svg x="0px" y="0px" width="36px" height="36px" viewBox="0 0 36 36"><circle fill="none" stroke-width="2" cx="18" cy="18" r="16" stroke-dasharray="100 100" stroke-dashoffset="100" transform="rotate(-90 18 18)"></circle></svg><?php
	}

}