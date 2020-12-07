<?php
namespace ElementorExtras\Modules\TemplatesControl;

// Extras for Elementor Classes
use ElementorExtras\Utils;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Provides temporary support for background images inside
 * our templates used in loops
 * @since 2.2.4
 */
class BackgroundImage {

	/**
	 * The current template ID
	 *
	 * @since 2.2.4
	 * @access private
	 *
	 * @var int
	 */
	private $template_id;

	/**
	 * The current template ID
	 *
	 * @since 2.2.4
	 * @access private
	 *
	 * @var int
	 */
	private $elements = [
		'section' => [
			'background_image' => [
				'allow_static' => false,
				'condition' => 'background_background',
				'selector' 	=> '
					{{WRAPPER}}:not(.elementor-motion-effects-element-type-background),
					{{WRAPPER}} > .elementor-motion-effects-container > .elementor-motion-effects-layer',
				'styles' 	=> [
					[
						'property' 	=> 'background-image',
						'value' 	=> 'url({{URL}});',
					]
				],
			],
			'background_hover_image' => [
				'allow_static'	=> true,
				'condition' => 'background_hover_background',
				'selector' 	=> '{{WRAPPER}}:hover, {{WRAPPER}}:hover > .elementor-motion-effects-container > .elementor-motion-effects-layer',
				'styles' 	=> [
					[
						'property' 	=> 'background-image',
						'value' 	=> 'url({{URL}});',
					]
				],
			],
		],
		'column' => [
			'background_image' => [
				'allow_static' => false,
				'condition' => 'background_background',
				'selector' 	=> '
					{{WRAPPER}}:not(.elementor-motion-effects-element-type-background) > .elementor-element-populated,
					{{WRAPPER}} > .elementor-column-wrap > .elementor-motion-effects-container > .elementor-motion-effects-layer',
				'styles' 	=> [
					[
						'property' 	=> 'background-image',
						'value' 	=> 'url({{URL}});',
					]
				],
			],
			'background_hover_image' => [
				'allow_static'	=> true,
				'condition' => 'background_hover_background',
				'selector' 	=> '{{WRAPPER}}:hover > .elementor-element-populated',
				'styles' 	=> [
					[
						'property' 	=> 'background-image',
						'value' 	=> 'url({{URL}});',
					]
				],
			],
		],
		'widget' => [
			'_background_image' => [
				'allow_static' => false,
				'condition' => '_background_background',
				'selector' 	=> '{{WRAPPER}} > .elementor-widget-container',
				'styles' 	=> [
					[
						'property' 	=> 'background-image',
						'value' 	=> 'url({{URL}});',
					]
				],
			],
			'_background_hover_image' => [
				'allow_static'	=> true,
				'condition' => '_background_hover_background',
				'selector' 	=> '{{WRAPPER}}:hover > .elementor-widget-container',
				'styles' 	=> [
					[
						'property' 	=> 'background-image',
						'value' 	=> 'url({{URL}});',
					]
				],
			],
		],
	];

	/**
	 * Set Template Id
	 *
	 * Sets the current template id
	 *
	 * @param 	int 		$template_id 	The template post ID
	 * @since 	2.2.4
	 * @return 	void
	 */
	public function set_template_id( $template_id ) {
		if ( ! $template_id )
			return;

		$this->template_id = $template_id;
	}

	/**
	 * Get Element Selector
	 *
	 * Retrieves the element selector for printing styles
	 *
	 * @param 	int 		$element_id 	The element ID
	 * @param 	int 		$post_id 		The current post ID in the loop
	 * @since 	2.2.4
	 * @return 	void
	 */
	protected function get_element_selector( $element_id, $post_id ) {
		$unique_id 	= $element_id . '-' . $post_id;

		return '.elementor-' . $this->template_id . ' .elementor-element.elementor-element-'. $element_id .'.elementor-ee-element-'. $unique_id;
	}

	/**
	 * Add Actions
	 *
	 * @param 	Element_Base 		$element 	The Elementor element object
	 * @since 	2.2.4
	 * @return 	void
	 */
	public function add_actions( $element ) {
		$this->parse_controls( $element );
	}

	/**
	 * Add Inline CSS
	 *
	 * Contains logic for determining wether the element needs
	 * template specific inline css addded before it's rendered
	 *
	 * @param 	Element_Base 		$element 	The Elementor element object
	 * @since 	2.2.4
	 * @return 	void
	 */
	protected function parse_controls( $element ) {

		$_settings 	= $element->get_settings();
		$settings 	= $element->get_settings_for_display();
		$has_styles = false;

		foreach ( $this->elements as $element_type => $styles ) {
			foreach ( $styles as $control_name => $control_settings ) {
				if ( 'classic' !== $element->get_settings( $control_settings['condition'] ) ) {
					continue;
				}

				$control = $element->get_controls( $control_name );

				if ( ! $control ) {
					continue;
				}

				$control_name = $control['name'];

				if ( empty( $control['type'] ) ) {
					continue;
				}

				$control_obj = \Elementor\Plugin::$instance->controls_manager->get_control( $control['type'] );

				if ( empty( $control['dynamic'] ) ) {
					continue;
				}

				$dynamic_settings = array_merge( $control_obj->get_settings( 'dynamic' ), $control['dynamic'] );

				if ( ! isset( $settings[ '__dynamic__' ][ $control_name ] ) ) {
					if ( true === $control_settings['allow_static'] && array_key_exists( $control_name, $settings ) ) {
						$parsed_value = $settings[ $control_name ];
					} else {
						continue;
					}
				} else {
					$parsed_value = $control_obj->parse_tags( $settings[ '__dynamic__' ][ $control_name ], $dynamic_settings );
				}

				if ( $parsed_value && array_key_exists( 'url' , $parsed_value ) && '' !== $parsed_value['url'] ) {
					$this->parse_control_styles( $element, $control, $parsed_value['url'] );
					$has_styles = true;
				} else {
					continue;
				}
			}
		}

		if ( $has_styles ) {
			$this->print_styles( $element );
		}
	}

	/**
	 * Passed through the control names and replaces
	 * css selectors and values
	 *
	 * @param 	Element_Base 		$element 	The Elementor element object
	 * @param 	Controls_Stack 		$control 	The control object
	 * @param 	string 				$value 		The value for the CSS style
	 * @since 	2.2.4
	 * @return 	void
	 */
	private function parse_control_styles( $element, $control, $value ) {
		$selector 			= $this->get_element_selector( $element->get_id(), get_the_ID() );
		$parsable_selector 	= $this->elements[ $element->get_type() ][ $control['name'] ]['selector'];
		$parsable_styles 	= $this->elements[ $element->get_type() ][ $control['name'] ]['styles'];

		// Replace selector
		$this->elements[ $element->get_type() ][ $control['name'] ]['selector'] = str_replace( '{{WRAPPER}}', $selector, $parsable_selector );
		
		// Replace url
		foreach ( $parsable_styles as $index => $style ) {
			$this->elements[ $element->get_type() ][ $control['name'] ]['styles'][ $index ]['value'] = str_replace( '{{URL}}', $value, $parsable_styles[ $index ]['value'] );
		}

		$this->elements[ $element->get_type() ][ $control['name'] ]['print'] = true;
	}

	/**
	 * Print Background Image Styles
	 *
	 * @param 	string 		$selector 	The base CSS selector
	 * @param 	string 		$url 	The base CSS selector
	 * @since 	2.2.4
	 * @return 	void
	 */
	private function print_styles( $element ) {
		$settings = $element->get_settings_for_display();

		echo '<style id="ee-template-loop-css-' . $this->template_id . '-' . $element->get_id() . '">';

		foreach ( $this->elements[ $element->get_type() ] as $control_name => $control_settings ) {
			if ( array_key_exists( 'print', $control_settings ) && true === $control_settings['print'] ) {
				echo $control_settings['selector'] . '{';
				foreach ( $control_settings['styles'] as $style ) {
					echo $style['property'] . ':' . $style['value'];
				}
				echo '}';		
			}
		}

		echo '</style>';
	}
}
