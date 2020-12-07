<?php
namespace ElementorExtras\Modules\Search\Skins;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\Search\Skins
 *
 * @since  2.1.0
 */
class Skin_Expand extends Skin_Base {

	/**
	 * Get ID
	 * 
	 * Gets the current skin ID
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_id() {
		return 'expand';
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
		return __( 'Expand', 'elementor-extras' );
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
		add_action( 'elementor/element/ee-search-form/section_input_style/before_section_end', [ $this, 'register_style_controls' ] );
	}

	/**
	 * Register Content Controls
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function register_content_controls() {

		$this->parent->start_injection( [
			'at' => 'after',
			'of' => '_skin',
		] );

			$this->add_control(
				'input_position',
				[
					'label' 	=> __( 'Position', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'left',
					'options' 	=> [
						'left'    		=> __( 'Left', 'elementor-extras' ),
						'right' 		=> __( 'Right', 'elementor-extras' ),
					],
					'label_block' 	=> false,
					'prefix_class' 	=> 'ee-search-form-input-position--'
				]
			);

		$this->parent->end_injection();

		$this->register_button_content_controls();

		parent::register_content_controls();
	}

	/**
	 * Register Button Content Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	public function register_button_content_controls() {

		$this->parent->start_injection( [
			'at' => 'after',
			'of' => 'heading_icon_content',
		] );

			$this->add_control(
				'icon',
				[
					'label' => __( 'Icon', 'elementor-extras' ),
					'type' => \Elementor\Controls_Manager::HIDDEN,
					'default' => 'search',
				]
			);

		$this->parent->end_injection();

	}

	/**
	 * Register Style Controls
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function register_style_controls() {
		$this->register_form_style_controls();
		$this->register_input_style_controls();
		$this->register_button_style_controls();
	}

	/**
	 * Register Input Style Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	public function register_input_style_controls() {

		$this->parent->start_injection( [
			'at' => 'after',
			'of' => 'fields_style_heading',
		] );

			$this->add_responsive_control(
				'input_width',
				[
					'label' => __( 'Expand Width', 'elementor-extras' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 400,
						'unit' => 'px',
					],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 1000,
						],
						'vw' => [
							'min' => 0,
							'max' => 100,
						],
						'em' => [
							'min' => 0,
							'max' => 50,
						],
					],
					'size_units' => [ 'px', 'vw', 'em' ],
					'selectors' => [
						'{{WRAPPER}} .ee-search-form.ee--active .ee-search-form__container' => 'width: {{SIZE}}{{UNIT}}',
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
						 {{WRAPPER}} .ee-search-form__submit,
						 {{WRAPPER}} .ee-search-form__fields' => 'border-radius: {{SIZE}}px;',
						'{{WRAPPER}} .select2-container--open.select2-container--below .ee-form__field__control--select2,
						.ee-select2__dropdown--{{ID}}.select2-dropdown--above' => 'border-radius: {{SIZE}}px {{SIZE}}px 0 0',
						'{{WRAPPER}} .select2-container--open.select2-container--above .ee-form__field__control--select2,
						.ee-select2__dropdown--{{ID}}.select2-dropdown--below' => 'border-radius: 0 0 {{SIZE}}px {{SIZE}}px',
					],
					'condition' => [
						'collapse_spacing!' => '',
					],
				]
			);

		$this->parent->end_injection();
	}

	/**
	 * Register Button Style Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	public function register_button_style_controls() {

		$this->parent->start_injection( [
			'at' => 'after',
			'of' => 'button_background_color',
		] );

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

		$this->render_form();
	}

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
	 * Render Form Container
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_form_container() {
		parent::render_form_container();
		parent::render_button();
	}

	/**
	 * Render Button Content
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_button_content() {
		$this->render_button_sr_label();
		$this->render_icon();
	}
}