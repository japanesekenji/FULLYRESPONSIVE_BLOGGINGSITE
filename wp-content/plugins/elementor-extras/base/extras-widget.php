<?php

namespace ElementorExtras\Base;

use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class Extras_Widget extends Widget_Base {

	/**
	 * Wether or not we are in edit mode
	 *
	 * Used for the add_helper_render_attribute method which needs to
	 * add attributes only in edit mode.
	 *
	 * @access public
	 *
	 * @var bool
	 */
	public $_is_edit_mode = false;

	/**
	 * Loop Dynamic Settings
	 *
	 * Used to keep dynamic settings for posts inside
	 * a custom loop used in the widget
	 *
	 * @access private
	 * @since  2.2.2
	 *
	 * @var null|array
	 */
	private $ee_loop_dynamic_settings = [];

	/**
	 * Get Categories
	 * 
	 * Get the categories in which this widget can be found
	 *
	 * @since  1.6.0
	 * @return array
	 */
	public function get_categories() {
		return [ 'elementor-extras' ];
	}

	/**
	 * Widget base constructor.
	 *
	 * Initializing the widget base class.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @param array       $data Widget data. Default is an empty array.
	 * @param array|null  $args Optional. Widget default arguments. Default is null.
	 */
	public function __construct( $data = [], $args = null ) {

		parent::__construct( $data, $args );

		// Set edit mode
		$this->_is_edit_mode = \Elementor\Plugin::instance()->editor->is_edit_mode();
	}

	/**
	 * Method for adding editor helper attributes
	 *
	 * Adds attributes that enable a display of a label for a specific html element
	 *
	 * @access public
	 * @since 1.6.0
	 * @return void
	 */
	public function add_helper_render_attribute( $key, $name = '' ) {

		if ( ! $this->_is_edit_mode )
			return;

		$this->add_render_attribute( $key, [
			'data-ee-helper' 	=> $name,
			'class'				=> 'ee-editor-helper',
		] );
	}

	/**
	 * Method for adding a placeholder for the widget in the preview area
	 *
	 * @access public
	 * @since 2.0.0
	 * @return void
	 */
	public function render_placeholder( $args ) {

		if ( ! $this->_is_edit_mode )
			return;

		$defaults = [
			'title_tag' => 'h4',
			'title' => $this->get_title(),
			'body' 	=> __( 'This is a placeholder for this widget and will not shown on the page.', 'elementor-extras' ),
		];

		$args = wp_parse_args( $args, $defaults );

		$this->add_render_attribute([
			'ee-placeholder' => [
				'class' => 'ee-editor-placeholder',
			],
			'ee-placeholder-title' => [
				'class' => 'ee-editor-placeholder__title',
			],
			'ee-placeholder-body' => [
				'class' => 'ee-editor-placeholder__body',
			],
		]);

		?><div <?php echo $this->get_render_attribute_string( 'ee-placeholder' ); ?>>
			<<?php echo $args['title_tag']; ?> <?php echo $this->get_render_attribute_string( 'ee-placeholder-title' ); ?>>
				<?php echo $args['title']; ?>
			</<?php echo $args['title_tag']; ?>>
			<div <?php echo $this->get_render_attribute_string( 'ee-placeholder-body' ); ?>><?php echo $args['body']; ?></div>
		</div><?php
	}

	/**
	 * Method for setting widget dependancy on Elementor Pro plugin
	 *
	 * When returning true it doesn't allow the widget to be registered
	 *
	 * @access public
	 * @since 1.6.0
	 * @return bool
	 */
	public static function requires_elementor_pro() {
		return false;
	}

	/**
	 * Get skin setting
	 *
	 * Retrieves the current skin setting
	 *
	 * @access protected
	 * @since 2.1.0
	 * @return mixed
	 */
	protected function get_skin_setting( $setting_key ) {
		if ( ! $setting_key )
			return false;

		return $this->get_current_skin()->get_instance_value( $setting_key );
	}

	/**
	 * Set Loop Dynamic Settings
	 *
	 * @access protected
	 * @since 2.2.2
	 * @return void
	 *
	 * @param WP_Query 	  $query      The query to generate the dynamic settings for
	 */
	protected function set_settings_for_loop( $query ) {

		global $wp_query;

		// Temporarily force a query for the template and set it as the currenty query
		$old_query 	= $wp_query;
		$wp_query 	= $query;

		while ( $query->have_posts() ) {

			$query->the_post();

			$this->set_settings_for_post( get_the_ID() );
		}

		// Revert to the initial query
		$wp_query = $old_query;

		wp_reset_postdata();
	}

	/**
	 * Set Post Dynamic Settings
	 *
	 * @access protected
	 * @since 2.2.33
	 * @return void
	 *
	 * @param int $post_id The post to generate the dynamic settings for
	 */
	protected function set_settings_for_post( $post_id ) {
		if ( ! $post_id ) {
			return;
		}

		$settings 		= $this->get_settings_for_display();
		$all_settings 	= $this->get_settings();
		$controls 		= $this->get_controls();
		
		$this->ee_loop_dynamic_settings[ $post_id ] = [];

		foreach ( $controls as $control ) {
			$control_name = $control['name'];
			$control_obj = \Elementor\Plugin::$instance->controls_manager->get_control( $control['type'] );

			if ( empty( $control['dynamic'] ) ) {
				continue;
			}

			$dynamic_settings = array_merge( $control_obj->get_settings( 'dynamic' ), $control['dynamic'] );
			$parsed_value = '';

			if ( ! isset( $all_settings[ '__dynamic__' ][ $control_name ] ) || empty( $control['dynamic']['loop'] ) ) {
				$parsed_value = $all_settings[ $control_name ];
			} else {
				$parsed_value = $control_obj->parse_tags( $settings[ '__dynamic__' ][ $control_name ], $dynamic_settings );
			}

			$this->ee_loop_dynamic_settings[ $post_id ][ $control_name ] = $parsed_value;
		}
	}

	/**
	 * Get Loop Dynamic Settings
	 *
	 * Fetches the dynamic settings for all looped posts
	 * or for a single post
	 *
	 * @access protected
	 * @since 2.2.2
	 * @return array
	 *
	 * @param int 	  $post_id      The ID of the post for which to fetch the settings
	 */
	protected function get_settings_for_loop_display( $post_id = false ) {

		if ( $post_id ) {
			if ( array_key_exists( $post_id, $this->ee_loop_dynamic_settings ) ) {
				return $this->ee_loop_dynamic_settings[ $post_id ];
			}
		}

		return $this->ee_loop_dynamic_settings;
	}

	/**
	 * Get ID for Loop
	 * 
	 * Returns a unique ID based on the global post id and widget id
	 *
	 * @since  2.2.33
	 * @return tring
	 */
	public function get_id_for_loop() {
		global $post;

		if ( ! $post ) {
			return $this->get_id();
		}

		return implode( '_', [ $this->get_id(), $post->ID ] );
	}
}
