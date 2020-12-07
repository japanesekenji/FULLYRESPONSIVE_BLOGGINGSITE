<?php
namespace ElementorExtras\Modules\TemplatesControl;

// Extras for Elementor Classes
use ElementorExtras\Utils;
use ElementorExtras\Base\Module_Base;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @since 2.0.0
 */
class Module extends Module_Base {

	/**
	 * @since 2.0.0
	 */
	public function get_name() {
		return 'templates';
	}

	/**
	 * @since 2.0.0
	 */
	public function get_widgets() {
		return [];
	}

	/**
	 * @since 2.0.0
	 */
	protected static function get_templates( $args = [] ) {

		if ( ! method_exists( '\Elementor\TemplateLibrary\Manager', 'get_source' ) ||
			 ! method_exists( '\Elementor\TemplateLibrary\Source_Local', 'get_items' ) )
			return;

		return Utils::elementor()->templates_manager->get_source( 'local' )->get_items( $args );
	}

	/**
	 * Markup for when no templates exist
	 *
	 * @since 2.0.0
	 * @return string
	 */
	protected static function empty_templates_message( $template_type = '' ) {
		return '<div id="elementor-widget-template-empty-templates">
				<div class="elementor-widget-template-empty-templates-icon"><i class="eicon-nerd"></i></div>
				<div class="elementor-widget-template-empty-templates-title">' . sprintf( __( 'You Haven’t Saved %sTemplates Yet.', 'elementor-extras' ), ucfirst( $template_type ) . ' ' ) . '</div>
				<div class="elementor-widget-template-empty-templates-footer">' . __( 'Want to learn more about Elementor library?', 'elementor-extras' ) . ' <a class="elementor-widget-template-empty-templates-footer-url" href="https://go.elementor.com/docs-library/" target="_blank">' . __( 'Click Here', 'elementor-extras' ) . '</a>
				</div>
				</div>';
	}

	/**
	 * Add Render Attributes
	 *
	 * Action that adds additional attributes to the element specific
	 * to the loop
	 *
	 * @param 	Element_Base 		$element 	The Elementor element object
	 * @since 	2.2.31
	 * @return 	void
	 */
	public static function add_render_attributes( $element ) {
		$unique_id 	= implode( '-', [ $element->get_id(), get_the_ID() ] );

		$element->add_render_attribute( [
			'_wrapper' => [
				'data-ee-template-widget-id' => $unique_id,
				'class' => [
					'elementor-ee-element-' . $unique_id,
				],
			],
		] );
	}

	/**
	 * Add Controls
	 *
	 * Registers all module controls
	 *
	 * @param Element_Base		$object 	The element object
	 * @param array 			$args 		The settings for the controls
	 * @since 2.0.0
	 */
	public static function add_controls( $object, $args = [] ) {

		$defaults = [
			'type' => [ 'section', 'page', 'widget' ],
			'condition' => [],
			'prefix' => '',
		];

		$args = wp_parse_args( $args, $defaults );

		self::add_types_control( $object, $args );

		if ( ! empty( $args['type'] ) ) {
			if ( is_array( $args['type'] ) ) {
				foreach ( $args['type'] as $type ) {
					self::add_control( $object, $args, $type );
				}
			} else {
				self::add_control( $object, $args, $args['type'] );
			}
		}
	}

	/**
	 * Add Types Control
	 *
	 * Registers a select control for selecting template types
	 *
	 * @param Element_Base		$object 	The element object
	 * @param array 			$args 		The settings for the control
	 * @since 2.0.0
	 */
	protected static function add_types_control( $object, $args = [] ) {

		if ( ! $object )
			return;

		$object->add_control(
			$args['prefix'] . 'template_type',
			[
				'label'		=> __( 'Template Type', 'elementor-extras' ),
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> 'section',
				'options' 	=> [
					'section'	=> __( 'Section', 'elementor-extras' ),
					'page'		=> __( 'Page', 'elementor-extras' ),
					'widget'	=> __( 'Widget', 'elementor-extras' ),
				],
				'condition' 	=> $args['condition'],
			]
		);
	}

	/**
	 * Add Control
	 *
	 * Registers the template selection control
	 *
	 * @param Element_Base		$object 	The element object
	 * @param array 			$args 		The settings for the control
	 * @param string 			$type 		The type of template
	 * @since 2.0.0
	 */
	protected static function add_control( $object, $args = [], $type = 'section' ) {
		$defaults = [];

		$args = wp_parse_args( $args, $defaults );

		$templates 	= self::get_templates( [ 'type' => $type ] );
		$options 	= [];
		$types 		= [];

		$prefix 			= $args['prefix'];
		$no_templates_key 	= $prefix . 'no_' . $type . '_templates';
		$templates_key 		= $prefix . $type . '_template_id';

		if ( empty( $templates ) ) {

			$object->add_control(
				$no_templates_key,
				[
					'label' => false,
					'type' 	=> Controls_Manager::RAW_HTML,
					'raw' 	=> self::empty_templates_message( $type ),
					'condition'	=> array_merge( $args['condition'], [
						$args['prefix'] . 'template_type' => $type
					] ),
				]
			);

			return;
		}

		$options['0'] = '— ' . sprintf( __( 'Select %s', 'elementor-extras' ), $type ) . ' —';

		foreach ( $templates as $template ) {
			$options[ $template['template_id'] ] = $template['title'] . ' (' . $template['type'] . ')';
		}

		$object->add_control(
			$templates_key,
			[
				'label' 		=> sprintf( __( 'Choose %s', 'elementor-extras' ), $type ),
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> '0',
				'options' 		=> $options,
				'condition'		=> array_merge( $args['condition'], [
					$prefix . 'template_type' => $type,
				] ),
			]
		);
	}

	/**
	 * Render Template Content
	 *
	 * Renders the content of an Elementor template for with the specified post ID
	 *
	 * @param int 									$template_id 	The template post ID
	 * @param \ElementorExtras\Base\Extras_Widget 	$widget 		The widget instance
	 * @since 2.0.0
	 */
	public static function render_template_content( $template_id, \ElementorExtras\Base\Extras_Widget $widget, $in_loop = false ) {

		if ( 'publish' !== get_post_status( $template_id ) || ! method_exists( '\Elementor\Frontend', 'get_builder_content_for_display' ) ) {
			return;
		}

		if ( ! $template_id ) {
			if ( method_exists( $widget, 'render_placeholder' ) ) {
				$widget->render_placeholder([
					'title_tag' => 'h5',
					'title' 	=> __( 'Missing Template', 'elementor-extras' ),
					'body'		=> __( 'Set the skin template you want to use in the widget settings.', 'elementor-extras' ),
				]);
			} else {
				_e( 'No template selected.', 'elementor-extras' );
			}
		} else {

			global $wp_query;

			$print_styles = false;

			if ( $in_loop ) { // If we're inside a loop we need to make sure the global query is replaced with the current post

				$print_styles = true;

				// Keep old global wp_query 
	    		$old_query 	= $wp_query;

	    		// Create a new query from the current post in loop
	    		$new_query 	= new \WP_Query( [
	    			'post_type' => 'any',
	    			'p' => get_the_ID(),
	    		] );

	    		// Set the global query to the new query
	    		$wp_query 	= $new_query;
			}

    		$background = new BackgroundImage();
    		$background->set_template_id( $template_id );

    		if ( $in_loop ) {
	    		// Alter element rendering to account for template
	    		add_action( 'elementor/frontend/before_render', [ __CLASS__, 'add_render_attributes' ], 10, 1 );
	    		add_action( 'elementor/frontend/before_render', [ $background, 'add_actions' ], 20, 1 );
    		}

    		// Fetch the template
			$template = Utils::elementor()->frontend->get_builder_content_for_display( $template_id, $print_styles );

			if ( $in_loop ) {
				// Remove action
				remove_action( 'elementor/frontend/before_render', [ $background, 'add_actions' ] );
				remove_action( 'elementor/frontend/before_render', [ __CLASS__, 'add_render_attributes' ] );

				// Revert to the initial query
				$wp_query = $old_query;
			}

			?><div class="elementor-template"><?php echo $template; ?></div><?php
		}
	}
}
