<?php

namespace ElementorExtras\Extensions;

use ElementorExtras\Base\Extension_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Sticky Extension
 *
 * Adds sticky on scroll capability to widgets and sections
 *
 * @since 0.1.0
 */
class Extension_Sticky_Elements extends Extension_Base {

	/**
	 * Is Common Extension
	 *
	 * Defines if the current extension is common for all element types or not
	 *
	 * @since 1.8.0
	 * @access protected
	 *
	 * @var bool
	 */
	protected $is_common = true;

	/**
	 * A list of scripts that the widgets is depended in
	 *
	 * @since 1.8.0
	 **/
	public function get_script_depends() {
		return [
			'hc-sticky',
			'jquery-resize-ee',
		];
	}

	/**
	 * Is disabled by default
	 *
	 * Return wether or not the extension should be disabled by default,
	 * prior to user actually saving a value in the admin page.
	 * Checks if Elementor Pro is enabled to allow for use of default Sticky
	 *
	 * @access public
	 * @since 2.1.0
	 * @return bool
	 */
	public static function is_default_disabled() {
		if ( is_elementor_pro_active() ) {
			return true;
		}
		return false;
	}

	/**
	 * The description of the current extension
	 *
	 * @since 1.8.0
	 **/
	public static function get_description() {

		$message = '';

		if ( is_elementor_pro_active() ) {
			$message = '<div class="ee-admin-notice ee-admin-notice--info notice notice-warning inline"><p>';
			$message .= __( '<strong>IMPORTANT:</strong> Enabling this extension disables the default Elementor Pro sticky options.', 'elementor-extras' );
			$message .= '</p></div>';
		}

		$message .= __( 'Adds an option to make any widget or section sticky when scrolling to it\'s position. Can be found under Advanced &rarr; Extras &rarr; Sticky.', 'elementor-extras' );

		return $message;
	}

	/**
	 * Add common sections
	 *
	 * @since 1.8.0
	 *
	 * @access protected
	 */
	protected function add_common_sections_actions() {

		// Activate sections for widgets
		add_action( 'elementor/element/common/section_custom_css/after_section_end', function( $element, $args ) {

			$this->add_common_sections( $element, $args );

		}, 10, 2 );

		// Activate sections for sections
		add_action( 'elementor/element/section/section_custom_css/after_section_end', function( $element, $args ) {

			$this->add_common_sections( $element, $args );

		}, 10, 2 );

		// Activate sections for sections
		add_action( 'elementor/element/section/section_custom_css_pro/after_section_end', function( $element, $args ) {

			$this->add_common_sections( $element, $args );

		}, 10, 2 );

	}

	/**
	 * Add Controls
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 */
	private function add_controls( $element, $args ) {

		$sticky_control_args = [ 'name' => 'sticky', 'render_type' ];

		if ( $element->get_type() === 'section' ) {
			
			$element->add_control(
				'sticky_warning',
				[
					'type' 					=> Controls_Manager::RAW_HTML,
					'raw' 					=> __( 'You cannot make this section sticky if the "Stretch Section" is enabled. To make it work, use a section within a section, make the outer section stretched and the inner section sticky.', 'elementor-extras' ),
					'content_classes' 		=> 'elementor-panel-alert elementor-panel-alert-danger',
					'separator'				=> 'before',
					'condition'				=> [
						'stretch_section' 	=> 'section-stretched',
					]
				]
			);
		}

		$element->add_control( 'sticky_enable', [
			'label'			=> _x( 'Sticky', 'Sticky Control', 'elementor-extras' ),
			'type' 			=> Controls_Manager::SWITCHER,
			'default' 		=> '',
			'separator'		=> 'before',
			'label_on' 		=> __( 'Yes', 'elementor-extras' ),
			'label_off' 	=> __( 'No', 'elementor-extras' ),
			'return_value' 	=> 'yes',
			'frontend_available'	=> true,
		]);

		$element->add_control(
			'sticky_info',
			[
				'type' 					=> Controls_Manager::RAW_HTML,
				'raw' 					=> __( 'If your section contains just this element, make sure "Vertical Align" is set to "Top" for any parent sections.', 'elementor-extras' ),
				'content_classes' 		=> 'elementor-panel-alert elementor-panel-alert-info',
				'separator'				=> 'before',
				'condition' 			=> [
					'sticky_enable!' 	=> '',
				],
			]
		);

		$parent_options = [
			'' 			=> __( 'Parent', 'elementor-extras' ),
			'body' 		=> __( 'Page Body', 'elementor-extras' ),
			'custom' 	=> __( 'Custom Parent', 'elementor-extras' ),
		];

		if ( 'widget' === $element->get_type() ) {
			$parent_options[''] = __( 'Parent Column', 'elementor-extras' );
			$parent_options['section'] = __( 'Parent Section', 'elementor-extras' );
		}

		$element->add_control( 'sticky_parent', [
			'label'			=> _x( 'Stay in', 'Sticky Control', 'elementor-extras' ),
			'type' 			=> Controls_Manager::SELECT,
			'default' 		=> '',
			'options'		=> $parent_options,
			'condition' => [
				'sticky_enable!' => '',
			],
			'frontend_available'	=> true,
		]);

		$element->add_control( 'sticky_parent_selector', [
			'label'			=> _x( 'Parent Selector', 'Sticky Control', 'elementor-extras' ),
			'title' 		=> __( 'Add your custom id or class WITH the Pound or Dot key. e.g: #my-id or .my-class', 'elementor-extras' ),
			'description'	=> sprintf( __( 'Set a class or ID to a column, section or any element that is a parent of this %s, then add that class here.', 'elementor-extras' ), $element->get_type() ),
			'type' 			=> Controls_Manager::TEXT,
			'default' 		=> '',
			'frontend_available'	=> true,
			'condition' 	=> [
				'sticky_parent' 	=> 'custom'
			]
		]);

		$element->add_control( 'sticky_unstick_on', [
			'label' 	=> _x( 'Unstick on', 'Sticky Control', 'elementor-extras' ),
			'type' 		=> Controls_Manager::SELECT,
			'default' 	=> 'mobile',
			'options' 			=> [
				'none' 		=> __( 'None', 'elementor-extras' ),
				'tablet' 	=> __( 'Mobile and tablet', 'elementor-extras' ),
				'mobile' 	=> __( 'Mobile only', 'elementor-extras' ),
			],
			'condition' => [
				'sticky_enable!' => '',
			],
			'frontend_available' => true,
		]);

		$element->add_control( 'sticky_follow_scroll', [
			'label'			=> _x( 'Follow Scroll', 'Sticky Control', 'elementor-extras' ),
			'description'	=> __( 'When disabled, the sticky element will not move with the page if it is bigger than the browser window.', 'elementor-extras' ),
			'type' 			=> Controls_Manager::SWITCHER,
			'default' 		=> 'yes',
			'label_on' 		=> __( 'Yes', 'elementor-extras' ),
			'label_off' 	=> __( 'No', 'elementor-extras' ),
			'return_value' 	=> 'yes',
			'condition' => [
				'sticky_enable!' => '',
			],
			'frontend_available'	=> true,
		]);

		$element->add_control( 'sticky_offset', [
			'label' 	=> _x( 'Offset Top', 'Sticky Control', 'elementor-extras' ),
			'type' 		=> Controls_Manager::SLIDER,
			'range' 	=> [
				'px' 	=> [
					'max' => 100,
				],
			],
			'default' 	=> [
				'size' 	=> 0,
			],
			'condition'		=> [
	        	'sticky_enable!' => '',
	        ],
	        'frontend_available' => true,
		]);

		$element->add_control( 'sticky_offset_bottom', [
			'label' 	=> _x( 'Offset Bottom', 'Sticky Control', 'elementor-extras' ),
			'type' 		=> Controls_Manager::SLIDER,
			'range' 	=> [
				'px' 	=> [
					'max' => 100,
				],
			],
			'default' 	=> [
				'size' 	=> 0,
			],
			'condition'		=> [
	        	'sticky_enable!' => '',
	        ],
	        'frontend_available' => true,
		]);

	}

	/**
	 * Remove elementor sticky controls
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 */
	protected function remove_elementor_sticky( $element ) {

		$this->remove_controls( $element, [
			'sticky',
			'sticky_effects_offset',
			'sticky_on',
			'sticky_offset',
			'sticky_parent',
		] );
	}

	protected function add_elementor_sticky_warning( $element ) {

		$element->add_control(
			'sticky_elementor_warning',
			[
				'type' 					=> Controls_Manager::RAW_HTML,
				'raw' 					=> __( 'Extras for Elementor: To use the default Sticky options in Elementor, disable the Extras "Sticky Elements" extension under Elementor > Extras > Extensions.', 'elementor-extras' ),
				'content_classes' 		=> 'elementor-panel-alert elementor-panel-alert-info',
				'separator'				=> 'before',
			]
		);

	}

	/**
	 * Add Actions
	 *
	 * @since 0.1.0
	 *
	 * @access protected
	 */
	protected function add_actions() {

		// Activate controls for widgets
		add_action( 'elementor/element/common/section_elementor_extras_advanced/before_section_end', function( $element, $args ) {

			$this->remove_elementor_sticky( $element );
			$this->add_controls( $element, $args );

		}, 10, 2 );

		// Activate controls for sections
		add_action( 'elementor/element/section/section_elementor_extras_advanced/before_section_end', function( $element, $args ) {

			$this->remove_elementor_sticky( $element );
			$this->add_controls( $element, $args );

		}, 10, 2 );

		add_action( 'elementor/element/common/section_effects/after_section_start', function( $element, $args ) {

			$this->add_elementor_sticky_warning( $element );

		}, 10, 2 );

		add_action( 'elementor/element/section/section_effects/after_section_start', function( $element, $args ) {

			$this->add_elementor_sticky_warning( $element );

		}, 10, 2 );
	}

}