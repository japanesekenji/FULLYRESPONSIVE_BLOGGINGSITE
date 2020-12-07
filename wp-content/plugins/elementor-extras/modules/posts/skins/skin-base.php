<?php
namespace ElementorExtras\Modules\Posts\Skins;

// Extras for Elementor Classes
use ElementorExtras\Utils;
use ElementorExtras\Base\Extras_Widget;
use ElementorExtras\Modules\Posts\Module as PostsModule;
use ElementorExtras\Modules\CustomFields\Module as CustomFields;
use ElementorExtras\Modules\TemplatesControl\Module as TemplatesControl;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\Posts\Skins
 *
 * @since  1.6.0
 */
abstract class Skin_Base extends Elementor_Skin_Base {

	/**
	 * Register Controls Actions
	 * 
	 * Registers controls at specific points in the Controls Stack
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function _register_controls_actions() {
		add_action( 'elementor/element/posts-extra/section_layout/before_section_end', [ $this, 'register_controls' ] );
		add_action( 'elementor/element/posts-extra/section_layout/before_section_end', [ $this, 'register_controls' ] );
	}

	/**
	 * Register Controls
	 *
	 * @since  1.6.0
	 * @return void
	 * @param  $widget Extras_Widget
	 */
	public function register_controls( Extras_Widget $widget ) {
		$this->parent 	= $widget;

		$this->register_before_skin_controls();
		$this->register_layout_content_controls();
	}

	/**
	 * Register Before Skin Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	public function register_before_skin_controls() {
		$this->parent->start_injection( [
			'at' => 'before',
			'of' => '_skin',
		] );

			$this->add_control(
				'widget_helpers',
				[
					'label' 		=> __( 'Editor Helper', 'elementor-extras' ),
					'description'	=> __( 'Shows labels overlaid on posts to help your easily identify each post area', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default'		=> '',
					'return_value' 	=> 'on',
					'prefix_class'	=> 'ee-posts-helpers-',
				]
			);

		$this->parent->end_injection();
	}

	/**
	 * Register Layout Content Controls
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function register_layout_content_controls() {

		$this->add_responsive_control(
			'grid_columns_spacing',
			[
				'label' 			=> __( 'Columns Spacing', 'elementor-extras' ),
				'type' 				=> Controls_Manager::SLIDER,
				'default'			=> [
					'size' => 24,
					'unit' => 'px',
				],
				'tablet_default'	=> [
					'size' => 12,
					'unit' => 'px',
				],
				'mobile_default'	=> [
					'size' => 0,
					'unit' => 'px',
				],
				'size_units' 		=> [ 'px' ],
				'range' 			=> [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'grid_rows_spacing',
			[
				'label' 			=> __( 'Rows Spacing', 'elementor-extras' ),
				'type' 				=> Controls_Manager::SLIDER,
				'size_units' 		=> [ 'px' ],
				'default'			=> [
					'size' => 24,
					'unit' => 'px',
				],
				'tablet_default'	=> [
					'size' => 12,
					'unit' => 'px',
				],
				'mobile_default'	=> [
					'size' => 0,
					'unit' => 'px',
				],
				'range' 		=> [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ee-post' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'layout_align',
			[
				'label' 		=> __( 'Vertical Align', 'elementor-extras' ),
				'type' 			=> Controls_Manager::CHOOSE,
				'default' 		=> 'stretch',
				'options' 		=> [
					'top' 			=> [
						'title' 	=> __( 'Top', 'elementor-extras' ),
						'icon' 		=> 'eicon-v-align-top',
					],
					'middle' 		=> [
						'title' 	=> __( 'Middle', 'elementor-extras' ),
						'icon' 		=> 'eicon-v-align-middle',
					],
					'bottom' 		=> [
						'title' 	=> __( 'Bottom', 'elementor-extras' ),
						'icon' 		=> 'eicon-v-align-bottom',
					],
					'stretch' 		=> [
						'title' 	=> __( 'Stretch', 'elementor-extras' ),
						'icon' 		=> 'eicon-v-align-stretch',
					],
				],
				'prefix_class' 	=> 'ee-grid-align%s--',
				'condition' 	=> [
					$this->get_control_id( 'layout' ) => 'default',
				],
			]
		);

		$this->add_control(
			'layout_equalize',
			[
				'label' 		=> __( 'Force Equal Height', 'elementor-extras' ),
				'description'	=> __( 'This option tries to give a 100% height to all relevant wrappers inside your template. You will still need to set your desired columns and sections Vertical Align attribute to Space Between, Space Around or Space Evenly if you want your elements to stretch.', 'elementor-extras' ),
				'type' 			=> Controls_Manager::SWITCHER,
				'default'		=> '',
				'return_value' 	=> 'stretch',
				'prefix_class'	=> 'ee-posts-skin--',
				'condition'		=> [
					'skin_source' => 'template',
					$this->get_control_id( 'layout_align' ) => 'stretch',
				],
			]
		);

		$this->add_responsive_control(
			'layout_halign',
			[
				'label' 		=> __( 'Horizontal Align', 'elementor-extras' ),
				'type' 			=> Controls_Manager::CHOOSE,
				'default' 		=> 'left',
				'options' 		=> [
					'left'    		=> [
						'title' 	=> __( 'Left', 'elementor-extras' ),
						'icon' 		=> 'eicon-h-align-left',
					],
					'center' 		=> [
						'title' 	=> __( 'Center', 'elementor-extras' ),
						'icon' 		=> 'eicon-h-align-center',
					],
					'right' 		=> [
						'title' 	=> __( 'Right', 'elementor-extras' ),
						'icon' 		=> 'eicon-h-align-right',
					],
				],
				'prefix_class' 	=> 'ee-grid-halign%s--',
				'condition' 	=> [
					$this->get_control_id( 'layout' ) => 'default',
				],
			]
		);
	}

	/**
	 * Get Post Link
	 * 
	 * Return the filtered post link
	 *
	 * @since  2.2.6
	 * @return void
	 */
	protected function get_post_link( $post ) {
		/**
		 * Global Link Filter
		 *
		 * Filters the post media link. This is available only if the whole media area
		 * is linked when the Content > Media > Link to Post option is enabled.
		 *
		 * @since 2.2.6
		 * @param array 			$link 	The original post link
		 * @param object|WP_Post 	$post  	The current post
		 */
		return apply_filters( 'elementor_extras/widgets/posts/link', get_permalink( $post ), $post );
	}

	/**
	 * Render
	 * 
	 * Render widget contents on frontend
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function render() {

		$this->parent->render();

		$this->parent->query_posts();

		$wp_query = $this->parent->get_query();

		if ( ! $wp_query->found_posts ) {
			$this->render_not_found();
			return;
		}

		// Add filters
		add_filter( 'excerpt_more', [ $this, 'custom_excerpt_more_filter' ], 999 );
		add_filter( 'excerpt_length', [ $this, 'custom_excerpt_length' ], 999 );
		add_filter( 'wp_calculate_image_srcset_meta', '__return_null' );

		$this->before_loop();
		$this->render_loop_start();
		$this->render_sizer();

		while ( $wp_query->have_posts() ) {

			$wp_query->the_post();
			
			$index = $wp_query->current_post + 1;
			$settings = $this->parent->get_settings();

			$this->render_post_start();

			switch ( $settings['skin_source'] ) {
				case 'template':
					TemplatesControl::render_template_content( $this->parent->get_settings( 'skin_template' ), $this->parent, true );
					break;
				default:
					$this->render_post();
					break;
			}

			$this->render_post_end();
		}

		wp_reset_postdata();
		wp_reset_query();

		$this->render_loop_end();
		$this->after_loop();

		$this->render_scripts();

		// Remove filters
		remove_filter( 'wp_calculate_image_srcset_meta', '__return_null' );
		remove_filter( 'excerpt_length', [ $this, 'custom_excerpt_length' ], 999 );
		remove_filter( 'excerpt_more', [ $this, 'custom_excerpt_more_filter' ], 999 );
	}

	/**
	 * Render not found message
	 *
	 * @since  2.2.39
	 * @return void
	 */
	public function render_not_found() {
		$this->parent->add_render_attribute('not-found', 'class', 'ee-posts__nothing-found');

		?><div <?php echo $this->parent->get_render_attribute_string('not-found'); ?>><?php
			echo $this->parent->get_settings_for_display( 'nothing_found_message' );
		?></div><?php
	}

	/**
	 * Custom Excerpt Length
	 * 
	 * Applies the custom excerpt length
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function custom_excerpt_length() {
		return $this->parent->get_settings( 'post_excerpt_length' );
	}

	/**
	 * Custom Excerpt More Filter
	 *
	 * Filter for setting the custom more suffix
	 *
	 * @since 2.1.0
	 */
	public function custom_excerpt_more_filter( $more ) {
		return $this->parent->get_settings( 'post_excerpt_more' );
	}

	/**
	 * Custom Excerpt More
	 * 
	 * Returns the post excerpt more suffix text
	 *
	 * @since 2.1.0
	 */
	public function custom_excerpt_more() {
		return $this->parent->get_settings( 'post_excerpt_more' );
	}

	/**
	 * Render Loop Start
	 * 
	 * Function to render markup before the posts loop starts
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_loop_start() {

		$this->parent->add_render_attribute( [
			'metas-separator' => [
				'class' => 'ee-post__meta__separator',
			],
			'terms-separator' => [
				'class' => [
					'ee-post__terms__separator',
				],
			],
			'loop' => [
				'class' => [
					'ee-grid',
					'ee-loop',
				],
			],
		] );

		if ( '' !== $this->parent->get_settings( 'layout' ) ) {
			$this->parent->add_render_attribute( 'loop', 'class', 'ee-grid--' . $this->parent->get_settings( 'classic_layout' ) );
		}

		?><div <?php echo $this->parent->get_render_attribute_string( 'loop' ); ?>><?php
	}

	/**
	 * Render Sizer
	 * 
	 * Render markup for masonry sizer
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_sizer() {
		$settings = $this->parent->get_settings();

		$this->parent->add_render_attribute( 'sizer', 'class', [
			'ee-grid__item--sizer',
		] );

		?><div <?php echo $this->parent->get_render_attribute_string( 'sizer' ); ?>></div><?php
	}

	/**
	 * Render Post
	 * 
	 * Output post content
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_post() {

		$settings = $this->parent->get_settings();

		if ( ! in_array( $settings[ 'post_media_position' ], array( 'left', 'right' ) ) ) {
			$this->render_vertical_post();		
		} else if ( 'yes' === $settings['post_media'] ) {
			$this->render_horizontal_post();
		} else {
			$this->render_vertical_post();
		}
	}

	/**
	 * Render Vertical Post
	 * 
	 * Output required markup for posts in vertical layout
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_vertical_post() {
		$this->render_post_header();
		$this->render_post_media();
		$this->render_post_body();
		$this->render_post_footer();
	}

	/**
	 * Render Horizontal Post
	 * 
	 * Output required markup for posts in horizontal layout
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_horizontal_post() {
		$this->render_post_media();

		$post_content_key = 'post-content-' . get_the_ID();

		$this->parent->add_render_attribute( [
			$post_content_key => [
				'class' => 'ee-post__content',
			],
		] );

		?><div <?php echo $this->parent->get_render_attribute_string( $post_content_key ); ?>><?php
			$this->render_post_header();
			$this->render_post_body();
			$this->render_post_footer();
		?></div><?php
	}

	/**
	 * Render Post Start
	 * 
	 * HTML tags and content before the post content starts
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_post_start() {
		global $post;

		$grid_item_key = 'grid-item-' . get_the_ID();

		$this->parent->add_render_attribute( $grid_item_key, 'class', [
			'ee-grid__item',
			'ee-loop__item',
		] );

		/**
		 * Item Classes Filter
		 *
		 * Filter for additional item classes
		 *
		 * @since 2.2.28
		 * @param string 			$item_classes 	The array of classes
		 * @param object|WP_Post 	$post 			The current post
		 * @param object|WP_Post 	$settings 		The widget settings
		 */
		$item_classes = apply_filters( 'elementor_extras/widgets/posts/item_classes', [], $post, $this->parent->get_settings() );

		$this->parent->add_render_attribute( $grid_item_key, 'class', $item_classes );

		$this->before_grid_item();

		?><div <?php echo $this->parent->get_render_attribute_string( $grid_item_key ); ?>><?php
			$this->after_grid_item_start();
			?><article <?php post_class( $this->parent->get_post_classes() ); ?>><?php
	}

	/**
	 * Render Post Header
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_post_header() {

		global $post;

		$settings = $this->parent->get_settings_for_display();

		/**
		 * Before Post Header
		 *
		 * Fires right before the output of post header area.
		 *
		 * @since 2.2.0
		 * @param array 	$settings 	The current widget settings
		 * @param int 		$post_id 	The post ID
		 */
		do_action( 'elementor_extras/widgets/posts/before_post_header', $settings, $post->ID );

		$area = 'header';

		if ( ! $this->parent->is_empty_area( $area ) ) {
			$post_header_key 	= 'post-header-' . get_the_ID();

			$this->parent->add_render_attribute( $post_header_key, 'class', [
				'ee-post__header',
				'ee-post__area',
			] );

			$this->parent->add_helper_render_attribute( $post_header_key, 'Header' );

			?><div <?php echo $this->parent->get_render_attribute_string( 'post-header-' . get_the_ID() ); ?>><?php
				$this->render_post_parts( $area );
			?></div><!-- .ee-post__header --><?php
		}

		/**
		 * After Post Header
		 *
		 * Fires right after the output of post header area.
		 *
		 * @since 2.2.0
		 * @param array 	$settings 	The current widget settings
		 * @param int 		$post_id 	The post ID
		 */
		do_action( 'elementor_extras/widgets/posts/after_post_header', $settings, $post->ID );

	}

	/**
	 * Render Post Media
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_post_media() {

		global $post;

		$settings = $this->parent->get_settings_for_display();

		/**
		 * Before Post Media
		 *
		 * Fires right before the output of post media area.
		 *
		 * @since 2.2.0
		 * @param array 	$settings 	The current widget settings
		 * @param int 		$post_id 	The post ID
		 */
		do_action( 'elementor_extras/widgets/posts/before_post_media', $settings, $post->ID );

		$area 				= 'media';
		$media_tag 			= 'div';
		$media_key 			= 'post-media-' . $post->ID;
		$media_content_key 	= 'post-media-content-' . $post->ID;

		// Option to not show media
		if ( 'yes' !== $settings['post_media'] )
			return;

		// No thumbnail and no placeholder -> hide
		if ( ! has_post_thumbnail() && empty( $settings['image']['url'] ) )
			return;

		$this->parent->add_render_attribute( $media_key, 'class', [
			'ee-media',
			'ee-post__media',
		] );

		$this->parent->add_helper_render_attribute( $media_key, 'Media' );

		if ( '' !== $settings['post_media_link'] ) {
			$media_tag = 'a';

			$this->parent->add_render_attribute( [
				$media_key => [
					/**
					 * Media Link Filter
					 *
					 * Filters the post media link. This is available only if the whole media area
					 * is linked when the Content > Media > Link to Post option is enabled.
					 *
					 * @since 2.2.2
					 * @param array $media_link 	The original post link
					 * @param object|WP_Post $post  The current post
					 */
					'href' => apply_filters( 'elementor_extras/widgets/posts/media/link', $this->get_post_link( $post ), $post ),
				],
			] );

			if ( '' !== $settings['post_media_blank'] ) {
				$this->parent->add_render_attribute( $media_key, 'target', '_blank' );
			}
		}

		if ( ! $this->parent->is_empty_area( $area ) ) {
			$this->parent->add_render_attribute( [
				$media_key => [
					'class' => [
						'ee-post__media--content'
					],
				],
				$media_content_key => [
					'class' => [
						'ee-media__content',
						'ee-post__media__content',
						'ee-post__area',
					],
				],
			] );
		}

		?><<?php echo $media_tag; ?> <?php echo $this->parent->get_render_attribute_string( $media_key ); ?>><?php
			$this->render_post_media_thumbnail();
			$this->render_post_media_overlay();

		if ( ! $this->parent->is_empty_area( $area ) ) {

			?><div <?php echo $this->parent->get_render_attribute_string( $media_content_key ); ?>><?php
				$this->render_post_parts( $area );
			?></div><!-- .ee-post__media__content --><?php
		}

		?></<?php echo $media_tag; ?>><!-- .ee-post__media --><?php

		/**
		 * After Post Media
		 *
		 * Fires right after the output of post media area.
		 *
		 * @since 2.2.0
		 * @param array 	$settings 	The current widget settings
		 * @param int 		$post_id 	The post ID
		 */
		do_action( 'elementor_extras/widgets/posts/after_post_media', $settings, $post->ID );
	}

	/**
	 * Render Post Body
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_post_body() {

		global $post;

		$settings = $this->parent->get_settings_for_display();

		/**
		 * Before Post Body
		 *
		 * Fires right before the output of post body area.
		 *
		 * @since 2.2.0
		 * @param array 	$settings 	The current widget settings
		 * @param int 		$post_id 	The post ID
		 */
		do_action( 'elementor_extras/widgets/posts/before_post_body', $settings, $post->ID );

		$area = 'body';

		if ( $this->parent->is_empty_area( $area ) )
			return;

		$body_key = 'post-body-' . get_the_ID();

		$this->parent->add_render_attribute( $body_key, 'class', [
			'ee-post__body',
			'ee-post__area',
		] );

		$this->parent->add_helper_render_attribute( $body_key, 'Body' );

		?><div <?php echo $this->parent->get_render_attribute_string( $body_key ); ?>><?php
			$this->render_post_parts( $area );
		?></div><!-- .ee-post__body --><?php

		/**
		 * After Post Body
		 *
		 * Fires right after the output of post body area.
		 *
		 * @since 2.2.0
		 * @param array 	$settings 	The current widget settings
		 * @param int 		$post_id 	The post ID
		 */
		do_action( 'elementor_extras/widgets/posts/after_post_body', $settings, $post->ID );
	}

	/**
	 * Render Post Footer
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_post_footer() {

		global $post;

		$settings = $this->parent->get_settings_for_display();

		/**
		 * Before Post Footer
		 *
		 * Fires right before the output of post footer area.
		 *
		 * @param array 	$settings 	The current widget settings
		 * @param int 		$post_id 	The post ID
		 */
		do_action( "elementor_extras/widgets/posts/before_post_footer", $settings, $post->ID );

		$area = 'footer';
		$footer_key = 'post-footer-' . get_the_ID();

		if ( $this->parent->is_empty_area( $area ) )
			return;

		$this->parent->add_render_attribute( $footer_key, 'class', [
			'ee-post__footer',
			'ee-post__area',
		] );

		$this->parent->add_helper_render_attribute( $footer_key, 'Footer' );

		?><div <?php echo $this->parent->get_render_attribute_string( $footer_key ); ?>><?php
			$this->render_post_parts( $area );
		?></div><!-- .ee-post__footer --><?php

		/**
		 * After Post Footer
		 *
		 * Fires right after the output of post footer area.
		 *
		 * @since 2.2.0
		 * @param array 	$settings 	The current widget settings
		 * @param int 		$post_id 	The post ID
		 */
		do_action( 'elementor_extras/widgets/posts/after_post_footer', $settings, $post->ID );
	}

	/**
	 * Render Post Parts
	 *
	 * Calls method for given post part
	 *
	 * @since  1.6.0
	 * @param  area   The area to render the post parts in
	 * @return void
	 */
	protected function render_post_parts( $area ) {

		global $post;

		$_ordered_parts = $this->parent->get_ordered_post_parts( PostsModule::get_post_parts() );

		/**
		 * After Area Start
		 *
		 * Fires right after the output of a post area container starting html tag.
		 * The $area part refers to the area of the post (header, media, body, footer)
		 *
		 * @since 2.2.0
		 * @param array 	$settings 	The current widget settings
		 * @param int 		$post_id 	The post ID
		 */
		do_action( "elementor_extras/widgets/posts/after_{$area}_start", $this->parent->get_settings(), $post->ID );

		foreach ( $_ordered_parts as $part => $index ) {

			/**
			 * Before Post Part
			 *
			 * Fires right before the output of a post content part belonging to a post area.
			 * The $area part refers to the area of the post (header, media, body, footer) and 
		 	 * $part refers to one of the post content parts ( terms, title, excerpt, button, metas ).
			 *
			 * @since 2.2.0
			 * @param array 	$settings 	The current widget settings
		 	 * @param int 		$post_id 	The post ID
			 */
			do_action( "elementor_extras/widgets/posts/before_{$area}_{$part}", $this->parent->get_settings(), $post->ID );

			call_user_func( array( $this, 'render_post_' . $part ), $area );

			/**
			 * After Post Part
			 *
			 * Fires right after the output of a post content part belonging to a post area.
			 * The $area part refers to the area of the post (header, media, body, footer) and 
		 	 * $part refers to one of the post content parts ( terms, title, excerpt, button, metas ).
			 *
			 * @since 2.2.0
			 * @param array 	$settings 	The current widget settings
		 	 * @param int 		$post_id 	The post ID
			 */
			do_action( "elementor_extras/widgets/posts/after_{$area}_{$part}", $this->parent->get_settings(), $post->ID );
		}

		/**
		 * Before Area End
		 *
		 * Fires right before the output of a post area container ending html tag.
		 * The $area part refers to the area of the post (header, media, body, footer)
		 *
		 * @since 2.2.0
		 * @param array 	$settings 	The current widget settings
		 * @param int 		$post_id 	The post ID
		 */
		do_action( "elementor_extras/widgets/posts/before_{$area}_end", $this->parent->get_settings(), $post->ID );
	}

	/**
	 * Render Post Metas
	 *
	 * @since  1.6.0
	 * @param  area   The post area
	 * @return void
	 */
	protected function render_post_metas( $area ) {

		global $post;

		// Render any metas in an area
		if ( $this->parent->metas_in_area( $area ) || $this->parent->is_in_area( 'avatar', $area ) ) {

			$metas_area_key = 'post-metas-' . $area . '-' . get_the_ID();
			$metas_list_key = 'post-metas-list-' . $area . '-' . get_the_ID();

			$this->parent->add_render_attribute( [
				 $metas_area_key => [
					'class' => 'ee-post__metas',
				],
			] );

			$this->parent->add_helper_render_attribute( $metas_area_key, 'Metas' );

			$this->parent->add_render_attribute( $metas_list_key, 'class', 'ee-post__metas__list' );

			if ( '' !== $this->parent->get_settings( 'metas_display' ) ) {
				$this->parent->add_render_attribute( $metas_list_key, 'class', 'display--' . $this->parent->get_settings( 'metas_display' ) );
			}

			if ( $this->parent->is_in_area( 'avatar', $area ) ) {
				$this->parent->add_render_attribute( $metas_area_key, 'class', 'ee-post__metas--has-avatar' );
			}

			if ( $this->parent->metas_in_area( $area ) ) {
				$this->parent->add_render_attribute( $metas_area_key, 'class', 'ee-post__metas--has-metas' );
			}

			?><div <?php echo $this->parent->get_render_attribute_string( $metas_area_key ); ?>><?php

				$this->render_post_avatar( $area );

				if ( $this->parent->metas_in_area( $area ) ) {

					?><ul <?php echo $this->parent->get_render_attribute_string( $metas_list_key ); ?>><?php

						$_ordered_parts = $this->parent->get_ordered_post_parts( PostsModule::get_meta_parts() );

						/**
						 * Before Metas
						 *
						 * Fires before the output of the first meta in the area.
						 * Any outputted content should be wrapped in <li></li> tags.
						 * The $area part refers to the area of the post ( header, media, body or footer ).
						 *
						 * @since 2.2.0
						 * @param array 	$settings 	The current widget settings
		 				 * @param int 		$post_id 	The post ID
						 */
						do_action( "elementor_extras/widgets/posts/before_{$area}_metas", $this->parent->get_settings(), $post->ID );

						foreach ( $_ordered_parts as $meta => $index ) {

							/**
							 * Before Meta
							 *
							 * Fires right before the output of a post meta.
							 * The $meta part refers to the meta of the post ( author, date, comments or price ).
							 *
							 * @since 2.2.0
							 * @param array 	$settings 	The current widget settings
		 					 * @param int 		$post_id 	The post ID
							 * @param string 	$area 	The area the meta resides in
							 */
							do_action( "elementor_extras/widgets/posts/before_{$meta}", $this->parent->get_settings(), $post->ID, $area );

							call_user_func( array( $this, 'render_post_' . $meta ), $area );

							/**
							 * After Meta
							 *
							 * Fires right after the output of a post meta.
							 * The $meta part refers to the meta of the post ( author, date, comments or price ).
							 *
							 * @since 2.2.0
							 * @param array 	$settings 	The current widget settings
		 					 * @param int 		$post_id 	The post ID
							 * @param string 	$area 	The area the meta resides in
							 */
							do_action( "elementor_extras/widgets/posts/after_{$meta}", $this->parent->get_settings(), $post->ID, $area );
						}

						/**
						 * After Metas
						 *
						 * Fires right after the output of the last meta in the area.
						 * Any outputted content should be wrapped in <li></li> tags.
						 * The $area part refers to the area of the post ( header, media, body or footer ).
						 *
						 * @since 2.2.0
						 * @param array 	$settings 	The current widget settings
		 				 * @param int 		$post_id 	The post ID
						 */
						do_action( "elementor_extras/widgets/posts/after_{$area}_metas", $this->parent->get_settings(), $post->ID );

					?></ul><?php

				}

		?></div><?php
		}
	}

	/**
	 * Render Post Media Thumbnail
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_post_media_thumbnail() {

		global $post;

		$settings = $this->parent->get_settings_for_display();

		// Setup using placholder image field
		$post_thumbnail =  Group_Control_Image_Size::get_attachment_image_html( $settings );

		// Setup using post thumbnail
		if ( has_post_thumbnail() ) {
			$settings[ 'post_media_thumbnail_size' ] = [
				'id' => get_post_thumbnail_id(),
			];

			$post_thumbnail = Group_Control_Image_Size::get_attachment_image_html( $settings, 'post_media_thumbnail_size' );
		}

		if ( empty( $post_thumbnail ) ) {
			return;
		}

		$thumbnail_key = 'post-thumbnail' . get_the_ID();

		$this->parent->add_render_attribute( $thumbnail_key, 'class', [
			'ee-post__media__thumbnail',
			'ee-media__thumbnail',
		] );

		/**
		 * Post Thumbnail Filter
		 *
		 * Filters the post thumbnail
		 *
		 * @since 2.1.3
		 * @param string 			$post_thumbnail 	The html for the thumbnail of the post
		 * @param object|WP_Post 	$post 				The current post
		 */
		$post_thumbnail = apply_filters( 'elementor_extras/widgets/posts/thumbnail', $post_thumbnail, $post );
		
		?><div <?php echo $this->parent->get_render_attribute_string( $thumbnail_key ); ?>><?php
			echo $post_thumbnail;
		?></div><?php
	}

	/**
	 * Render Post Media Overlay
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_post_media_overlay() {
		$overlay_key = 'post-overlay' . get_the_ID();

		$this->parent->add_render_attribute( $overlay_key, 'class', [
			'ee-post__media__overlay',
			'ee-media__overlay',
		] );

		?><div <?php echo $this->parent->get_render_attribute_string( $overlay_key ); ?>></div><?php
	}

	/**
	 * Render Post Terms
	 *
	 * @since  1.6.0
	 * @param area   The area in which the terms are displayed. Defaults to 'header'
	 * @return void
	 */
	protected function render_post_terms( $area = 'header' ) {
		if ( ! $this->parent->is_in_area( 'terms', $area ) )
			return;

		$settings 	= $this->parent->get_settings();
		$terms 		= $this->parent->get_terms();
		$terms_key 	= 'post-terms-' . get_the_ID();
		$term_prefix_key = 'term-prefix-' . get_the_ID();
		$term_count = $settings['post_terms_count'];

		if ( ! $terms || $term_count === 0 )
			return;

		$count 			= 0;
		$terms_tag 		= 'span';
		$terms_linked 	= 'yes' === $this->parent->get_settings( 'post_terms_link' );
		$media_linked 	= 'yes' === $this->parent->get_settings( 'post_media_link' );
		$in_media 		= $this->parent->is_in_area( 'terms', 'media' );

		$this->parent->add_render_attribute( [
			$terms_key => [
				'class' => [
					'ee-post__terms',
				],
			],
			$term_prefix_key => [
				'class' => [
					'ee-post__terms__term',
					'ee-post__terms__term--prefix',
				],
			],
		] );

		$this->parent->add_helper_render_attribute( $terms_key, 'Terms' );

		?>
		<ul <?php echo $this->parent->get_render_attribute_string( $terms_key ); ?>>

			<?php if ( $settings['post_terms_prefix'] ) { ?>
			<li <?php echo $this->parent->get_render_attribute_string( $term_prefix_key ); ?>>
				<?php echo $settings['post_terms_prefix']; ?>
			</li>
			<?php } ?>

			<?php foreach( $terms as $term ) {
				if ( '' !== $term_count && $term_count === $count ) break;

				$term_render_key = 'term-item-' . get_the_ID() . ' ' . $term->term_id;
				$term_link_render_key = 'term-link-' . get_the_ID() . ' ' . $term->term_id;

				$this->parent->add_render_attribute( [
					$term_render_key => [
						'class' => [
							'ee-post__terms__term',
							'ee-term',
							'ee-term--' . $term->slug,
						],
					],
					$term_link_render_key => [
						'class' => [
							'ee-post__terms__link',
							'ee-term__link',
						],
					],
				] );

				if ( ( $in_media && ! $media_linked && $terms_linked ) || ( ! $in_media && $terms_linked ) ) {
					$terms_tag = 'a';
					$this->parent->add_render_attribute( $term_link_render_key, 'href', get_term_link( $term ) );
				}
			?>

				<li <?php echo $this->parent->get_render_attribute_string( $term_render_key ); ?>>
					<<?php echo $terms_tag; ?> <?php echo $this->parent->get_render_attribute_string( $term_link_render_key ); ?>>
						<?php echo $term->name; ?>
					</<?php echo $terms_tag; ?>><?php echo $this->render_terms_separator(); ?>
				</li>

			<?php $count++; } ?>
		</ul>
		<?php
	}

	/**
	 * Render Post Title
	 *
	 * @since  1.6.0
	 * @param  area   The area to render the post title in
	 * @return void
	 */
	protected function render_post_title( $area = 'body' ) {
		if ( ! $this->parent->is_in_area( 'title', $area ) )
			return;

		global $post;

		$title_tag 		= 'div';
		$heading_tag 	= $this->parent->get_settings( 'post_title_element' );

		$title_key 		= 'post-title-' . get_the_ID();
		$heading_key 	= 'post-title-heading-' . get_the_ID();

		$in_media 		= $this->parent->is_in_area( 'title', 'media' );
		
		$title_linked 	= 'yes' === $this->parent->get_settings( 'post_title_link' );
		$media_linked 	= 'yes' === $this->parent->get_settings( 'post_media_link' );

		$post_title 	= get_the_title();

		if ( ( $in_media && ! $media_linked && $title_linked ) || ( ! $in_media && $title_linked ) ) {
			$title_tag = 'a';
			$this->parent->add_render_attribute( [
				$title_key => [
					/**
					 * Title Link Filter
					 *
					 * Filters the link for the title
					 *
					 * @since 2.2.6
					 * @param array $title_link 	The original post link
					 * @param object|WP_Post $post  The current post
					 */
					'href' 	=> apply_filters( 'elementor_extras/widgets/posts/title/link', $this->get_post_link( $post ), $post )
				]
			] );

			if ( '' !== $this->parent->get_settings( 'post_title_link_blank') ) {
				$this->parent->add_render_attribute( $title_key, 'target', '_blank' );
			}
		}

		$this->parent->add_render_attribute( [
			$title_key => [
				'class' => 'ee-post__title',
			],
			$heading_key => [
				'class' => 'ee-post__title__heading',
			],
		] );

		$this->parent->add_helper_render_attribute( $title_key, 'Title' );

		/**
		 * Post Title Filter
		 *
		 * Filters the current post title
		 *
		 * @since 2.1.3
		 * @param string 			$post_title 	The original post date time
		 * @param object|WP_Post 	$post 			The current post
		 */
		$post_title = apply_filters_deprecated( 'ee_posts_title', [ $post_title, $post ], '2.1.3', 'elementor_extras/widgets/posts/post_title' );
		$post_title = apply_filters( 'elementor_extras/widgets/posts/title', $post_title, $post );

		?><<?php echo $title_tag; ?> <?php echo $this->parent->get_render_attribute_string( $title_key ); ?>>
			<<?php echo $heading_tag; ?> <?php echo $this->parent->get_render_attribute_string( $heading_key ); ?>><?php
				echo $post_title;
			?></<?php echo $heading_tag; ?>>
		</<?php echo $title_tag; ?>><?php
	}

	/**
	 * Render Metas Separator
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_metas_separator() {
		if ( '' === $this->parent->get_settings( 'post_metas_separator' ) )
			return;

		$separator = $this->parent->get_settings( 'post_metas_separator' );

		?><span <?php echo $this->parent->get_render_attribute_string( 'metas-separator' ); ?>><?php echo $separator; ?></span><?php
	}

	/**
	 * Render Terms Separator
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_terms_separator() {
		if ( '' === $this->parent->get_settings( 'post_terms_separator' ) )
			return;

		$separator = $this->parent->get_settings( 'post_terms_separator' );

		?><span <?php echo $this->parent->get_render_attribute_string( 'terms-separator' ); ?>><?php echo $separator; ?></span><?php
	}

	/**
	 * Render Post Author
	 *
	 * @since  1.6.0
	 * @param  area  The area that the post author is displayed in
	 * @return void
	 */
	protected function render_post_author( $area = 'footer' ) {
		if ( ! $this->parent->is_in_area( 'author', $area ) )
			return;

		$has_link = ! $this->parent->is_in_area( 'author', 'media' ) && 'yes' === $this->parent->get_settings( 'post_author_link' );
		$meta_author_key = 'meta-author-' . get_the_ID();
		$meta_author_link_key = 'meta-author-link-' . get_the_ID();

		$this->parent->add_render_attribute( [
			$meta_author_key => [
				'class' => [
					'ee-post__meta',
					'ee-post__meta--author',
				],
			],
			$meta_author_link_key => [
				'href' => get_author_posts_url( get_the_author_meta( 'ID' ) ),
			],
		] );

		?><li <?php echo $this->parent->get_render_attribute_string( $meta_author_key ); ?>>
			<?php if ( $has_link ) : ?>
				<a <?php echo $this->parent->get_render_attribute_string( $meta_author_link_key ); ?>>
			<?php endif; ?>
				<?php echo $this->parent->get_settings('post_author_prefix'); ?> <?php the_author(); ?><?php echo $this->render_metas_separator(); ?>
			<?php if ( $has_link ) : ?></a><?php endif; ?>
		</li>
		<?php
	}

	/**
	 * Render Post Avatar
	 *
	 * @since  1.6.0
	 * @param  area  The area that the post avatar is displayed in
	 * @return void
	 */
	protected function render_post_avatar( $area = 'footer' ) {
		if ( ! $this->parent->is_in_area( 'avatar', $area ) )
			return;

		$has_link = ! $this->parent->is_in_area( 'avatar', 'media' ) && 'yes' === $this->parent->get_settings( 'post_avatar_link' );
		$meta_avatar_key = 'meta-avatar-' . get_the_ID();
		$meta_avatar_link_key = 'meta-avatar-link-' . get_the_ID();
		$args = [
			'class' => 'ee-post__metas__avatar__image',
			'size'	=> 64,
		];

		$this->parent->add_render_attribute( [
			$meta_avatar_key => [
				'class' => [
					'ee-post__metas__avatar',
					'ee-post__meta--avatar'
				],
			],
			$meta_avatar_link_key => [
				'href' => get_author_posts_url( get_the_author_meta( 'ID' ) ),
			],
		] );

		?><div <?php echo $this->parent->get_render_attribute_string( $meta_avatar_key ); ?>>
			<?php if ( $has_link ) : ?>
				<a <?php echo $this->parent->get_render_attribute_string( $meta_avatar_link_key ); ?>>
			<?php endif; ?>
				<?php echo get_avatar( get_the_author_meta( 'ID' ), 64, '', get_the_author_meta( 'display_name' ), $args ); ?>
			<?php if ( $has_link ) : ?></a><?php endif; ?>
		</div><?php
	}

	/**
	 * Render Post Datre
	 *
	 * @since  1.6.0
	 * @param  area  The area that the post date is displayed in
	 * @return void
	 */
	protected function render_post_date( $area = 'footer' ) {
		global $post;

		if ( ! $this->parent->is_in_area( 'date', $area ) )
			return;

		$meta_date_key = 'post-date-' . get_the_ID();
		$settings = $this->parent->get_settings();
		$custom = 'custom' === $settings['post_date_format'];

		$this->parent->add_render_attribute( [
			$meta_date_key => [
				'class' => [
					'ee-post__meta',
					'ee-post__meta--date',
					'ee-post__metas__date',
				],
			],
		] );

		$post_date_time = $this->parent->get_date_formatted(
			$custom,
			$settings['post_date_custom_format'],
			$settings['post_date_format'],
			$settings['post_time_format'] );

		/**
		 * Date Time Filter
		 *
		 * Filters the date and time text
		 *
		 * @since 2.1.3
		 * @param string 		 $post_date_time 	The original post date time
		 * @param object|WP_Post $post  			The current post
		 */
		$post_date_time = apply_filters_deprecated( 'ee_posts_date_time', [ $post_date_time, $post ], '2.1.3', 'elementor_extras/widgets/posts/date_time' );
		$post_date_time = apply_filters( 'elementor_extras/widgets/posts/date_time', $post_date_time, $post );

		?><li <?php echo $this->parent->get_render_attribute_string( $meta_date_key ); ?>><?php
			echo $settings['post_date_prefix'] . ' ' . $post_date_time;
			echo $this->render_metas_separator();
		?></li><?php
	}

	/**
	 * Render Post Price
	 *
	 * @since  1.6.0
	 * @param  area  The area that the post price is displayed in
	 * @return void
	 */
	protected function render_post_price( $area = 'footer' ) {
		if ( ! is_woocommerce_active() || ! function_exists( 'wc_get_product' ) )
			return;

		if ( ! $this->parent->is_in_area( 'price', $area ) )
			return;

		global $product;
		$product = wc_get_product();

		if ( empty( $product ) )
			return;

		$meta_date_key = 'post-price-' . get_the_ID();

		$this->parent->add_render_attribute( [
			$meta_date_key => [
				'class' => [
					'ee-post__meta',
					'ee-post__meta--price',
					'ee-post__metas__price',
				],
			],
		] );

		?><li <?php echo $this->parent->get_render_attribute_string( $meta_date_key ); ?>>
			<?php wc_get_template( '/single-product/price.php' ); ?>
			<?php echo $this->render_metas_separator(); ?>
		</li><?php
	}

	/**
	 * Render Post Comments
	 *
	 * @since  1.6.0
	 * @param  area  The area that the post comments are displayed in
	 * @return void
	 */
	protected function render_post_comments( $area = 'body' ) {
		if ( ! $this->parent->is_in_area( 'comments', $area ) )
			return;

		global $post;

		$post_comments = get_comments_number();
		$post_comments_prefix = $this->parent->get_settings( 'post_comments_prefix' );
		$post_comments_suffix = $this->parent->get_settings( 'post_comments_suffix' );
		$comments_key = 'post-comments-' . get_the_ID();

		$this->parent->add_render_attribute( [
			$comments_key => [
				'class' => [
					'ee-post__meta',
					'ee-post__meta--comments',
				],
			],
		] );

		/**
		 * Comments Prefix Filter
		 *
		 * Filters the prefix of the post comments counter
		 *
		 * @since 2.1.3
		 * @param string 			$post_comments_prefix 	The initial post comments prefix
		 * @param object|WP_Post 	$post  					The current post
		 */
		$post_comments_prefix = apply_filters_deprecated( 'ee_posts_comments_prefix', [ $post_comments_prefix, $post ], '2.1.3', 'elementor_extras/widgets/posts/comments/prefix' );
		$post_comments_prefix = apply_filters( 'elementor_extras/widgets/posts/comments/prefix', $post_comments_prefix, $post );

		/**
		 * Comments Count Filter
		 *
		 * Filters the comments count for the current post
		 *
		 * @since 2.1.3
		 * @param string 			$post_comments 	The initial post comments count
		 * @param object|WP_Post 	$post  			The current post
		 */
		$post_comments = apply_filters_deprecated( 'ee_posts_comments', [ $post_comments, $post ], '2.1.3', 'elementor_extras/widgets/posts/comments' );
		$post_comments = apply_filters( 'elementor_extras/widgets/posts/comments', $post_comments, $post );

		/**
		 * Comments Suffix Filter
		 *
		 * Filters the prefix of the post comments counter
		 *
		 * @since 2.1.3
		 * @param string 			$post_comments_suffix 	The initial post comments suffix
		 * @param object|WP_Post 	$post  					The current post
		 */
		$post_comments_suffix = apply_filters_deprecated( 'ee_posts_comments_suffix', [ $post_comments_suffix, $post ], '2.1.3', 'elementor_extras/widgets/posts/comments/prefix' );
		$post_comments_suffix = apply_filters( 'elementor_extras/widgets/posts/comments/suffix', $post_comments_suffix, $post );

		?><li <?php echo $this->parent->get_render_attribute_string( $comments_key ); ?>><?php
			echo $post_comments_prefix;
			echo $post_comments;
			echo $post_comments_suffix;
			echo $this->render_metas_separator();
		?></li><?php
	}

	/**
	 * Render Post Excerpt
	 *
	 * @since  1.6.0
	 * @param  area  The area that the post excerpt is displayed in
	 * @return void
	 */
	protected function render_post_excerpt( $area = 'body' ) {

		if ( ! $this->parent->is_in_area( 'excerpt', $area ) || ! $this->custom_excerpt_length() )
			return;

		global $post;

		$post_excerpt_key = 'post-excerpt-' . get_the_ID();
		$post_excerpt = get_the_excerpt();
		$post_excerpt_tag = 'div';

		$this->parent->add_render_attribute( $post_excerpt_key, 'class', 'ee-post__excerpt' );
		$this->parent->add_helper_render_attribute( $post_excerpt_key, 'Excerpt' );

		if ( 'yes' === $this->parent->get_settings( 'post_excerpt_trim_custom' ) ) {
			$post_excerpt = wp_trim_words( $post_excerpt, $this->custom_excerpt_length(), $this->custom_excerpt_more() );
		}

		if ( 'media' === $area ) $tag = 'span';

		/**
		 * Post Excerpt Filter
		 *
		 * Filters the post excerpt after at a post level
		 *
		 * @since 2.1.3
		 * @param string 			$post_excerpt 	The original excerpt
		 * @param object|WP_Post 	$post  			The current post
		 */
		$post_excerpt = apply_filters_deprecated( 'ee_posts_excerpt', [ $post_excerpt, $post ], '2.1.3', 'elementor_extras/widgets/posts/excerpt' );
		$post_excerpt = apply_filters( 'elementor_extras/widgets/posts/excerpt', $post_excerpt, $post );

		?><<?php echo $post_excerpt_tag; ?> <?php echo $this->parent->get_render_attribute_string( $post_excerpt_key ); ?>><?php
			echo $post_excerpt;
		?></<?php echo $post_excerpt_tag; ?>><?php
	}

	/**
	 * Render Post Button
	 *
	 * @since  2.0.0
	 * @param  area  The area that the post button is displayed in
	 * @return void
	 */
	protected function render_post_button( $area = 'body' ) {

		if ( ! $this->parent->is_in_area( 'button', $area ) )
			return;

		if ( 'product' === $this->parent->get_settings('posts_post_type') && 'add_to_cart' == $this->parent->get_settings('post_button_type') ) {
			$this->render_add_to_cart_button( $area );
		} else {
			$this->render_button( $area );
		}
	}

	/**
	 * Render Button
	 *
	 * @since  2.1.2
	 * @param  area  The area that the post button is displayed in
	 * @return void
	 */
	protected function render_button( $area = 'body' ) {

		global $post;

		$button_tag 		= 'a';
		$settings 			= $this->parent->get_settings();

		$button_link 		= $this->get_post_link( $post );
		$post_read_more_key = $this->parent->_get_repeater_setting_key( 'post-read-more', 'parts', get_the_ID() );
		$post_button_key 	= $this->parent->_get_repeater_setting_key( 'post-button', 'parts', get_the_ID() );

		$this->parent->add_render_attribute( [
			$post_read_more_key => [
				'class' => 'ee-post__read-more',
			],
			$post_button_key => [
				'class' => 'ee-post__button',
			],
		] );

		if ( 'media' === $area && 'yes' === $settings['post_media_link'] ) {
			$button_tag = 'div';
		} else {
			$this->parent->add_render_attribute( $post_button_key, [
				/**
				 * Button Link Filter
				 *
				 * Filters the link for the button
				 *
				 * @since 2.1.5
				 * @param array $button_link 	The original post link
				 * @param object|WP_Post $post  The current post
				 */
				'href' 	=> apply_filters( 'elementor_extras/widgets/posts/button/link', $button_link, $post ),
			] );

			if ( '' !== $settings['post_button_blank'] ) {
				$this->parent->add_render_attribute( $post_button_key, 'target', '_blank' );
			}
		}
		
		$this->parent->add_helper_render_attribute( $post_read_more_key, 'Button' );

		/**
		 * Button Text Filter
		 *
		 * Filters the text for the button
		 *
		 * @since 2.1.3
		 * @param string 		 $button_text 	The original text
		 * @param object|WP_Post $post  		The current post
		 */
		$button_text = apply_filters( 'elementor_extras/widgets/posts/button/text', $settings['post_read_more_text'], $post );

		?><div <?php echo $this->parent->get_render_attribute_string( $post_read_more_key ); ?>>
			<<?php echo $button_tag; ?> <?php echo $this->parent->get_render_attribute_string( $post_button_key ); ?>>
				<?php echo $button_text; ?>
			</<?php echo $button_tag; ?>>
		</div><?php
	}

	/**
	 * Render Add To Cart Button
	 *
	 * @since  2.1.2
	 * @param  area  The area that the post button is displayed in
	 * @return void
	 */
	protected function render_add_to_cart_button( $area = 'body' ) {
		if ( ! is_woocommerce_active() )
			return;

		if ( 'media' === $area && 'yes' === $this->parent->get_settings('post_media_link') )
			return;

		$add_to_cart_key = $this->parent->_get_repeater_setting_key( 'add_to_cart', 'parts', get_the_ID() );

		$this->parent->add_render_attribute( [
			$add_to_cart_key => [
				'class' => [
					'ee-post__read-more',
					'ee-post__add-to-cart',
				],
			],
		] );

		add_filter( 'woocommerce_loop_add_to_cart_args', [ $this, 'filter_woocommerce_add_to_cart_button_args' ], 10, 2 ); 
		add_filter( 'woocommerce_product_add_to_cart_text', [ $this, 'filter_woocommerce_add_to_cart_button_text' ], 10, 2 ); 
		
		?><div <?php echo $this->parent->get_render_attribute_string( $add_to_cart_key ); ?>><?php
			woocommerce_template_loop_add_to_cart();
		?></div><?php

		remove_filter( 'woocommerce_loop_add_to_cart_args', [ $this, 'filter_woocommerce_add_to_cart_button_args' ], 10, 2 );
		remove_filter( 'woocommerce_product_add_to_cart_text', [ $this, 'filter_woocommerce_add_to_cart_button_text' ], 10, 2 ); 
	}

	/**
	 * Add To Cart Button Filter
	 *
	 * @since  2.1.2
	 * @param  area  The area that the post button is displayed in
	 * @return void
	 */
	public function filter_woocommerce_add_to_cart_button_args( $defaults, $product ) {

		$args = [
			'class' => $defaults['class'] . ' ee-post__button ee-post__button--add-to-cart',
		];

		return wp_parse_args( $args, $defaults ); 
	}

	/**
	 * Add To Cart Button Text Filter
	 *
	 * @since  2.1.2
	 * @param  area  The area that the post button is displayed in
	 * @return void
	 */
	public function filter_woocommerce_add_to_cart_button_text( $add_to_cart_text, $instance ) {
		global $product;

		$setting_text = $this->parent->get_settings( 'post_button_add_to_cart_text' );

		if ( '' !== $setting_text ) {
			$add_to_cart_text = $setting_text;
		}

		/**
		 * Add To Cart Filter
		 *
		 * Filters the button text for add to cart button
		 *
		 * @since 2.1.3
		 * @param string 			$add_to_cart_text 	The original text
		 * @param object|WP_Product $product  			The current woocommerce product
		 */
		$add_to_cart_text = apply_filters( 'elementor_extras/widgets/posts/button/text', $add_to_cart_text, $product );
		$add_to_cart_text = apply_filters( 'elementor_extras/widgets/posts/add_to_cart/text', $add_to_cart_text, $product );
		
		return $add_to_cart_text;
	}

	/**
	 * Render Loop End
	 *
	 * Outputs the markup for the end of the loop
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_loop_end() {
		?></div><!-- .ee-loop --><?php
	}

	/**
	 * Render Post End
	 *
	 * Outputs the markup for the end of the post
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_post_end() {
			?></article><!-- .ee-post --><?php
			$this->before_grid_item_end();
		?></div><!-- .ee-loop__item --><?php

		$this->after_grid_item();
	}

	/**
	 * Before Loop
	 *
	 * Executes before the loop is started
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function before_loop() {
		global $post;

		/**
		 * Before Loop
		 *
		 * Fires right before the loop starts
		 *
		 * @since 2.2.0
		 * @param array 	$settings 	The current widget settings
		 * @param int 		$post_id 	The post ID
		 */
		do_action( 'elementor_extras/widgets/posts/before_loop', $this->parent->get_settings(), $post->ID );
	}

	/**
	 * Before Grid Item
	 *
	 * Executes before the grid item is outputted
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function before_grid_item() {
		global $post;

		/**
		 * Before Grid Item
		 *
		 * Fires right before the output of the grid item container
		 *
		 * @since 2.2.0
		 * @param array 	$settings 	The current widget settings
		 * @param int 		$post_id 	The post ID
		 */
		do_action( 'elementor_extras/widgets/posts/before_grid_item', $this->parent->get_settings(), $post->ID );
	}

	/**
	 * After Grid Item Start
	 *
	 * Executes after the grid item opening tag is outputted
	 *
	 * @since  2.2.0
	 * @return void
	 */
	public function after_grid_item_start() {
		global $post;

		$skin = $this->parent->get_settings( '_skin' );
		
		/**
		 * After Grid Item Start
		 *
		 * Fires right after the output of the grid item container starting html tag.
		 *
		 * @since 2.2.0
		 * @param array 	$settings 	The current widget settings
		 * @param int 		$post_id 	The post ID
		 */
		do_action( 'elementor_extras/widgets/posts/after_grid_item_start', $this->parent->get_settings(), $post->ID );
	}

	/**
	 * Before Grid Item End
	 *
	 * Executes before the grid item closing tag is outputted
	 *
	 * @since  2.2.0
	 * @return void
	 */
	public function before_grid_item_end() {
		global $post;

		$skin = $this->parent->get_settings( '_skin' );
		/**
		 * After Grid Item Start
		 *
		 * Fires right after the output of the grid item container ending html tag.
		 *
		 * @since 2.2.0
		 * @param array 	$settings 	The current widget settings
		 * @param int 		$post_id 	The post ID
		 */
		do_action( 'elementor_extras/widgets/posts/before_grid_item_end', $this->parent->get_settings(), $post->ID );
	}

	/**
	 * After Grid Item
	 *
	 * Executes after the grid item is outputted
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function after_grid_item() {
		global $post;

		/**
		 * After Grid Item
		 *
		 * Fires right after the output of the grid item container
		 *
		 * @since 2.2.0
		 * @param array 	$settings 	The current widget settings
		 * @param int 		$post_id 	The post ID
		 */
		do_action( 'elementor_extras/widgets/posts/after_grid_item', $this->parent->get_settings(), $post->ID );
	}

	/**
	 * Before Loop
	 *
	 * Executes after the loop has ended
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function after_loop() {
		global $post;

		/**
		 * After Loop
		 *
		 * Fires right after the loop ends
		 *
		 * @since 2.2.0
		 * @param array 	$settings 	The current widget settings
		 * @param int 		$post_id 	The post ID
		 */
		do_action( 'elementor_extras/widgets/posts/after_loop', $this->parent->get_settings(), $post->ID );
	}

	/**
	 * Render Pagination
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function render_pagination() {}

	/**
	 * Render Load Status
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function render_load_status() {}

	/**
	 * Render Load Button
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function render_load_button() {}

	/**
	 * Render Scripts
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function render_scripts() {}

}