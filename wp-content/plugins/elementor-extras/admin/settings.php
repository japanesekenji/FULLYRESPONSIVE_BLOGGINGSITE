<?php
namespace ElementorExtras;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Settings
 *
 * @since 1.8.0
 */
class Settings extends Settings_Page {

	const PAGE_ID = 'elementor-extras';

	// Tabs
	const TAB_WIDGETS 		= 'widgets';
	const TAB_EXTENSIONS 	= 'extensions';
	const TAB_ADVANCED 		= 'advanced';
	const TAB_LICENSE 		= 'license';
	const TAB_APIS 			= 'apis';
	const TAB_DOCUMENTATION	= 'documentation';
	const TAB_SUPPORT		= 'support';

	private $_tabs;

	private $_widgets_count;

	private $_extensions_count;

	/**
	 * menu
	 *
	 * Adds the item to the menu
	 *
	 * @since 1.8.0
	 *
	 * @access public
	*/

	public function menu() {
		$slug = 'elementor-extras';
		$capability = 'manage_options';

		add_submenu_page(
			\Elementor\Settings::PAGE_ID,
			$this->get_page_title(),
			__( 'Extras', 'elementor-extras' ),
			$capability,
			$slug,
			[ $this, 'render_page' ]
		);
	}

	/**
	* enqueue_scripts
	*
	* Enqueue styles and scripts
	*
	* @since 1.8.0
	*
	* @access public
	*/
	
	public function enqueue_scripts() {}

	/**
	* Hooked into admin_init action
	*
	* @since 1.8.0
	*
	* @access public
	*/

	public function init() {
		parent::init();

		// Refresh Instagram Access Token
		$this->refresh_instagram_access_token();
	}

	/**
	* Creates the tabs object
	*
	* @since 1.8.0
	*
	* @access protected
	*/

	protected function create_page_tabs() {
		return $this->_tabs;
	}

	/**
	 * Gets the settings sections
	 *
	 * @since 1.8.0
	 *
	 * @access public
	*/

	public function get_settings_sections() {

		$this->_widgets_count = $this->get_widgets_count( true );
		$this->_extensions_count = $this->get_extensions_count( true );

		$license_status = 'active';
		$license_key = Licensing::get_license_key();
		$license_data = Licensing::get_license_data();
    	$license_status = 'active';

		$sections = array(
			array(
				'id'    => $this->settings_prefix . self::TAB_WIDGETS,
				'title' => __( 'Widgets', 'elementor-extras' ),
				'count'	=> $this->_widgets_count,
				'label' => $this->_widgets_count > 0 ? '' : 'error',
				'desc'	=> __( 'Disable widgets from Extras. If disabled, a widget will no longer be available in the Elementor editor panel. We strongly recommend disabling the widgets you don\'t plan on using to improve the load time of the Elementor editor.', 'elementor-extras' ),
			),
			array(
				'id'    => $this->settings_prefix . self::TAB_EXTENSIONS,
				'title' => __( 'Extensions', 'elementor-extras' ),
				'count' => $this->_extensions_count,
				'label' => $this->_extensions_count > 0 ? '' : 'error',
				'desc'	=> __( 'Extras extensions are features added to the default Elementor elements. They display additional controls that can be found usually under the Advanced tab of each element. Below you can disable any or all these extensions. If disabled, these additional controls will no longer be available in the Elementor editor panel.', 'elementor-extras' ),
			),
			array(
				'id'    => $this->settings_prefix . self::TAB_APIS,
				'title' => __( 'APIs', 'elementor-extras' ),
			),
			array(
				'id'    => $this->settings_prefix . self::TAB_ADVANCED,
				'title' => __( 'Advanced', 'elementor-extras' ),
			),
			array(
				'id'    => $this->settings_prefix . self::TAB_LICENSE,
				'title' => __( 'License', 'elementor-extras' ),
				'count' => $license_status,
				'label' => 'active' === $license_status ? 'success' : 'error',
				'link'	=> admin_url( 'admin.php?page=elementor_extras_license' ),
			),
			array(
				'id'    => $this->settings_prefix . self::TAB_SUPPORT,
				'title' => __( 'Get Support', 'elementor-extras' ),
				'target'=> '_blank',
				'link'	=> ElementorExtrasPlugin::$instance->get_link('support'),
				'icon'	=> 'dashicons dashicons-external',
			),
			array(
				'id'    => $this->settings_prefix . self::TAB_DOCUMENTATION,
				'title' => __( 'Documentation', 'elementor-extras' ),
				'target'=> '_blank',
				'link'	=> ElementorExtrasPlugin::$instance->get_link('docs'),
				'icon'	=> 'dashicons dashicons-external',
			),
		);

		return $sections;
	}

	/**
	 * Gets the settings fields
	 *
	 * @since 1.8.0
	 *
	 * @access public
	*/

	public function get_settings_fields() {
		$fields = [];

		$sections = $this->get_settings_sections();

		foreach( $sections as $section ) {
			if ( $this->settings_api->is_tab_linked( $section ) )
				continue;

			$fields[ $section['id'] ] = call_user_func( array( $this, 'get_' . str_replace( $this->settings_prefix, '', $section['id'] ) . '_fields' ) );
		}

		return $fields;
	}

	/**
	* Returns the number of Extras widgets
	*
	* @since 2.0.0
	*
	* @access protected
	*/
	protected function get_widgets_count( $enabled_only = false ) {
		$modules = ElementorExtrasPlugin::$instance->modules_manager->get_modules();
		$count = 0;

		foreach( $modules as $module ) {
			$widgets = $module->get_widgets();
			foreach( $widgets as $widget ) {
				if ( ! $enabled_only ) {
					$count ++;
				} else {
					if ( ! $module->is_widget_disabled( strtolower( $widget ) ) ) {
						$count++;
					}
				}
			}
		}

		return $count;
	}

	/**
	* Returns the fields for the widgets section
	*
	* @since 1.8.0
	*
	* @access protected
	*/
	protected function get_widgets_fields() {

		$fields = [];

		$modules = ElementorExtrasPlugin::$instance->modules_manager->get_modules();

		foreach( $modules as $module ) {

			$module_name = $module->get_name();

			$module_class_name = str_replace( '-', ' ', $module_name );
			$module_class_name = str_replace( ' ', '', ucwords( $module_class_name ) );

			$widgets = $module->get_widgets();

			foreach( $widgets as $widget ) {

				$class_name = 'ElementorExtras\Modules\\' . $module_class_name . '\Widgets\\' . $widget;

				$widget_title 	= str_replace( '_', ' ', ucwords( $widget ) );
				$widget_slug 	= strtolower( $widget );

				$field = [
					'name'		=> 'enable_' . $widget_slug,
					'label' 	=> $widget_title,
					'desc' 		=> __( 'Enable', 'elementor-extras' ),
					'type' 		=> 'checkbox',
					'default' 	=> 'on',
				];

				if ( $class_name::requires_elementor_pro() && ! is_elementor_pro_active() ) {
					$field['type'] = 'html';
					$field['note'] = __( 'You need Elementor Pro installed and activated for this widget to be available.', 'elementor-extras' );

					unset( $field['desc'] );
				}

				$fields[] = $field;

			}
		}

		return $fields;
	}

	/**
	* Returns the number of Extras extensions
	*
	* @since 2.0.0
	*
	* @access protected
	*/
	protected function get_extensions_count( $enabled_only = false ) {
		$extensions = ElementorExtrasPlugin::$instance->extensions_manager->available_extensions;
		$count = 0;

		foreach( $extensions as $extension_id ) {
			$extension_name = str_replace( '-', '_', $extension_id );
			if ( ! $enabled_only ) {
				$count ++;
			} else {
				if ( ! ElementorExtrasPlugin::$instance->extensions_manager->is_disabled( $extension_name ) ) {
					$count++;
				}
			}
		}

		return $count;
	}

	/**
	* Returns the fields for the extensions section
	*
	* @since 1.8.0
	*
	* @access protected
	*/
	protected function get_extensions_fields() {

		$fields = [];

		$extensions = ElementorExtrasPlugin::$instance->extensions_manager->available_extensions;

		foreach( $extensions as $extension_id ) {

			$extension_name = str_replace( '-', '_', $extension_id );
			$class_name = 'ElementorExtras\Extensions\Extension_' . ucwords( $extension_name );

			$extension_title = str_replace( '-', ' ', $extension_id );
			$extension_title = ucwords( $extension_title );

			$description = $class_name::get_description();

			$fields[] = [
				'name'		=> 'enable_' . $extension_name,
				'label' 	=> $extension_title,
				'desc' 		=> __( 'Enable', 'elementor-extras' ),
				'type' 		=> 'checkbox',
				'default' 	=> $class_name::is_default_disabled() ? 'off' : 'on',
				'note'		=> $description,
			];
		}

		return $fields;
	}

	/**
	* Return the fields for the advanced section
	*
	* @since 2.0.0
	*
	* @access protected
	*/
	protected function get_advanced_fields() {

		$gsap_version = sprintf( __( '%1$sCurrent TweenMax version: %2$s', 'elementor-extras' ), '<br>', '<strong>' . ElementorExtrasPlugin::$instance->gsap_version . '</strong>' );
		$gsap_description = sprintf( __( 'By default, we load GSAP\'s TweenMax which we use for Parallax Elements and other extensions and widgets. If another plugin uses this as well you might end up with conflicts. Set this to "No" ONLY such cases, otherwise some Extras functionality will not work. %s', 'elementor-extras' ), $gsap_version );

		$fields = [
			[
				'name'		=> 'enable_beta',
				'label'		=> __( 'Enable Beta Versions', 'elementor-extras' ),
				'desc' 		=> __( 'Enable updates to beta versions of Extras. If you update to a beta version and wish to revert back to a stable release, you will need to download that version from your account and install it manually.', 'elementor-extras' ),
				'no_desc_p' => false,
				'note'		=> '<div class="ee-admin-notice ee-admin-notice--warning notice notice-warning inline"><p><strong>WARNING:</strong> Do not update to beta versions on production websites!</p></div>',
				'no_note_p' => true,
				'type'		=> 'radio',
				'default'	=> 'no',
				'options'	=> [
					'yes' 	=> __( 'Yes', 'elementor-extras' ),
					'no' 	=> __( 'No', 'elementor-extras' ),
				]
			],
			[
				'name'		=> 'load_tweenmax',
				'label'		=> __( 'Load TweenMax', 'elementor-extras' ),
				'desc' 		=> $gsap_description,
				'type'		=> 'radio',
				'default'	=> 'yes',
				'options'	=> [
					'yes' 	=> __( 'Yes', 'elementor-extras' ),
					'no' 	=> __( 'No', 'elementor-extras' ),
				]
			],
			[
				'name'		=> 'load_google_maps_api',
				'label'		=> __( 'Load Google Maps API', 'elementor-extras' ),
				'desc' 		=> __( 'You can disable loading the Google Maps API script if it\'s already added from a theme or plugin.', 'elementor-extras' ),
				'type'		=> 'radio',
				'default'	=> 'yes',
				'options'	=> [
					'yes' 	=> __( 'Yes', 'elementor-extras' ),
					'no' 	=> __( 'No', 'elementor-extras' ),
				]
			],
		];

		return $fields;

	}

	/**
	* Returns the fields for the API section
	*
	* @since 2.0.0
	*
	* @access protected
	*/
	protected function get_apis_fields() {

		$gmap_description = sprintf( __( 'You can get your API key %1$shere%2$s', 'elementor-extras' ), '<a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key">', '</a>' );

		$snazzy_description = sprintf( __( 'You can get your API key %1$shere%2$s after you create an account on Snazzy Maps.', 'elementor-extras' ), '<a target="_blank" href="https://snazzymaps.com/account/developer">', '</a>' );

		$insta_token_description = sprintf( __( 'Find out %1$show to get your instagram access token%2$s.', 'elementor-extras' ), '<a target="_blank" href="' . ElementorExtrasPlugin::$instance->get_link('docs_ig_token') . '">', '</a>' );

		$fields = [
			[
				'name'		=> 'google_maps_api_key',
				'label'		=> __( 'Google Maps API Key', 'elementor-extras' ),
				'desc' 		=> $gmap_description,
				'type'		=> 'text',
			],
			[
				'name'		=> 'snazzy_maps_api_key',
				'label'		=> __( 'Snazzy Maps API Key', 'elementor-extras' ),
				'desc' 		=> $snazzy_description,
				'type'		=> 'text',
			],			
			[
				'name'		=> 'snazzy_maps_endpoint',
				'label'		=> __( 'Snazzy Maps Endpoint', 'elementor-extras' ),
				'desc' 		=> __( 'Select where to search for map styles. "Explore" searches all public map styles, "My Styles" search the styles you created on Snazzy Maps and "Favorites" fetches styles from the ones you added to your favorites.', 'elementor-extras' ),
				'type'		=> 'select',
				'options'	=> [
					'explore' 	=> __( 'Explore', 'elementor-extras' ),
					'my-styles' => __( 'My Styles', 'elementor-extras' ),
					'favorites' => __( 'Favorites', 'elementor-extras' ),
				],
			],
			[
				'name'		=> 'instagram_access_token',
				'label'		=> __( 'Instagram Access Token', 'elementor-extras' ),
				'desc' 		=> $insta_token_description,
				'type'		=> 'text',
			],
		];

		return $fields;

	}

	/**
	* Refresh Long-Lived Instagram Access Token
	*
	* @since 2.2.23
	*
	* @access public
	*/

	public function refresh_instagram_access_token() {
		$update_token_key 	= self::UPDATED_INSTA_ACCESS_TOKEN;
		$api_endpoint 		= self::REFRESH_INSTA_ACCESS_TOKEN_ENDPOINT;
		$access_token 		= trim( $this->settings_api->get_option( 'instagram_access_token', 'elementor_extras_apis', false ) );

		if ( empty( $access_token ) ) {
			return;
		}

		$updated = get_transient( $update_token_key );

		if ( ! empty( $updated ) ) {
			return;
		}

		$endpoint_url = add_query_arg( [
				'access_token' => $access_token,
				'grant_type'   => 'ig_refresh_token',
			],
			$api_endpoint
		);

		$response = wp_remote_get( $endpoint_url );

		if ( ! $response || 200 !== wp_remote_retrieve_response_code( $response ) || is_wp_error( $response ) ) {
			set_transient( $update_token_key, 'error', DAY_IN_SECONDS );
			return;
		}

		$body = wp_remote_retrieve_body( $response );

		if ( ! $body ) {
			set_transient( $update_token_key, 'error', DAY_IN_SECONDS );
			return;
		}

		$body = @json_decode( $body, true );

		if ( empty( $body['access_token'] ) || empty( $body['expires_in'] ) ) {
			set_transient( $update_token_key, 'error', DAY_IN_SECONDS );
			return;
		}

		set_transient( $update_token_key, 'updated', 30 * DAY_IN_SECONDS );
	}

	/**
	* Returns current page title
	*
	* @since 1.8.0
	*
	* @access protected
	*/
	protected function get_page_title() {
		return __( 'Extras for Elementor', 'elementor-extras' );
	}

}

// initialize
new Settings();

?>