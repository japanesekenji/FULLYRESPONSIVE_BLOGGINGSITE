<?php
namespace ElementorExtras\Modules\ScrollIndicator\Skins;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\ScrollIndicator\Skins
 *
 * @since  2.1.0
 */
class Skin_Bar extends Skin_Base {

	/**
	 * Get ID
	 * 
	 * Gets the current skin ID
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_id() {
		return 'bar';
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
		return __( 'Bar', 'elementor-extras' );
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
		add_action( 'elementor/element/ee-scroll-indicator/section_settings/after_section_end', [ $this, 'register_bar_style_controls' ] );
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
					'raw' 				=> sprintf( __( '%1$sImportant note:%2$s Use the Elementor or Extras sticky functionality to keep the bar in view.', 'elementor-extras' ), '<strong>', '</strong>' ),
					'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-info',
				]
			);

		$this->parent->end_injection();
	}

	/**
	 * Register bar style controls
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function register_bar_style_controls() {

		$this->start_controls_section(
			'section_bar_style',
			[
				'label' => __( 'Bar', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'height',
				[
					'label' 	=> __( 'Height (px)', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 1,
							'max' => 50,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-scroll-indicator__element__wrapper' => 'height: {{SIZE}}{{UNIT}};',
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
						'{{WRAPPER}} .ee-scroll-indicator__element' => 'margin-left: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ee-scroll-indicator__menu' => 'margin-left: -{{SIZE}}{{UNIT}};',
					],
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

			$this->end_controls_tab();

			$this->start_controls_tab( 'indicators_progress', [ 'label' => __( 'Progress', 'elementor-extras' ) ] );

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

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();
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
		$settings 		= $this->parent->get_settings();
		$wrapper_key 	= $this->parent->_get_repeater_setting_key( 'wrapper', 'sections', $index );
		$link_key 		= $this->parent->_get_repeater_setting_key( 'link', 'sections', $index );
		$progress_key 	= $this->parent->_get_repeater_setting_key( 'progress', 'sections', $index );

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

		if ( 'yes' === $settings['click'] ) {
			$this->parent->add_render_attribute( $link_key, 'class', 'has--cursor' );
		}

		?>
		<a <?php echo $this->parent->get_render_attribute_string( $link_key ); ?>>
			<div <?php echo $this->parent->get_render_attribute_string( $wrapper_key ); ?>>
				<div <?php echo $this->parent->get_render_attribute_string( $progress_key ); ?>></div>
			</div>
		</a>
		<?php
	}
}