<?php
namespace ElementorExtras\Modules\Search\Skins;

// Extras for Elementor Classes
use ElementorExtras\Utils;
use ElementorExtras\Base\Extras_Widget;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\Search\Skins
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
	 * Register Controls Actions
	 * 
	 * Registers controls at specific points in the Controls Stack
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function _register_controls_actions() {
		add_action( 'elementor/element/ee-search-form/section_button/before_section_end', [ $this, 'register_controls' ] );
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
	}

	/**
	 * Register Content Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	public function register_content_controls() {
		$this->parent->start_injection( [
			'at' => 'after',
			'of' => $this->get_control_id('icon'),
		] );

			$this->add_control(
				'icon_style',
				[
					'label' 	=> __( 'Style', 'elementor-extras' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'thin',
					'options' 	=> [
						'thin' 	=> __( 'Thin', 'elementor-extras' ),
						'thick' => __( 'Thick', 'elementor-extras' ),
					],
					'condition'	=> [
						$this->get_control_id('icon!') => [ 'triangle', '' ],
					],
					'render_type' => 'template',
				]
			);

		$this->parent->end_injection();
	}

	/**
	 * Register Style Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	public function register_base_style_controls() {}

	/**
	 * Add Actions
	 * 
	 * Registers actions for rendering
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function add_actions() {

		add_action( 'elementor-extras/search-form/form/after_start', [ $this->parent, 'render_hidden_fields' ], 20 );

	}

	/**
	 * Render widget
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render() {
		$this->parent->render();

		$this->add_actions();
	}

	/**
	 * Render Form
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function has_button_label() {
		if ( '' === $this->get_instance_value('button_label') || '' === trim( $this->get_instance_value('button_label_text') ) || empty( $this->get_instance_value('button_label_text') ) )
			return false;

		return true;
	}

	/**
	 * Render Form
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_form() {
		$settings = $this->parent->get_settings();

		$this->parent->add_render_attribute(
			'form', [
				'class' => [
					'ee-form',
					'ee-search-form',
					'ee-search-form-skin--' . $settings['_skin'],
				],
				'role' 		=> 'search',
				'action' 	=> $this->parent->get_search_url(),
				'method' 	=> 'get',
				'value' 	=> get_search_query(),
			]
		);

		?><form <?php echo $this->parent->get_render_attribute_string( 'form' ); ?>>
			<?php

			$this->after_form_start();
			$this->render_form_container();
			$this->before_form_end();

		?></form><?php
	}

	/**
	 * After Form Start
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function after_form_start() {
		$this->parent->render_hidden_fields();
	}

	/**
	 * Before Form End
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function before_form_end() {}

	/**
	 * Render Form Container
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_form_container() {
		$this->parent->add_render_attribute( 'form-container', 'class', 'ee-search-form__container' );

		?><div <?php echo $this->parent->get_render_attribute_string( 'form-container' ); ?>><?php
			$this->render_form_container_content();
		?></div><?php
	}

	/**
	 * Render Form Container Content
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_form_container_content() {
		$this->render_fields();
	}

	/**
	 * Render Form Input
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_fields() {
		$settings = $this->parent->get_settings_for_display();

		$this->parent->add_render_attribute( [
			'fields' => [
				'class' => [
					'ee-form__fields',
					'ee-search-form__fields',
				],
			],
		] );

		$widget_id = $this->parent->get_id();

		?><div <?php echo $this->parent->get_render_attribute_string( 'fields' ); ?>>
			<?php

				$this->before_fields();
				$this->render_input();
				$this->after_fields();

			?>
		</div><?php
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
	public function after_fields() {}

	/**
	 * Render Form Input Field
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_input() {
		$settings = $this->parent->get_settings_for_display();

		$this->parent->add_render_attribute( [
			'field' => [
				'class' 		=> [
					'ee-form__field',
					'ee-form__field--input',
					'ee-form__field--search',
					'ee-search-form__field',
				],
			],
			'input' => [
				'placeholder' 	=> $settings['input_placeholder'],
				'class' 		=> [
					'ee-search-form__input',
					'ee-form__field__control',
					'ee-form__field__control--search',
					'ee-form__field__control--text',
					'ee-form__field__control--sent',
					'ee-form__field__control--input',
				],
				'type' 			=> 'search',
				'name' 			=> 's',
				'title' 		=> __( 'Search', 'elementor-extras' ),
				'value' 		=> get_search_query(),
			]
		] );

		?><div <?php echo $this->parent->get_render_attribute_string( 'field' ); ?>>
			<input <?php echo $this->parent->get_render_attribute_string( 'input' ); ?>>
		</div><?php
	}

	/**
	 * Render Filters
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_filters() {
		$this->parent->render_filters();
	}

	/**
	 * Render Filters
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_filters_toggle() {
		$settings = $this->parent->get_settings_for_display();

		if ( ! $this->parent->_fields )
			return;

		$this->parent->add_render_attribute( [
			'filters-toggle' => [
				'class' => [
					'ee-search-form__filters-toggle',
					'nicon nicon-filter',
				],
			],
		] );

		?><span <?php echo $this->parent->get_render_attribute_string( 'filters-toggle' ); ?>></span><?php
	}

	/**
	 * Render Button
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_button() {
		$this->parent->add_render_attribute(
			'button', [
				'class' => [
					'ee-search-form__submit',
					'ee-form__field__control',
					'ee-form__field__control--submit',
					'ee-form__field__control--text',
					'ee-form__field__control--sent',
					'ee-form__field__control--button',
				],
				'type' 	=> 'submit',
			]
		);

		if ( $this->has_button_label() ) {
			$this->parent->add_render_attribute( 'button', 'class', 'ee-search-form__submit--has-label' );
		} else {
			$this->parent->add_render_attribute( 'button', 'class', 'ee-search-form__control--icon' );
		}

		?><button <?php echo $this->parent->get_render_attribute_string( 'button' ); ?>>
			<?php $this->render_button_content(); ?>
		</button><?php
	}

	/**
	 * Render Button Content
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_button_content() {
		$this->render_button_label();
		$this->render_icon();
	}

	/**
	 * Render Button Icon
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_icon() {
		$settings 	= $this->parent->get_settings();
		$icon 		= $this->get_instance_value( 'icon' );
		$icon_style = $this->get_instance_value( 'icon_style' );

		if ( '' === $icon )
			return;

		$icon_style = ( 'thin' !== $icon_style ) ? '-thick' : '';
		$icon_class = 'search';

		switch ( $icon ) {
			case 'arrow' :
				$icon_class = is_rtl() ? 'arrow-left' : 'arrow-right';
				break;
			case 'angle' :
				$icon_class = is_rtl() ? 'angle-left' : 'angle-right';
				break;
			case 'triangle' :
				$icon_style = '';
				$icon_class = is_rtl() ? 'triangle-left' : 'triangle-right';
				break;
			default :
				break;
		}

		$this->parent->add_render_attribute( 'icon', [
			'class' => [
				'ee-search-form__icon',
				'nicon nicon-' . $icon_class . $icon_style,
			],
			'aria-hidden' => 'true',
		] );

		?><i <?php echo $this->parent->get_render_attribute_string( 'icon' ); ?>></i><?php
	}

	/**
	 * Render Button Icon
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_button_label() {
		$settings = $this->parent->get_settings_for_display();

		if ( ! $this->has_button_label() ) {
			$this->render_button_sr_label();
			return;
		}

		echo $this->get_instance_value('button_label_text');
	}

	/**
	 * Render Button Screen Reader Label
	 *
	 * @since 2.2.40
	 * @return void
	 */
	public function render_button_sr_label() {
		$this->parent->add_render_attribute( 'screen-reader', 'class', 'elementor-screen-only' );

		?><span <?php echo $this->parent->get_render_attribute_string( 'screen-reader' ); ?>><?php
			/**
			 * Separator filter
			 *
			 * Filters the separator
			 *
			 * @since 2.2.0
			 * @param string 			$post_title 	The markup for the separator
			 */
			echo apply_filters( 'elementor_extras/widgets/search-form/button/sr_text', __('Search', 'elementor-extras') );
		?></span><?php
	}

}