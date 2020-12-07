<?php
namespace ElementorExtras;

use Elementor\Core\Settings\Manager as SettingsManager;
use ElementorExtras\Core\Kits\Manager as Kits_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Main Plugin Class
 *
 * Register elementor widget.
 *
 * @since 0.1.0
 */
class ElementorExtrasPlugin {

	/**
	 * @var Settings
	 */
	public $settings;

	/**
	 * @var Modules_Manager
	 */
	public $modules_manager;

	/**
	 * @var Extensions_Manager
	 */
	public $extensions_manager;

	/**
	 * @var Kits_Manager
	 */
	public $kits_manager;

	/**
	 * @var Licensing
	 */
	public $licensing;

	/**
	 * @var WPML
	 */
	public $wpml_compatibility;

	/**
	 * @var Plugin
	 */
	public static $instance = null;

	/**
	 * @var GSAP Version
	 */
	public $gsap_version = '2.1.3';

	/**
	 * @var Links
	 */
	public $links = [
		'shop'				=> 'https://shop.namogo.com/', // Trailing '/' is important
		'support'			=> 'account/support/elementor-extras/',
		'docs'				=> 'topic/elementor-extras/',
		'docs_ig_token' 	=> 'docs/elementor-extras/gallery-extra/getting-an-instagram-access-token/',
		'generate_ig_token' => 'instagram-token/',
	];

	/**
	 * Constructor
	 *
	 * @since 0.1.0
	 *
	 * @access public
	 */
	public function __construct() {
		spl_autoload_register( [ $this, 'autoload' ] );

		add_action( 'init', [ $this, 'on_init' ] );
		add_action( 'elementor/init', [ $this, 'on_elementor_init' ], 0 );
	}

	/**
	 * On Init
	 *
	 * @since 2.2.38
	 * @access public
	 */
	public function on_init() {
		if ( ! $this->get_current_version() || version_compare( ELEMENTOR_EXTRAS_VERSION, $this->get_current_version(), '!=' ) ) {

			// Set new version
			$this->set_current_version();
		}
	}

	/**
	 * On Elementor Init
	 *
	 * @since 0.1.0
	 * @access public
	 */
	public function on_elementor_init() {

		// Elementor hooks
		$this->add_actions();

		/* INCLUDES */

		// Include extensions
		$this->includes();

		// Settings
		// $this->register_settings_managers();

		// Components
		$this->init_components();

		/* LICENSING & TRACKING */

		// Setup licesing class
		$this->init_licensing();

		// Add editor panel widgets section
		$this->init_panel_section();

		/**
		 * Elementor Extras init action.
		 *
		 * @since 1.0.0
		 */
		do_action( 'elementor_extras/init' );
	}

	/**
	 * Plugin instance
	 * 
	 * @since 0.1.0
	 * @return Plugin
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Add Actions
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 */
	private function add_actions() {

		// Register widgets hook

		add_action( 'elementor/controls/controls_registered', [ $this, 'register_controls' ] );
		add_action( 'elementor/controls/controls_registered', [ $this, 'register_group_controls' ] );

		// ——— SCRIPTS ——— //

			// Editor Scripts
			add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );

			// Front-end Scripts
			add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'enqueue_frontend_scripts' ] );
			add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_scripts' ] );

			// Preview
			// add_action( 'elementor/preview/enqueue_styles', [ $this, 'enqueue_scripts' ] );
			// add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] ); 

			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );

		// ——— STYLES ——— //

			// Admin Styles
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_styles' ] );

			// Editor Styles
			add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'enqueue_editor_styles' ] );

			// Editor Preview Styles
			add_action( 'elementor/preview/enqueue_styles', [ $this, 'enqueue_editor_preview_styles' ] );

			// Front-end Styles
			add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_frontend_styles' ] );
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @since 1.8.0
	 *
	 * @access public
	 */
	public function enqueue_admin_scripts() {

		global $pagenow;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( $pagenow == 'admin.php' && ! empty( $_GET['page'] ) && $_GET['page'] == 'elementor-extras' ) {
			
			// Register scripts
			wp_register_script(
				'elementor-extras-admin',
				plugins_url( '/assets/js/admin' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
				[],
				ELEMENTOR_EXTRAS_VERSION,
				true );

			// Enqueue scripts
			wp_enqueue_script( 'elementor-extras-admin' );
		}
	}

	/**
	 * Enqueue admin styles
	 *
	 * @since 1.1.3
	 *
	 * @access public
	 */
	public function enqueue_admin_styles() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register styles
		wp_register_style(
			'elementor-extras-admin',
			plugins_url( '/assets/css/admin' . $suffix . '.css', ELEMENTOR_EXTRAS__FILE__ ),
			[],
			ELEMENTOR_EXTRAS_VERSION
		);

		wp_register_style(
			'namogo-icons',
			ELEMENTOR_EXTRAS_ASSETS_URL . 'lib/nicons/css/nicons.css',
			[],
			ELEMENTOR_EXTRAS_VERSION
		);

		// Enqueue styles
		wp_enqueue_style( 'namogo-icons' );
		wp_enqueue_style( 'elementor-extras-admin' );
	}

	/**
	 * Enqueue editor scripts
	 *
	 * @since 0.1.0
	 *
	 * @access public
	 */
	public function enqueue_editor_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$elementor_extras_frontend_config = [
			'urls' => [
				'assets' => ELEMENTOR_EXTRAS_ASSETS_URL,
			],
		];

		// Register scripts
		wp_register_script(
			'elementor-extras-editor',
			plugins_url( '/assets/js/editor' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[],
			ELEMENTOR_EXTRAS_VERSION,
			true );

		wp_localize_script( 'elementor-extras-editor', 'elementorExtrasFrontendConfig', $elementor_extras_frontend_config );

		// Enqueue scripts
		wp_enqueue_script( 'elementor-extras-editor' );
	}

	/**
	 * Register scripts
	 *
	 * @since 0.1.0
	 *
	 * @access public
	 */
	public function register_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register scripts

		// Non-widget scripts
		wp_register_script(
			'audio-player',
			plugins_url( '/assets/lib/audio-player/audio-player' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.0.0',
			true );

		wp_register_script(
			'hc-sticky',
			plugins_url( '/assets/lib/hc-sticky/hc-sticky' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'2.2.3',
			true );

		wp_register_script(
			'parallax-gallery',
			plugins_url( '/assets/lib/parallax-gallery/parallax-gallery' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.0.0',
			true );

		wp_register_script(
			'parallax-element',
			plugins_url( '/assets/lib/parallax-element/parallax-element' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.0.0',
			true );

		wp_register_script(
			'parallax-background',
			plugins_url( '/assets/lib/parallax-background/parallax-background' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.2.0',
			true );

		// Custom widget scripts
		wp_register_script(
			'image-comparison',
			plugins_url( '/assets/lib/image-comparison/image-comparison' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery' ],
			'1.0.0',
			true );

		wp_register_script(
			'hotips',
			plugins_url( '/assets/lib/hotips/hotips' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery' ],
			'1.1.0',
			true );

		wp_register_script(
			'unfold',
			plugins_url( '/assets/lib/unfold/unfold' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery' ],
			'1.0.0',
			true );

		wp_register_script(
			'clndr',
			plugins_url( '/assets/lib/clndr/clndr' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery' ],
			'1.0.0',
			true );

		wp_register_script(
			'moment',
			plugins_url( '/assets/lib/moment/moment' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery' ],
			'1.0.0',
			true );

		wp_register_script(
			'circle-progress',
			plugins_url( '/assets/lib/circle-progress/circle-progress' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery' ],
			'1.2.2',
			true );

		wp_register_script(
			'ee-scroll-indicator',
			plugins_url( '/assets/lib/scroll-indicator/scroll-indicator' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery' ],
			'1.0.0',
			true );

		if ( 'no' !== $this->settings->get_option( 'load_google_maps_api', 'elementor_extras_apis', false ) ) {
			wp_register_script(
				'google-maps-api',
				add_query_arg(
					array( 'key' => $this->settings->get_option( 'google_maps_api_key', 'elementor_extras_apis', false ), ),
					'https://maps.googleapis.com/maps/api/js'
				),
				false,
				null,
				true );
		}

		wp_register_script(
			'gmap3',
			plugins_url( '/assets/lib/gmap3/gmap3' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery' ],
			'7.2',
			true );

		wp_register_script(
			'ee-timeline',
			plugins_url( '/assets/lib/timeline/timeline' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.0.0',
			true );

		wp_register_script(
			'ee-switcher',
			plugins_url( '/assets/lib/switcher/switcher' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.0.0',
			true );

		wp_register_script(
			'toggle-element',
			plugins_url( '/assets/lib/toggle-element/toggle-element' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.2.0',
			true );

		wp_register_script(
			'slidebars',
			plugins_url( '/assets/lib/slidebars/js/slidebars' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.2.7',
			true );

		wp_register_script(
			'slide-menu',
			plugins_url( '/assets/lib/slide-menu/slide-menu' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.0.1',
			true );

		wp_register_script(
			'splittext',
			plugins_url( '/assets/lib/splittext/splittext' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[],
			'0.4.0',
			true );

		wp_register_script(
			'jquery-appear',
			plugins_url( '/assets/lib/jquery-appear/jquery.appear' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'0.3.6',
			true );

		wp_register_script(
			'jquery-visible',
			plugins_url( '/assets/lib/jquery-visible/jquery.visible' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.0.0',
			true );

		wp_register_script(
			'jquery-easing',
			plugins_url( '/assets/lib/jquery-easing/jquery-easing' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.3.2',
			true );

		wp_register_script(
			'jquery-mobile',
			plugins_url( '/assets/lib/jquery-mobile/jquery-mobile' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.4.3',
			true );

		wp_register_script(
			'jquery-long-shadow',
			plugins_url( '/assets/lib/jquery-long-shadow/jquery.longShadow' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.1.0',
			true );

		wp_register_script(
			'video-player',
			plugins_url( '/assets/lib/video-player/video-player' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.0.0',
			true );

		wp_register_script(
			'iphone-inline-video',
			plugins_url( '/assets/lib/iphone-inline-video/iphone-inline-video' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[],
			'2.2.2',
			true );

		wp_register_script(
			'tablesorter',
			plugins_url( '/assets/lib/tablesorter/jquery.tablesorter' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[],
			'2.2.2',
			true );

		wp_register_script(
			'isotope',
			plugins_url( '/assets/lib/isotope/isotope.pkgd' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[],
			'3.0.4',
			true );

		wp_register_script(
			'filtery',
			plugins_url( '/assets/lib/filtery/filtery' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.0.0',
			true );

		wp_register_script(
			'magnific-popup',
			plugins_url( '/assets/lib/magnific-popup/js/magnific-popup' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.1.0',
			true );

		wp_register_script(
			'tilt',
			plugins_url( '/assets/lib/tilt/tilt.jquery' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.0.0',
			true );

		wp_register_script(
			'infinite-scroll-ee',
			plugins_url( '/assets/lib/infinite-scroll/infinite-scroll.pkgd' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[],
			'3.0.2',
			true );

		wp_register_script(
			'jquery-resize-ee',
			plugins_url( '/assets/lib/jquery-resize/jquery.resize' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[ 'jquery' ],
			'3.0.2',
			true );

		wp_register_script(
			'custom-ease',
			plugins_url( '/assets/lib/custom-ease/custom-ease' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[],
			'0.2.2',
			true );

		wp_register_script(
			'elementor-extras-frontend',
			plugins_url( '/assets/js/frontend' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[],
			ELEMENTOR_EXTRAS_VERSION,
			true );

		wp_register_script(
			'jquery-elementor-select2',
			ELEMENTOR_ASSETS_URL . 'lib/e-select2/js/e-select2.full' . $suffix . '.js',
			[
				'jquery',
			],
			'4.0.6-rc.1',
			true
		);

	}

	/**
	 * Enqueue scripts for frontend
	 *
	 * @since 0.1.0
	 *
	 * @access public
	 */
	public function enqueue_frontend_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$elementor_extras_frontend_config = [
			'urls' => [
				'assets' => ELEMENTOR_EXTRAS_ASSETS_URL,
			],
			'refreshableWidgets' => apply_filters( 'elementor_extras/widgets/refreshable', [
				'ee-offcanvas.classic',
				'ee-popup.classic',
				'gallery-slider.default',
				'media-carousel.default',
				'image-carousel.default',
				'slides.default',
			] ),
		];

		// GSAP TweenMax
		if ( 'no' !== $this->settings->get_option( 'load_tweenmax', 'elementor_extras_advanced', false ) ) {
			wp_enqueue_script( 
				'gsap-js',
				'//cdnjs.cloudflare.com/ajax/libs/gsap/' . $this->gsap_version . '/TweenMax.min.js',
				array(),
				null,
				true
			);
		}

		wp_localize_script( 'elementor-extras-frontend', 'elementorExtrasFrontendConfig', $elementor_extras_frontend_config );
		wp_enqueue_script( 'elementor-extras-frontend' );
	}

	/**
	 * Enqueue preview scripts
	 *
	 * @since 0.1.0
	 *
	 * @access public
	 */
	public function enqueue_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register scripts
		wp_register_script(
			'elementor-extras-preview',
			plugins_url( '/assets/js/preview' . $suffix . '.js', ELEMENTOR_EXTRAS__FILE__ ),
			[],
			ELEMENTOR_EXTRAS_VERSION,
			true );

		wp_enqueue_script( 'elementor-extras-preview' );
	}

	/**
	 * Enqueue preview styles
	 *
	 * @since 1.6.0
	 *
	 * @access public
	 */
	public function enqueue_editor_preview_styles() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register styles
		wp_register_style(
			'elementor-extras-editor-preview',
			plugins_url( '/assets/css/editor-preview' . $suffix . '.css', ELEMENTOR_EXTRAS__FILE__ ),
			[],
			ELEMENTOR_EXTRAS_VERSION
		);

		// Enqueue style
		wp_enqueue_style( 'magnific-popup' );
		wp_enqueue_style( 'elementor-extras-editor-preview' );
	}

	/**
	 * Enqueue frontend styles
	 *
	 * @since 0.1.0
	 *
	 * @access public
	 */
	public function enqueue_frontend_styles() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$direction_suffix = is_rtl() ? '-rtl' : '';

		// Register styles
		wp_register_style(
			'magnific-popup',
			plugins_url( '/assets/lib/magnific-popup/css/magnific-popup' . $suffix . '.css', ELEMENTOR_EXTRAS__FILE__ ),
			[],
			ELEMENTOR_EXTRAS_VERSION
		);

		wp_register_style(
			'elementor-extras-frontend',
			plugins_url( '/assets/css/frontend' . $direction_suffix . $suffix . '.css', ELEMENTOR_EXTRAS__FILE__ ),
			[],
			ELEMENTOR_EXTRAS_VERSION
		);

		wp_register_style(
			'namogo-icons',
			ELEMENTOR_EXTRAS_ASSETS_URL . 'lib/nicons/css/nicons.css',
			[],
			ELEMENTOR_EXTRAS_VERSION
		);

		wp_register_style(
			'elementor-select2',
			ELEMENTOR_ASSETS_URL . 'lib/e-select2/css/e-select2' . $suffix . '.css',
			[],
			'4.0.6-rc.1'
		);

		// Enqueue styles
		wp_enqueue_style( 'namogo-icons' );
		wp_enqueue_style( 'elementor-extras-frontend' );
	}

	/**
	 * Enqueue editor styles
	 *
	 * @since 0.1.0
	 *
	 * @access public
	 */
	public function enqueue_editor_styles() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		
		// Register styles
		wp_register_style(
			'namogo-icons',
			ELEMENTOR_EXTRAS_ASSETS_URL . 'lib/nicons/css/nicons.css',
			[],
			ELEMENTOR_EXTRAS_VERSION
		);

		// Register styles
		wp_register_style(
			'elementor-extras-editor',
			plugins_url( '/assets/css/editor' . $suffix . '.css', ELEMENTOR_EXTRAS__FILE__ ),
			[],
			ELEMENTOR_EXTRAS_VERSION
		);

		// Enqueue style
		wp_enqueue_style( 'namogo-icons' );
		wp_enqueue_style( 'elementor-extras-editor' );
	}

	/**
	 * Include components
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 */
	private function includes() {

		// Utils
		elementor_extras_include( 'includes/utils.php' );

		// Widget integrations
		elementor_extras_include( 'includes/compatibility/wpml/compatibility.php' );

		// Managers
		elementor_extras_include( 'includes/managers/extensions.php' );
		elementor_extras_include( 'includes/managers/modules.php' );

		// Panel Settings
		elementor_extras_include( 'core/settings/global/model.php' );
		elementor_extras_include( 'core/settings/global/manager.php' );
	}

	/**
	 * Sections init
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 */
	public function init_panel_section() {
		\Elementor\Plugin::instance()->elements_manager->add_category(
			'elementor-extras',
			array( 'title'  => esc_html__( 'Extras for Elementor', 'elementor-extras' ), ),
			1
		);
	}

	/**
	 * Sections init
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 */
	private function register_settings_managers() {
		SettingsManager::add_settings_manager( new \ElementorExtras\Core\Settings\General\Manager );
	}

	/**
	 * Components init
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 */
	private function init_components() {
		$this->settings 				= new Settings_API();
		$this->modules_manager 			= new Modules_Manager();
		$this->extensions_manager 		= new Extensions_Manager();
		// $this->kits_manager 			= new Kits_Manager();
		$this->wpml_compatibility 		= new Compatibility\WPML();
	}

	/**
	 * Initializes the licensing class
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 */
	private function init_licensing() {

		if ( is_admin() ) {

			// Setup the settings page and validation
			$this->licensing = new Licensing(
				ELEMENTOR_EXTRAS_SL_ITEM_ID,
				ELEMENTOR_EXTRAS_SL_ITEM_NAME,
				ELEMENTOR_EXTRAS_TEXTDOMAIN
			);
		}

	}

	/**
	 * Register Group Controls
	 *
	 * @since 1.1.4
	 *
	 * @access private
	 */
	public function register_group_controls() {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;

		// Include Control Groups
		elementor_extras_include( 'includes/controls/groups/sticky.php' );
		elementor_extras_include( 'includes/controls/groups/long-shadow.php' );
		elementor_extras_include( 'includes/controls/groups/button-effect.php' );
		elementor_extras_include( 'includes/controls/groups/transition.php' );
		elementor_extras_include( 'includes/controls/groups/tooltip.php' );

		// Add Control Groups
		$controls_manager->add_group_control( 'long-shadow', new Group_Control_Long_Shadow() );
		$controls_manager->add_group_control( 'effect', new Group_Control_Button_Effect() );
		$controls_manager->add_group_control( 'ee-transition', new Group_Control_Transition() );
		$controls_manager->add_group_control( 'ee-tooltip', new Group_Control_Tooltip() );
	}

	/**
	 * Register Controls
	 *
	 * @since 2.0.0
	 *
	 * @access private
	 */
	public function register_controls() {

		// Include Controls
		elementor_extras_include( 'includes/controls/query.php' );
		elementor_extras_include( 'includes/controls/snazzy.php' );

		// Register Controls
		\Elementor\Plugin::instance()->controls_manager->register_control( 'ee-snazzy', new Control_Snazzy() );
		\Elementor\Plugin::instance()->controls_manager->register_control( 'ee-query', new Control_Query() );
	}

	/**
	 * Autoload Classes
	 *
	 * @since 1.6.0
	 */
	public function autoload( $class ) {

		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		$has_class_alias = isset( $this->classes_aliases[ $class ] );

		// Backward Compatibility: Save old class name for set an alias after the new class is loaded
		if ( $has_class_alias ) {
			$class_alias_name = $this->classes_aliases[ $class ];
			$class_to_load = $class_alias_name;
		} else {
			$class_to_load = $class;
		}

		if ( ! class_exists( $class_to_load ) ) {

			$filename = strtolower(
				preg_replace(
					[ '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
					[ '', '$1-$2', '-', DIRECTORY_SEPARATOR ],
					$class_to_load
				)
			);

			$filename = ELEMENTOR_EXTRAS_PATH . $filename . '.php';

			if ( is_readable( $filename ) ) {
				include( $filename );
			}
		}

		if ( $has_class_alias ) {
			class_alias( $class_alias_name, $class );
		}
	}

	/**
	 * Check if Woocommerce is installed and active
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 */
	public function is_woocommerce_active() {
		return in_array( 
			'woocommerce/woocommerce.php', 
			apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) 
		);
	}

	/**
	 * Get a specific link from the links property
	 *
	 * @since 2.2.23
	 *
	 * @access public
	 */
	public function get_link( $name ) {
		return $this->links['shop'] . $this->links[ $name ];
	}

	/**
	 * Get the current plungin version from options
	 *
	 * @since 2.2.38
	 *
	 * @access public
	 */
	public function get_current_version() {
		return get_option('elementor_extras_version', false);
	}

	/**
	 * Get the current plungin version from options
	 *
	 * @since 2.2.38
	 *
	 * @access public
	 */
	public function set_current_version() {
		update_option( 'elementor_extras_version', ELEMENTOR_EXTRAS_VERSION );
	}
}

if ( ! defined( 'ELEMENTOR_EXTRAS_TESTS' ) ) {
	// In tests we run the instance manually.
	ElementorExtrasPlugin::instance();
}

