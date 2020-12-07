<?php
namespace ElementorExtras\Modules\Search\Skins;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\Search\Skins
 *
 * @since  2.1.0
 */
class Skin_Classic extends Skin_Base {

	/**
	 * Get ID
	 * 
	 * Gets the current skin ID
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_id() {
		return 'classic';
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
		return __( 'Classic', 'elementor-extras' );
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

		add_action( 'elementor/element/ee-search-form/section_button/before_section_end', [ $this, 'register_content_controls' ] );
		add_action( 'elementor/element/ee-search-form/section_button_style/before_section_end', [ $this, 'register_style_controls' ] );
	}

	/**
	 * Register Content Controls
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function register_content_controls() {
		$this->register_button_content_controls();
		
		parent::register_content_controls();
	}

	/**
	 * Register Style Controls
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function register_style_controls() {
		$this->register_form_style_controls();
		$this->register_filters_style_controls();
		$this->register_button_style_controls();
	}

	/**
	 * Register Button Content Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	public function register_button_content_controls() {

		$this->parent->start_injection( [
			'type' => 'section',
			'at' => 'start',
			'of' => 'section_button',
		] );

			$this->add_control(
				'heading_label_content',
				[
					'label' 	=> __( 'Label', 'elementor-extras' ),
					'type' 		=> Controls_Manager::HEADING,
				]
			);

			$this->add_control(
				'button_label',
				[
					'label' 	=> __( 'Show Label', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SWITCHER,
					'default'	=> 'yes',
					'label_off' => __( 'Hide', 'elementor-extras' ),
					'label_on' 	=> __( 'Show', 'elementor-extras' ),
				]
			);

			$this->add_control(
				'button_label_text',
				[
					'label' 	=> __( 'Label', 'elementor-extras' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'Search', 'elementor-extras' ),
					'separator' => 'after',
					'condition' => [
						$this->get_control_id( 'button_label!' ) => '',
					],
				]
			);

		$this->parent->end_injection();

		$this->parent->start_injection( [
			'at' => 'before',
			'of' => 'icon_style',
		] );

			$this->add_control(
				'icon',
				[
					'label' 		=> __( 'Icon', 'elementor-extras' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'label_block' 	=> false,
					'default' 		=> 'search',
					'options' 		=> [
						''				=> [
							'title'		=> __( 'Hide', 'elementor-extras' ),
							'icon'		=> 'fa fa-eye-slash',
						],
						'search' 	=> [
							'title' => __( 'Search', 'elementor-extras' ),
							'icon' 	=> 'nicon nicon-search',
						],
						'arrow' 	=> [
							'title' => __( 'Arrow', 'elementor-extras' ),
							'icon' 	=> 'nicon nicon-arrow-right',
						],
						'angle' 	=> [
							'title' => __( 'Angle', 'elementor-extras' ),
							'icon' 	=> 'nicon nicon-angle-right',
						],
						'triangle' 	=> [
							'title' => __( 'Triangle', 'elementor-extras' ),
							'icon' 	=> 'nicon nicon-triangle-right',
						],
					],
					'render_type' 	=> 'template',
					'prefix_class' 	=> 'ee-search-form-icon--',
				]
			);

		$this->parent->end_injection();

		$this->parent->start_injection( [
			'at' => 'after',
			'of' => 'icon_style',
		] );

			$this->add_control(
				'icon_position',
				[
					'label' 	=> __( 'Position', 'elementor-extras' ),
					'type' 		=> Controls_Manager::CHOOSE,
					'default' 	=> 'right',
					'options' 	=> [
						'left'    	=> [
							'title' => __( 'Left', 'elementor-extras' ),
							'icon' 	=> 'eicon-h-align-left',
						],
						'right' 	=> [
							'title' => __( 'Right', 'elementor-extras' ),
							'icon' 	=> 'eicon-h-align-right',
						],
					],
					'condition'	=> [
						$this->get_control_id( 'icon!' ) => '',
						$this->get_control_id( 'button_label!' ) => '',
					],
					'label_block' 	=> false,
					'prefix_class' 	=> 'ee-search-form-icon-position--'
				]
			);

			$this->add_control(
				'icon_distance',
				[
					'label' 		=> __( 'Spacing', 'elementor-pro' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 		=> [
						'size' 		=> 12,
					],
					'selectors' => [
						'body:not(.rtl) {{WRAPPER}}.ee-search-form-icon-position--left .ee-search-form__submit--has-label .ee-search-form__icon,
						 body.rtl {{WRAPPER}}:not(.ee-search-form-icon-position--left) .ee-search-form__submit--has-label .ee-search-form__icon' => 'margin-right: {{SIZE}}px;',
						'body:not(.rtl) {{WRAPPER}}:not(.ee-search-form-icon-position--left) .ee-search-form__submit--has-label .ee-search-form__icon,
						 body.rtl {{WRAPPER}}.ee-search-form-icon-position--left .ee-search-form__submit--has-label .ee-search-form__icon' => 'margin-left: {{SIZE}}px;',
					],
					'condition' => [
						$this->get_control_id( 'icon!' ) => '',
						$this->get_control_id( 'button_label!' ) => '',
					],
				]
			);

		$this->parent->end_injection();
	}

	/**
	 * Register Form Style Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function register_form_style_controls() {

		$this->parent->start_injection( [
			'at' => 'after',
			'of' => 'form_border_color',
		] );

			$this->add_responsive_control(
				'form_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'elementor-extras' ),
					'description'	=> __( 'For perfectly rounded corners set this to half of the height', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 50,
						],
					],
					'selectors'	=> [
						'{{WRAPPER}} .ee-search-form__container,
						 {{WRAPPER}} .ee-search-form__filters .ee-form__field__control--text' => 'border-radius: {{SIZE}}px;',
						'{{WRAPPER}} .ee-search-form__filters .select2-container--open.select2-container--below .ee-form__field__control--select2,
						.ee-select2__dropdown--{{ID}}.select2-dropdown--above' => 'border-radius: {{SIZE}}px {{SIZE}}px 0 0',
						'{{WRAPPER}} .ee-search-form__filters .select2-container--open.select2-container--above .ee-form__field__control--select2,
						.ee-select2__dropdown--{{ID}}.select2-dropdown--below' => 'border-radius: 0 0 {{SIZE}}px {{SIZE}}px',
					],
					'condition' => [
						'collapse_spacing!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'form_box_shadow',
					'selector' 	=> '{{WRAPPER}} .ee-search-form__container',
					'condition' => [
						'collapse_spacing!' => '',
					],
				]
			);

		$this->parent->end_injection();

		$this->parent->start_injection( [
			'type' => 'section',
			'at' => 'start',
			'of' => 'section_form_style',
		] );

			$this->add_responsive_control(
				'form_width',
				[
					'label' 	=> __( 'Width', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 500,
						],
						'%' 	=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .ee-search-form' => 'width: {{SIZE}}{{UNIT}}',
					],
				]
			);

		$this->parent->end_injection();

		$this->parent->start_injection( [
			'at' => 'after',
			'of' => 'alignment',
		] );

			$this->add_control(
				'fields_wrap',
				[
					'label' 		=> __( 'Wrap on', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> '',
					'options' 		=> [
						''			=> __( 'None', 'elementor-extras' ),
						'desktop'	=> __( 'Desktop', 'elementor-extras' ),
						'tablet'	=> __( 'Tablet', 'elementor-extras' ),
						'mobile'	=> __( 'Mobile', 'elementor-extras' ),
					],
					'prefix_class'	=> 'ee-search-form-fields-wrap--',
				]
			);

		$this->parent->end_injection();
	}

	/**
	 * Register Filters Style Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function register_filters_style_controls() {

		$this->parent->start_injection( [
			'at' => 'after',
			'of' => 'filters_custom',
		] );

			

		$this->parent->end_injection();

	}

	/**
	 * Register Icon Content Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	public function register_button_style_controls() {

		$this->parent->start_injection( [
			'type' => 'section',
			'at' => 'start',
			'of' => 'section_button_style',
		] );

			$this->add_responsive_control(
				'button_width',
				[
					'label' 	=> __( 'Width', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-search-form__submit' => 'flex-basis: {{SIZE}}%',
					],
				]
			);

		$this->parent->end_injection();
	}

	/**
	 * Render widget content
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render() {
		parent::render();

		$widget_id = $this->parent->get_id();

		$this->render_form();
	}

	/**
	 * Before Form End
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function before_form_end() {
		$this->render_filters();
	}

	/**
	 * Before Fields
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function before_fields() {}

	/**
	 * After Fields
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function after_fields() {
		$this->parent->render_inline_filters();
	}

	/**
	 * Render Form Container Content
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_form_container_content() {
		$this->render_fields();
		$this->render_button();
	}
}