<?php
namespace ElementorExtras\Modules\Posts\Widgets;

// Extras for Elementor Classes
use ElementorExtras\Base\Extras_Widget;
use ElementorExtras\Modules\Posts\Module;

// Elementor Classes
use Elementor\Controls_Manager; 

// Elementor Pro Classes
use ElementorPro\Modules\QueryControl\Controls\Group_Control_Related;
use ElementorPro\Modules\QueryControl\Module as Module_Query;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Posts
 *
 * @since 1.6.0
 */
abstract class Posts_Base extends Extras_Widget {

	/**
	 * Query
	 *
	 * @since  2.2.0
	 * @var    \WP_Query
	 */
	protected $_query = null;

	/**
	 * Get Query
	 *
	 * @since  2.2.0
	 * @return object|\WP_Query
	 */
	public function get_query() {
		return $this->_query;
	}

	/**
	 * Register Query Content Controls
	 *
	 * @since  2.2.0
	 * @return void
	 */
	protected function register_query_content_controls( $condition = [] ) {
		
		$this->start_controls_section(
			'section_query',
			[
				'label' => __( 'Query', 'elementor-extras' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
				'condition' => $condition,
			]
		);

			$this->add_group_control(
				Group_Control_Related::get_type(),
				[
					'name' => 'posts',
					'presets' => [ 'full' ],
					'exclude' => [
						'posts_per_page', //use the one from Layout section
						'ignore_sticky_posts'
					],
				]
			);

		$this->end_controls_section();

		$this->start_injection( [
			'at' => 'after',
			'of' => 'posts_select_date',
		] );

			$this->update_control( 'posts_orderby', [
				'options' => [
					'post_date' 		=> __( 'Date', 'elementor-extras' ),
					'post_title' 		=> __( 'Title', 'elementor-extras' ),
					'menu_order' 		=> __( 'Menu Order', 'elementor-extras' ),
					'rand' 				=> __( 'Random', 'elementor-extras' ),
					'meta_value'		=> __( 'Meta Value (text)', 'elementor-extras' ),
					'meta_value_num'	=> __( 'Meta Value (number)', 'elementor-extras' )
				],
			] );

		$this->end_injection();

		$this->start_injection( [
			'at' => 'after',
			'of' => 'posts_orderby',
		] );

			$this->add_control( 'posts_orderby_meta_key',
				[
					'label' 		=> __( 'Meta Key', 'elementor-extras' ),
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'condition' => [
						'posts_orderby' => [ 'meta_value', 'meta_value_num' ],
					],
				]
			);

		$this->end_injection();

		$this->start_injection( [
			'at' => 'after',
			'of' => 'posts_order',
		] );

			$this->add_control(
				'sticky_posts',
				[
					'label' 		=> __( 'Sticky Posts', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default'		=> '',
					'separator'		=> 'before',
					'return_value' 	=> 'yes',
				]
			);

			$this->add_control(
				'sticky_posts_info',
				[
					'type' 				=> Controls_Manager::RAW_HTML,
					'raw' 				=> __( 'Preview of sticky posts option is only available on frontend.', 'elementor-extras' ),
					'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-info',
					'condition' 		=> [
						'sticky_posts!' => '',
						'sticky_only' => '',
					],
				]
			);

			$this->add_control(
				'sticky_only',
				[
					'label' 		=> __( 'Show Only Sticky Posts', 'elementor-extras' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default'		=> '',
					'condition' 	=> [
						'sticky_posts!' => '',
						'posts_post_type!' => 'by_id',
					],
					'return_value' 	=> 'yes',
				]
			);

		$this->end_injection();
	}

	/**
	 * Query Posts
	 *
	 * @since  2.2.0
	 * @return void
	 */
	public function query_posts() {
		$query_args = [
			'ignore_sticky_posts'	=> 1,
			'posts_per_page' 		=> $this->get_posts_per_page(),
			'paged' 				=> $this->get_current_page(),
		];

		if ( $this->get_settings( 'posts_orderby_meta_key' ) ) {
			$query_args['meta_key'] = $this->get_settings( 'posts_orderby_meta_key' );
		}

		if ( 'yes' === $this->get_settings('sticky_posts') ) {
			$sticky_posts = get_option('sticky_posts');

			$post__in = ! empty( $query_args['post__in'] ) ? $query_args['post__in'] : [];

			if ( 'yes' === $this->get_settings('sticky_only') ) {
				if ( empty( $sticky_posts ) ) {
					$sticky_posts = [0];
				}

				$query_args['post__in'] = array_merge( $post__in, $sticky_posts );
			} else {
				$query_args['ignore_sticky_posts'] = 0;
			}
		}

		$this->set_query( $query_args );
	}

	/**
	 * Retrieve posts per page setting
	 *
	 * @since 	2.2.16
	 * @return 	int
	 */
	public function get_posts_per_page() {
		$posts_per_page = $this->get_settings('posts_per_page');

		if ( 'current_query' === $this->get_settings('posts_post_type') || ! $posts_per_page ) {
			$posts_per_page = (int)get_option( 'posts_per_page' );
		} else if ( 0 >= $posts_per_page ) {
			$posts_per_page = -1;
		}

		return $posts_per_page;
	}

	/**
	 * Checks for the Query ID and inits the WP_Query object
	 *
	 * @since 	2.2.0
	 * @param 	Array $query_args
	 * @return 	void
	 */
	public function set_query( $query_args ) {

		if ( ! is_elementor_pro_active() ) {
			return;
		}

		if ( '' === $query_args['posts_per_page'] ) {
			// Handle empty posts per page setting
			$query_args['posts_per_page'] = (int)get_option( 'posts_per_page' );
		}
		
		$elementor_query = Module_Query::instance();
		
		add_filter( 'elementor/query/get_query_args/current_query', [ $this, 'fix_default_query_args' ] );

		$this->_query = $elementor_query->get_query( $this, 'posts', $query_args, [] );

		/**
		 * Query Filter
		 *
		 * Filters the current query
		 *
		 * @since 2.1.3
		 * @param WP_Query 			$query 		The initial query
		 */
		$this->_query = apply_filters( 'elementor_extras/widgets/posts/query', $this->_query );

		remove_filter( 'elementor/query/get_query_args/current_query', [ $this, 'fix_default_query_args' ] );
	}

	/**
	 * Filter to override posts per page on current query setting
	 *
	 * @since 2.2.0
	 * @param Array $global_args
	 */
	public function fix_default_query_args( $global_args ) {

		// When using current_query some default categories are set with a new WP_Query
		// which restrict results in archive pages
		if ( 'current_query' === $this->get_settings( 'posts_post_type' ) ) {
			if ( ! is_category() ) {
				$global_args['cat'] = false;
				$global_args['category_name'] = '';
			}
		}

		return $global_args;
	}

	/**
	 * Get Formatted Date
	 *
	 * Format a date based on format settings
	 *
	 * @since 2.2.0
	 * @param string $custom 		Wether the format is custom or not
	 * @param string $date_format 	The date format
	 * @param string $time_format 	The time format
	 *
	 * @return string
	 */
	public function get_date_formatted( $custom = false, $custom_format, $date_format, $time_format, $post_id = null ) {
		if ( $custom ) {
			$format = $custom_format;
		} else {
			$date_format = $date_format;
			$time_format = $time_format;
			$format = '';

			if ( 'default' === $date_format ) {
				$date_format = get_option( 'date_format' );
			}

			if ( 'default' === $time_format ) {
				$time_format = get_option( 'time_format' );
			}

			if ( $date_format ) {
				$format = $date_format;
				$has_date = true;
			} else {
				$has_date = false;
			}

			if ( $time_format ) {
				if ( $has_date ) {
					$format .= ' ';
				}
				$format .= $time_format;
			}
		}

		$value = get_the_date( $format, $post_id );
		
		return wp_kses_post( $value );
	}

	/**
	 * Filter pagination link for page number
	 *
	 * @since  	2.2.38
	 * @return 	string
	 */
	public function filter_page_link( $link, $page ) {
		return add_query_arg( 'posts', $this->get_id(), $link );
	}

	/**
	 * Fetch wp link for page number
	 * Based on https://developer.wordpress.org/reference/functions/_wp_link_page/
	 *
	 * @since  	2.2.38
	 * @return 	string
	 */
	private function wp_link_page( $page ) {
		global $wp_rewrite;

		$post = get_post();
		$query_args = [];

		if ( $page == 1 ) {
			$url = get_permalink();
		} else {
			if ( '' === get_option( 'permalink_structure' ) || in_array( $post->post_status, [ 'draft', 'pending' ] ) ) {
				$url = add_query_arg( 'page', $page, get_permalink() );
			} elseif ( get_option( 'show_on_front' ) === 'page' && (int) get_option( 'page_on_front' ) === $post->ID ) {
				$url = trailingslashit( get_permalink() ) . user_trailingslashit( "$wp_rewrite->pagination_base/" . $page, 'single_paged' );
			} else {
				$url = trailingslashit( get_permalink() ) . user_trailingslashit( $page, 'single_paged' );
			}
		}

		if ( is_preview() ) {
			if ( ( 'draft' !== $post->post_status ) && isset( $_GET['preview_id'], $_GET['preview_nonce'] ) ) {
				$query_args['preview_id'] = wp_unslash( $_GET['preview_id'] );
				$query_args['preview_nonce'] = wp_unslash( $_GET['preview_nonce'] );
			}

			$url = get_preview_post_link( $post, $query_args, $url );
		}

		return $url;
	}

	/**
	 * Get Pagination Link
	 * 
	 * Fetch link for page number
	 * Based on https://developer.wordpress.org/reference/functions/_wp_link_page/
	 *
	 * @since  	2.2.38
	 * @return 	string
	 */
	private function get_pagination_link( $page ) {
		$link = Module::is_custom_pagination() ? $this->wp_link_page( $page ) : get_pagenum_link( $page );

		/**
		 * Pagination Link Filter
		 *
		 * Filters the pagination link of the specified page
		 *
		 * @since 2.2.38
		 * @param string 			$link 	The initial link
		 * @param object|WP_Post 	$page 	The page number
		 */
		return apply_filters( 'elementor_extras/widgets/posts/pagination_link', $link, $page );
	}

	/**
	 * Render pagination link by page number
	 *
	 * @since  2.2.38
	 * @return void
	 */
	public function render_pagination_link( $label, $direction = NULL, $limit = NULL ) {
		$page = $this->get_current_page();

		if ( is_null( $limit ) ) {
			$limit = $this->get_query()->max_num_pages;
		}

		if ( 'next' === $direction ) {
			$page = intval( $page ) + 1;

			if ( $page > $limit ) {
				return;
			}

		} else if ( 'previous' === $direction ) {
			$page = intval( $page ) - 1;

			if ( $page < 1 ) {
				return;
			}
		} else {
			if ( $page < 1 || $page > $limit ) {
				return;
			}

			$label = $page;
		}

		$nav_link_key = $this->_get_repeater_setting_key( 'pagination', $direction, $page );

		$this->add_render_attribute( $nav_link_key, [
			'class' => [
				'ee-pagination__' . $direction,
				'page-numbers',
			],
			'href' => $this->get_pagination_link( $page ),
		] );

		?><a <?php echo $this->get_render_attribute_string( $nav_link_key ); ?>><?php
			echo $label;
		?></a><?php
	}

	/**
	 * Get Pagination Query Var
	 *
	 * @since  2.2.39
	 * @return string
	 */
	protected function get_pagination_query_var() {

		if ( is_front_page() && ! is_home() ) {

			// Page et as homepage uses paged in query
			// string and page as query var
			return 'page';

		} else if ( Module::is_custom_pagination() ) {

			// Use our own query var
			return 'ee-page';
		}

		// Default
		return 'paged';
	}

	/**
	 * Get Current Page
	 *
	 * @since  1.6.0
	 * @return array
	 */
	public function get_current_page() {
		$pagination = '' !== $this->get_skin_setting( 'pagination' );
		$multiple 	= '' !== $this->get_skin_setting( 'pagination_multiple' );
		$infinite 	= '' !== $this->get_skin_setting( 'infinite_scroll' );
		$posts 		= isset( $_GET['posts'] ) ? $_GET['posts'] : false;
		$query_var 	= $this->get_pagination_query_var();

		if ( ! $infinite && ! $pagination ) {
			return 1;
		}

		$page = get_query_var( $query_var );

		if ( ! $page || $page < 2 ) {
			return 1;
		}

		if ( $posts && $this->get_id() !== $posts ) {
			return 1;
		} else {
			if ( $multiple && ! $posts ) {
				return 1;
			}
		}

		return $page;
	}

	/**
	 * get_repeater_setting_key wrapper
	 *
	 * @since 2.1.2
	 * @return string
	 */
	public function _get_repeater_setting_key( $setting_key, $repeater_key, $repeater_item_index ) {
		return $this->get_repeater_setting_key( $setting_key, $repeater_key, $repeater_item_index );
	}
}
