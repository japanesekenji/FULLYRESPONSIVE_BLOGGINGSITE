<?php
namespace ElementorExtras\Modules\Posts;

// Extras for Elementor Classes
use ElementorExtras\Base\Module_Base;
use ElementorExtras\Modules\Posts\Widgets\Posts_Base;

use ElementorPro\Modules\ThemeBuilder\Module as ThemeBuilder;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\Posts\Module
 *
 * @since  1.6.0
 */
class Module extends Module_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  1.6.0
	 * @return string
	 */
	public function get_name() {
		return 'posts';
	}

	/**
	 * Get Widgets
	 * 
	 * Get the modules' widgets
	 *
	 * @since  1.6.0
	 * @return array
	 */
	public function get_widgets() {
		return [
			'Posts',
			'Timeline',
		];
	}

	/**
	 * Get Content Parts
	 * 
	 * Get the content parts
	 *
	 * @since  1.6.0
	 * @return array
	 */
	public static function get_content_parts() {
		$content_parts = [
			'terms',
			'title',
			'avatar',
			'author',
			'date',
			'comments',
			// 'custom_fields',
			'excerpt',
			'button',
		];

		if ( is_woocommerce_active() ) {
			$content_parts[] = 'price';
		}

		return $content_parts;
	}

	/**
	 * Get Post Parts
	 * 
	 * Get the post parts
	 *
	 * @since  1.6.0
	 * @return array
	 */
	public static function get_post_parts() {
		$post_parts = [
			'terms',
			'title',	
			'excerpt',
			'button',
			'metas',
		];

		return $post_parts;
	}

	/**
	 * Get Content Parts
	 * 
	 * Get the post content parts
	 *
	 * @since  1.6.0
	 * @return array
	 */
	public static function get_content_post_parts() {
		$post_parts = [
			'terms',
			'title',	
			'excerpt',
			'button',
		];

		return $post_parts;
	}

	/**
	 * Get Meta Parts
	 * 
	 * Get the available metas
	 *
	 * @since  1.6.0
	 * @return array
	 */
	public static function get_meta_parts() {
		$meta_parts = [
			'author',
			'date',
			'comments',
			// 'custom_fields',
		];

		if ( is_woocommerce_active() ) {
			$meta_parts[] = 'price';
		}

		return $meta_parts;
	}

	/**
	 * Checks if current page applies for custom pagination handling
	 *
	 * @since  2.2.38
	 * @return array
	 */
	public static function is_custom_pagination() {
		return is_singular() && ! is_front_page() && ! is_singular('page');
	}

	/**
	 * Bypass WP 404 on singular custom pagination where
	 * Wordpress is using the 'page' query var and
	 * post content paging is not being used
	 *
	 * @since  2.2.38
	 * @return bool
	 */
	public function handle_pagination_404( $preempt, $wp_query ) {
		// Conditions leave our posts built with Elementor
		// since widgets can also be present on theme builder
		if ( ! $preempt && ! empty( $wp_query->query_vars['ee-page'] ) && self::is_custom_pagination() ) {
			$preempt = true;
		}

		return $preempt;
	}

	/**
	 * Use our our query var and match it with
	 * the single pagination 'page' query var 
	 *
	 * @since  2.2.38
	 * @return array
	 */
	public function add_pagination_query_var( $request ) {
		if ( ! empty( $request['page'] ) && intval( $request['page'] ) > 1 ) {
			$request['ee-page'] = $request['page'];
		}

		return $request;
	}

	/**
	 * Register custom pagination query var
	 *
	 * @since  2.2.38
	 * @return array
	 */
	public function register_pagination_query_var( $vars ) {
		$vars[] = 'ee-page';
		return $vars;
	}

	public function __construct() {
		parent::__construct();

		add_filter( 'query_vars', 		[ $this, 'register_pagination_query_var' ], 10, 1 );
		add_filter( 'request', 			[ $this, 'add_pagination_query_var' ] );
		add_filter( 'pre_handle_404', 	[ $this, 'handle_pagination_404' ], 10, 2 );
	}
}
