<?php
namespace ElementorExtras;

use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Group Control Tooltip
 *
 * @since 1.8.0
 */
class Group_Control_Tooltip extends Group_Control_Base {

	protected static $fields;

	/**
	 * @since 1.8.0
	 * @access public
	 */
	public static function get_type() {
		return 'ee-tooltip';
	}

	/**
	 * @since 1.8.0
	 * @access protected
	 */
	protected function init_fields() {
		$controls = [];

		$controls['content'] = [
			'label'			=> _x( 'Content', 'Tooltip Control', 'elementor-extras' ),
			'type' 			=> Controls_Manager::TEXT,
			'default' 		=> __( 'I am a tooltip', 'elementor-extras' ),
			'dynamic' 		=> [ 'active' => true ],
			'frontend_available'	=> true,
		];

		$controls['target'] = [
			'label'		=> __( 'Target', 'elementor-extras' ),
			'type' 		=> Controls_Manager::SELECT,
			'default' 	=> 'element',
			'options' 	=> [
				'element' 	=> __( 'Current Element', 'elementor-extras' ),
				'custom' 	=> __( 'Custom Selector', 'elementor-extras' ),
			],
			'frontend_available' => true
		];

		$controls['selector'] = [
			'label'			=> _x( 'CSS Selector', 'Tooltip Control', 'elementor-extras' ),
			'description'	=> __( 'Use a CSS selector for any html element WITHIN this element.', 'elementor-extras' ),
			'type' 			=> Controls_Manager::TEXT,
			'default' 		=> '',
			'placeholder' 	=> __( '.css-selector', 'elementor-extras' ),
			'frontend_available'	=> true,
			'condition'	=> [
				'target' => 'custom',
			],
		];

		$controls['trigger'] = [
			'responsive'=> true,
			'label'		=> __( 'Trigger', 'elementor-extras' ),
			'type' 		=> Controls_Manager::SELECT,
			'default' 	=> 'mouseenter',
			'tablet_default' 	=> 'click_target',
			'mobile_default' 	=> 'click_target',
			'options' 	=> [
				'mouseenter' 	=> __( 'Mouse Over', 'elementor-extras' ),
				'click_target' 	=> __( 'Click Target', 'elementor-extras' ),
				'load' 			=> __( 'Page Load', 'elementor-extras' ),
			],
			'frontend_available' => true
		];

		$controls['trigger_warning'] = [
			'type' 					=> Controls_Manager::RAW_HTML,
			'raw' 					=> __( 'Notice: If you element is a link, clicking it will result in both opening the tooltip and navigating to the link URL.', 'elementor-extras' ),
			'content_classes' 		=> 'elementor-panel-alert elementor-panel-alert-warning',
			'condition' 			=> [
				'trigger' 			=> 'click_target',
			],
		];

		$controls['_hide'] = [
			'responsive'=> true,
			'label'		=> __( 'Hide on', 'elementor-extras' ),
			'type' 		=> Controls_Manager::SELECT,
			'default' 			=> 'mouseleave',
			'tablet_default' 	=> 'click_any',
			'mobile_default' 	=> 'click_any',
			'options' 	=> [
				'mouseleave' 	=> __( 'Mouse Leave', 'elementor-extras' ),
				'click_out' 	=> __( 'Click Outside', 'elementor-extras' ),
				'click_target' 	=> __( 'Click Target', 'elementor-extras' ),
				'click_any' 	=> __( 'Click Anywhere', 'elementor-extras' ),
			],
			'frontend_available' => true
		];

		$controls['position'] = [
			'label'			=> _x( 'Show to', 'Tooltip Control', 'elementor-extras' ),
			'type' 		=> Controls_Manager::SELECT,
			'default' 	=> '',
			'options' 	=> [
				'' 			=> __( 'Global', 'elementor-extras' ),
				'bottom' 	=> __( 'Bottom', 'elementor-extras' ),
				'left' 		=> __( 'Left', 'elementor-extras' ),
				'top' 		=> __( 'Top', 'elementor-extras' ),
				'right' 	=> __( 'Right', 'elementor-extras' ),
			],
			'frontend_available' => true
		];

		$controls['arrow_position_h'] = [
			'label'			=> _x( 'Show at', 'Tooltip Control', 'elementor-extras' ),
			'type' 		=> Controls_Manager::SELECT,
			'default' 	=> '',
			'options' 	=> [
				'' 			=> __( 'Global', 'elementor-extras' ),
				'center' 	=> __( 'Center', 'elementor-extras' ),
				'left' 		=> __( 'Left', 'elementor-extras' ),
				'right' 	=> __( 'Right', 'elementor-extras' ),
			],
			'condition'		=> [
				'position'	=> [ 'top', 'bottom' ],
			],
			'frontend_available' => true
		];

		$controls['arrow_position_v'] = [
			'label'			=> _x( 'Show at', 'Tooltip Control', 'elementor-extras' ),
			'type' 		=> Controls_Manager::SELECT,
			'default' 	=> '',
			'options' 	=> [
				'' 			=> __( 'Global', 'elementor-extras' ),
				'center' 	=> __( 'Center', 'elementor-extras' ),
				'bottom' 	=> __( 'Bottom', 'elementor-extras' ),
				'top' 		=> __( 'Top', 'elementor-extras' ),
			],
			'condition'		=> [
				'position'	=> [ 'left', 'right' ],
			],
			'frontend_available' => true
		];

		$controls['css_position'] = [
			'label' 		=> _x( 'CSS Position', 'Tooltip Control', 'elementor-extras' ),
			'type' 			=> Controls_Manager::SELECT,
			'default' 		=> '',
			'options'		=> [
				'' 			=> 'Absolute',
				'fixed'		=> 'Fixed',
			],
			'frontend_available' => true,
		];

		$controls['disable'] = [
			'label'		=> _x( 'Disable On', 'Tooltip Control', 'elementor-extras' ),
			'type' 		=> Controls_Manager::SELECT,
			'default' 	=> '',
			'options' 	=> [
				'' 			=> __( 'None', 'elementor-extras' ),
				'tablet' 	=> __( 'Tablet & Mobile', 'elementor-extras' ),
				'mobile' 	=> __( 'Mobile', 'elementor-extras' ),
			],
			'frontend_available' => true
		];

		$controls['delay_in'] = [
			'label' 		=> _x( 'Delay in (s)', 'Tooltip Control', 'elementor-extras' ),
			'type' 			=> Controls_Manager::SLIDER,
			'range' 	=> [
				'px' 	=> [
					'min' 	=> 0,
					'max' 	=> 1,
					'step'	=> 0.1,
				],
			],
			'frontend_available' => true
		];

		$controls['delay_out'] = [
			'label' 		=> _x( 'Delay out (s)', 'Tooltip Control', 'elementor-extras' ),
			'type' 			=> Controls_Manager::SLIDER,
			'range' 	=> [
				'px' 	=> [
					'min' 	=> 0,
					'max' 	=> 1,
					'step'	=> 0.1,
				],
			],
			'frontend_available' => true
		];

		$controls['duration'] = [
			'label' 		=> _x( 'Duration', 'Tooltip Control', 'elementor-extras' ),
			'type' 			=> Controls_Manager::SLIDER,
			'range' 	=> [
				'px' 	=> [
					'min' 	=> 0,
					'max' 	=> 2,
					'step'	=> 0.1,
				],
			],
			'frontend_available' => true
		];

		return $controls;
	}

	/**
	 * @since 1.8.0
	 * @access protected
	 */
	protected function get_default_options() {
		return [
			'popover' => false,
		];
	}
}
