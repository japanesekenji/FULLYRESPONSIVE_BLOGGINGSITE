<?php
namespace ElementorExtras;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handles license input and validation
 */
class Licensing {

	/**
	 * @var string  The license key for this installation
	 */
	private $license_key;

	/**
	 * The text domain of the plugin or theme using this class.
	 * Populated in the class's constructor.
	 *
	 * @var String  The text domain of the plugin / theme.
	 */
	private $text_domain;

	/**
	 * The name of the product using this class. Configured in the class's constructor.
	 *
	 * @var int     The name of the product (plugin / theme) using this class.
	 */
	private $product_name;

	// License Statuses
	const STATUS_VALID 			= 'valid';
	const STATUS_INVALID 		= 'invalid';
	const STATUS_EXPIRED 		= 'expired';
	const STATUS_DEACTIVATED 	= 'deactivated';
	const STATUS_SITE_INACTIVE 	= 'site_inactive';
	const STATUS_DISABLED 		= 'disabled';
	const STATUS_LIMIT 			= 'no_activations_left';

	/**
	 * Construct
	 *
	 * @since 0.1.0
	 */
	public function __construct( $product_id, $product_name, $text_domain ) {
		// Store setup data
		$this->text_domain 		= $text_domain;
		$this->product_name 	= $product_name;

		// Init
		$this->add_actions();
	}

	/**
	 * Adds actions required for class functionality
	 *
	 * @since 0.1.0
	 */
	public function add_actions() {
		if ( is_admin() ) {

			// Add the menu screen for inserting license information
			add_action( 'admin_menu', 										array( $this, 'add_license_settings_page' ), 201 );
			add_action( 'admin_init', 										array( $this, 'register_license_settings' ) );
			add_action( 'admin_post_elementor_extras_license_activate', 	array( $this, 'activate_license' ) );
			add_action( 'admin_post_elementor_extras_license_deactivate', 	array( $this, 'deactivate_license' ) );
			add_action( 'admin_notices', 									array( $this, 'admin_notices' ), 19 );
		}
	}

	/**
	 * Get status errors
	 * Retrieves error messages data
	 *
	 * @since 	2.1.0
	 * @return  array
	 */
	public static function get_status_errors() {

		return [
			self::STATUS_EXPIRED => [
				'title' 		=> __( 'Your License Has Expired', 'elementor-extras' ),
				'message' 		=> __( 'Seems that your license has expired.', 'elementor-extras' ),
				'action'		=> __( '<a href="%s" target="_blank">Renew your license today</a> to keep getting updates and access to premium support.', 'elementor-extras' ),
				'link'			=> 'https://shop.namogo.com/checkout/?edd_license_key=' . self::get_license_key() . '&download_id=19',
				'label'			=> 'expired',
				'dismissable' 	=> [
					'key' 		=> 'license-expired',
					'duration' 	=> 7,
				],
			],
			self::STATUS_DISABLED => [
				'title' 		=> __( 'Your License Is Inactive', 'elementor-extras' ),
				'message' 		=> __( '<strong>Your license key has been cancelled</strong> (most likely due to a refund request). Please consider acquiring a new license.', 'elementor-extras' ),
				'label'			=> 'disabled',
				'dismissable' 	=> [
					'key' 		=> 'license-expired',
					'duration' 	=> 2,
				],
			],
			self::STATUS_INVALID => [
				'title' 		=> __( 'Invalid License', 'elementor-extras' ),
				'message' 		=> __( '<strong>Your license key doesn\'t match the current domain</strong>..', 'elementor-extras' ),
				'action'		=> __( 'Please check the license key you received after purchase and <a href="%s">re-activate it on this domain.</a>.', 'elementor-extras' ),
				'link'			=> admin_url( 'admin.php?page=elementor_extras_license' ),
				'label'			=> 'invalid',
			],
			self::STATUS_SITE_INACTIVE => [
				'title' 		=> __( 'License Mismatch', 'elementor-extras' ),
				'message' 		=> __( '<strong>Your license key doesn\'t seem to be active on this domain</strong>. This usually happens when moving the website from one domain to another, changing the website URL or migrating to HTTPS/SSL.', 'elementor-extras' ),
				'action'		=> __( 'Please <a href="%s">re-activate your license</a>.', 'elementor-extras' ),
				'link'			=> admin_url( 'admin.php?page=elementor_extras_license' ),
				'label'			=> 'mismatch',
			],
			self::STATUS_LIMIT 	=> [
				'title' 		=> __( 'Activation Limit Reached', 'elementor-extras' ),
				'message'		=> __( '<strong>Unfortunately you don\'t have any more activations left.</strong> Login to your account to see on which sites the license is currently active.', 'elementor-extras' ),
				'action'		=> __( '<a href="%s" target="_blank">Please upgrade your license.</a>', 'elementor-extras' ),
				'link'			=> 'https://shop.namogo.com/account/license-keys/',
				'label'			=> 'limit reached',
			],
		];
	}

	/**
	 * Get errors
	 *
	 * Retrieves errors and their corresponding bodies
	 *
	 * @since 	2.1.0
	 * @return 	array
	 * @access 	public
	 */
	public function get_activation_errors() {
		return [
			'no_activations_left' 	=> $this->get_error_body( self::STATUS_LIMIT ),
			'expired' 				=> $this->get_error_body( self::STATUS_EXPIRED ),
			'missing' 				=> $this->get_error_body( self::STATUS_INVALID ),
			'revoked' 				=> $this->get_error_body( self::STATUS_DISABLED ),
			'disabled' 				=> $this->get_error_body( self::STATUS_DISABLED ),
			'key_mismatch' 			=> $this->get_error_body( self::STATUS_SITE_INACTIVE ),
		];
	}

	/**
	 * Build error message body string
	 *
	 * @since 	2.1.0
	 * @return 	string
	 * @access 	private
	 */
	private function get_error_body( $key ) {
		$errors 	= self::get_status_errors();

		$message 	= $errors[ $key ][ 'message' ];
		$body 		= $message;

		if ( ! empty( $errors[ $key ][ 'action' ] ) ) {
			if ( ! empty( $errors[ $key ][ 'link' ] ) ) {
				$body .= ' ' . sprintf( $errors[ $key ][ 'action' ], $errors[ $key ][ 'link' ] );
			} else {
				$body .= ' ' . $errors[ $key ][ 'action' ];
			}
		}

		return $body;
	}

	/**
	 * Retrieve error message
	 * Returns generic message if error index doesn't exist
	 *
	 * @since  	2.1.0
	 * @return 	string
	 * @access  public
	 */
	public function get_activation_error_message( $error ) {
		$errors = $this->get_activation_errors();

		if ( isset( $errors[ $error ] ) ) {
			$error_msg = $errors[ $error ];
		} else {
			$error_msg = __( 'An error occurred. Please check your internet connection and try again. If the problem persists, contact our support.', 'elementor-extras' ) . ' (' . $error . ')';
		}

		return $error_msg;
	}

	/**
	 * Gets the currently set license key
	 *
	 * @since 	0.1.0
	 * @return 	bool|string   The product license key, or false
	 */
	public static function get_license_key() {

		$license = get_option( 'elementor_extras_license_key' );

		

		return 'B5E0B5F8DD8689E6ACA49DD6E6E1A930';
	}

	/**
	 * Replaces license key string with special characters to hide it
	 *
	 * @since 0.1.0
	 * @return string
	 */
	private function get_hidden_license_key() {
		$input_string = 'B5E0B5F8DD8689E6ACA49DD6E6E1A930';

		$start = 5;
		$length = mb_strlen( $input_string ) - $start - 5;

		$mask_string = preg_replace( '/\S/', '*', $input_string );
		$mask_string = mb_substr( $mask_string, $start, $length );
		$input_string = substr_replace( $input_string, $mask_string, $start, $length );

		return $input_string;
	}

	/**
	 * Updates the license key option
	 *
	 * @apram 	bool|string   The product license key, or false if not set
	 * @since 	2.1.0
	 */
	public function set_license_key( $license_key ) {
		return update_option( 'elementor_extras_license_key', 'B5E0B5F8DD8689E6ACA49DD6E6E1A930' );
	}

	/**
	 * Retrieves the license data from transient or remotely
	 *
	 * @return 	bool|string   The product license key, or false if not set
	 * @since 	2.1.0
	 */
	public static function get_license_data( $force_request = false ) {
		

		$license_data = [
				'license' 			=> 'active',
				'payment_id' 		=> '10',
				'license_limit' 	=> '100',
				'site_count' 		=> '1',
				'activations_left' 	=> '9999',
				'expires'               => 'lifetime',
				];

		
		return $license_data;
	}

	/**
	 * Updates the license data transient
	 *
	 * @return 	void   The product license key, or false if not set
	 * @since 	2.1.0
	 */
	public static function set_license_data( $license_data, $expiration = null ) {
		if ( null === $expiration ) {
			$expiration = 12 * HOUR_IN_SECONDS;
		}

		set_transient( 'elementor_extras_license_data', $license_data, 'lifetime' );
	}

	/**
	 * Retrieves the remote license status
	 *
	 * @param  	license The license key
	 * @return 	object|bool   The status data
	 * @since 	2.1.0
	 */
	private static function get_remote_license_response( $license, $action = 'check_license' ) {

		// data to send in our API request
		$api_params = array(
			'edd_action' => $action,
			'license'    => $license,
			'item_name'  => urlencode( ELEMENTOR_EXTRAS_SL_ITEM_NAME ), // the name of our product in EDD
			'site_lang'  => get_bloginfo( 'language' ),
			'url'        => home_url(),
		);

		// Call the custom API.
		$response = wp_remote_post( ELEMENTOR_EXTRAS_STORE_URL,
			array(
				'timeout' 	=> 40,
				'sslverify' => false,
				'body' 		=> $api_params
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );

		if ( 200 !== (int) $response_code ) {
			return new \WP_Error( $response_code, __( 'Could not connect to server. Please contact us if this problem persists.', 'elementor-extras' ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $data ) || ! is_array( $data ) ) {
			return new \WP_Error( 'no_json', __( 'An error occurred, please try again', 'elementor-extras' ) );
		}

		return $data;
	}

	/**
	 * Creates the settings items for entering license information (email + license key).
	 *
	 * NOTE:
	 * If you want to move the license settings somewhere else (e.g. your theme / plugin
	 * settings page), we suggest you override this function in a subclass and
	 * initialize the settings fields yourself. Just make sure to use the same
	 * settings fields so that Nmg_License_Manager_Client can still find the settings values.
	 *
	 * @since 0.1.0
	 */
	public function add_license_settings_page() {
		add_submenu_page(
			'elementor_extras_license',
			__( 'Extras License', $this->text_domain ),
			__( 'Extras License', $this->text_domain ),
			'manage_options',
			$this->get_settings_page_slug(),
			[ $this, 'render_licenses_page' ]
		);
	}

	/**
	 * Creates the settings fields needed for the license settings menu.
	 *
	 * @since 0.1.0
	 */
	public function register_license_settings() {
		// creates our settings in the options table
		register_setting( $this->get_settings_page_slug(), 'elementor_extras_license_key', 'sanitize_license' );
	}

	/**
	 * Sanitize License
	 */
	public function sanitize_license( $new ) {
		$old = get_option( 'elementor_extras_license_key' );
		if ( $old && $old != $new ) {
			delete_option( 'elementor_extras_license_status' ); // new license has been entered, so must reactivate
		}
		return $new;
	}

	/**
	 * Renders the settings page for entering license information.
	 *
	 * @since 0.1.0
	 */
	public function render_licenses_page() {

		$license_key 	= self::get_license_key();
		$title 			= sprintf( __( '%s License', $this->text_domain ), $this->product_name );
		$disabled 		= ! empty( $license_key ) ? 'disabled' : '';

		?>
		<div class="wrap">

			<h1><?php echo $title; ?></h1>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">

				<?php settings_fields( $this->get_settings_page_slug() ); ?>

				<?php wp_nonce_field( 'elementor_extras_license_nonce', 'elementor_extras_license_nonce' ); ?>

				<div class="metabox-holder ee-metabox-holder ee-settings">

					<p><?php _e( 'On this page you can add or change your licensing information.', $this->text_domain ); ?></p>

					<table class="form-table ee-form-table ee-form-table--compact">
						<tbody>
							<tr valign="top" class="ee-form-table__row">
								<th scope="row" valign="top" class="ee-form-table__cell ee-form-table__cell--header">
									<label for="elementor_extras_license_key"><?php _e( 'License Key', $this->text_domain ); ?>:</label>
								</th>
								<td class="ee-form-table__cell">
									<input <?php echo $disabled; ?> id="elementor_extras_license_key" name="elementor_extras_license_key" type="text" class="regular-text" value="<?php echo esc_attr( self::get_hidden_license_key() ); ?>" />
								</td>
							</tr><?php

							if ( empty( $license_key ) ) {
							
							?><tr valign="top" class="ee-form-table__row">
								<th scope="row" valign="top" class="ee-form-table__cell ee-form-table__cell--header"></th>
								<td class="ee-form-table__cell">
									<input type="hidden" name="action" value="elementor_extras_license_activate" /><?php 
									submit_button( __( 'Activate', $this->text_domain ), 'button-primary button-large', 'submit', false, array( 'class' => 'button button-primary' ) ); 
								?></td>
							</tr><?php

							} else {

							$license_data = self::get_license_data( true );
							$errors = self::get_status_errors();

							?><tr valign="top" class="ee-form-table__row">
								<th scope="row" valign="top" class="ee-form-table__cell ee-form-table__cell--header"><?php _e( 'Status', 'elementor-extras' ); ?>:</th>
								<td class="ee-form-table__cell ee-form-table__cell--strong">
									<?php if ( self::STATUS_EXPIRED === $license_data['license'] ) : ?>
										<span style="color: #ff0000;"><?php _e( 'Expired', 'elementor-extras' ); ?></span>
									<?php elseif ( self::STATUS_SITE_INACTIVE === $license_data['license'] ) : ?>
										<span style="color: #ff0000;"><?php _e( 'Mismatch', 'elementor-extras' ); ?></span>
									<?php elseif ( self::STATUS_INVALID === $license_data['license'] ) : ?>
										<span style="color: #ff0000;"><?php _e( 'Invalid', 'elementor-extras' ); ?></span>
									<?php elseif ( self::STATUS_DISABLED === $license_data['license'] ) : ?>
										<span style="color: #ff0000;"><?php _e( 'Disabled', 'elementor-extras' ); ?></span>
									<?php else : ?>
										<span style="color: #008000;"><?php _e( 'Active', 'elementor-extras' ); ?></span>
									<?php endif; ?>
								</td>
							</tr><?php

							if ( self::STATUS_EXPIRED === $license_data['license'] ) :
								?><p class="ee-admin-notice ee-admin-notice--danger"><?php
									echo $errors[ self::STATUS_EXPIRED ]['message'] . ' ';
									printf( $errors[ self::STATUS_EXPIRED ]['action'], $errors[ self::STATUS_EXPIRED ]['link'] );
								?></p><?php
							endif;

							if ( self::STATUS_SITE_INACTIVE === $license_data['license'] ) :
								 ?><p class="ee-admin-notice ee-admin-notice--warning"><?php
									echo $errors[ self::STATUS_SITE_INACTIVE ]['message'];
								?></p><?php
							endif;

							if ( self::STATUS_INVALID === $license_data['license'] ) :
								?><p class="ee-admin-notice ee-admin-notice--warning"><?php
									echo $errors[ self::STATUS_INVALID ]['message'];
								?></p><?php
							endif;

							 ?><tr valign="top" class="ee-form-table__row">
								<th scope="row" valign="top" class="ee-form-table__cell ee-form-table__cell--header"></th>
								<td class="ee-form-table__cell">
									<input type="hidden" name="action" value="elementor_extras_license_deactivate" /><?php 
									submit_button( __( 'Deactivate', $this->text_domain ), 'button-primary button-large', 'submit', false, array( 'class' => 'button button-primary' ) ); ?>
								</td>
							</tr>

							<?php } ?>
						</tbody>
					</table>
				</div>

			</form>
		</div><?php
	}

	/**
	 * Renders the description for the settings section.
	 *
	 * @since 0.1.0
	 */
	public function render_settings_section() {
		printf( __( 'Insert your %s license information to enable future updates (including bug fixes and new features) and gain access to support.', $this->text_domain ), $this->product_name );
	}

	/**
	 * Renders the license key settings field on the license settings page.
	 *
	 * @since 0.1.0
	 */
	public function render_license_key_settings_field() {
		$settings_field_name = $this->get_settings_field_name();
		$options = get_option( $settings_field_name );
		?>
		<input type='text' name='<?php echo $settings_field_name; ?>[license_key]' value='<?php echo $options['license_key']; ?>' class='regular-text' /><?php
	}

	/**
	 * @return 	string   The slug id of the licenses settings page.
	 * @since 	0.1.0
	 */
	protected function get_settings_page_slug() {
		return 'elementor_extras_license';
	}

	/**
	 * @return 	string   The name of the settings field storing all license manager settings.
	 * @since 	0.1.0
	 */
	protected function get_settings_field_name() {
		return 'elementor_extras-license-settings';
	}

	/**
	 * Validates the license and saves the license key in the database
	 *
	 * @return object|bool   The product data, or false if API call fails.
	 * @since 0.1.0
	 */
	public function activate_license() {

		// run a quick security check
	 	if( ! check_admin_referer( 'elementor_extras_license_nonce', 'elementor_extras_license_nonce' ) ) {
			return; // get out if we didn't click the Activate button
	 	}

	 	// Check if license key field is set
		

		// retrieve the license from the database
		$license_key = 'B5E0B5F8DD8689E6ACA49DD6E6E1A930';

		// Get the remote response
		$data = self::get_remote_license_response( $license_key, 'activate_license' );
		
		$data = [
				'license' 			=> 'active',
				'payment_id' 		=> '10',
				'license_limit' 	=> '100',
				'site_count' 		=> '1',
				'activations_left' 	=> '9999',
				'expires'               => 'lifetime',
				];
		
		// Make sure the response came back okay
		

		$this->set_license_key( $license_key );
		self::set_license_data( $data );

		wp_safe_redirect( $_POST['_wp_http_referer'] );
		die;
	}


	/**
	 * Remove the license validation
	 *
	 * @return object|bool   The product data, or false if API call fails.
	 * @since 0.1.0
	 */
	public function deactivate_license() {

		// run a quick security check
	 	if( ! check_admin_referer( 'elementor_extras_license_nonce', 'elementor_extras_license_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license_key = self::get_license_key();

		// Get the remote response
		$data = self::get_remote_license_response( $license_key, 'deactivate_license' );

		delete_option( 'elementor_extras_license_key' );
		delete_option( 'elementor_extras_license_status' );
		delete_transient( 'elementor_extras_license_data' );

		wp_safe_redirect( $_POST['_wp_http_referer'] );
		die;
	}

	/**
	 * Template for printing an admin notice
	 * 
	 * @since 2.1.0
	 */
	private function print_admin_notice( $title, $description, $action = null, $link = null, $dismissable = false ) {

		if ( null !== $action ) {
			$action = ( null !== $link ) ? sprintf( $action, $link ) : $action;
		}

		$classes = [
			'ee-admin-notice',
			'notice',
			'notice-error',
		];

		$data = '';

		if ( $dismissable && is_array( $dismissable ) ) {
			$dismissable_key = 'ee-' . $dismissable['key'] . '-notice-' . $dismissable['duration'];

			if ( ! \ElementorExtras\Dismiss_Notice::is_admin_notice_active( $dismissable_key ) )
	        	return;

			$classes[] = 'is-dismissible';
			$data = 'data-dismissible="' . $dismissable_key . '"';
		}

		$classes = implode( ' ', $classes );

		?><div class="<?php echo $classes; ?>" <?php echo $data; ?>>
			<h3><?php printf( __( 'Extras for Elementor: %s' ), $title ); ?></h3>
			<p><?php
				echo $description;
				if ( null !== $action )	echo ' ' . $action;
			?></p>
		</div><?php
	}

	/**
	 * Checks if current screen is block editor
	 * 
	 * @since 2.1.0
	 */
	private function is_block_editor_page() {
		$current_screen = get_current_screen();

		if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			return true;
		}

		if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
			return true;
		}

		return false;
	}

	/**
	 * Handles admin notices for errors and license activation
	 * 
	 * @since 0.1.0
	 */
	public function admin_notices() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( $this->is_block_editor_page() ) {
			return;
		}

		$license_key = self::get_license_key();

		if ( empty( $license_key ) ) {
			
			$msg = __( 'Please %1$sactivate your license%2$s key to enable updates for %3$s.', $this->text_domain );
			$msg = sprintf( $msg, '<a href="' . admin_url( 'admin.php?page=' . $this->get_settings_page_slug() ) . '">', '</a>', '<strong>' . $this->product_name . '</strong>' );
			
			?><div class="notice notice-error">
				<p><?php echo $msg; ?></p>
			</div><?php
			return;
		}

		$license_data = self::get_license_data();

		if ( empty( $license_data['license'] ) ) {
			return;
		}

		$errors = self::get_status_errors();

		if ( isset( $errors[ $license_data['license'] ] ) ) {

			$error_data = $errors[ $license_data['license'] ];

			if ( array_key_exists( 'dismissable', $error_data ) && false !== $error_data['dismissable'] ) {
				$dismissable = $error_data['dismissable'];
			} else { $dismissable = false; }

			$this->print_admin_notice( $error_data['title'], $error_data['message'], $error_data['action'], $error_data['link'], $dismissable );
			return;
		}

		if ( self::STATUS_VALID === $license_data['license'] ) {
			if ( ! empty( $license_data['subscriptions'] ) && 'enable' === $license_data['subscriptions'] ) {
				return;
			}

			if ( 'lifetime' === $license_data['expires'] ) {
				return;
			}

			$expiry_time = strtotime( $license_data['expires'] );
			$until_expiry_time = strtotime( '-28 days', $expiry_time );

			if ( $until_expiry_time <= current_time( 'timestamp' ) ) {

				$title = sprintf( __( 'License expires in %s.', 'elementor-extras' ), human_time_diff( current_time( 'timestamp' ), $expiry_time ) );
				$message = sprintf( __( '<a href="%s" target="_blank">Renew your license</a> with a <strong>30&#37; discount</strong> before it expires. Continue getting updates and keep your access to premium support.', 'elementor-extras' ), $errors[ self::STATUS_EXPIRED ][ 'link' ] );

				$this->print_admin_notice( $title, $message, null, null, [
					'key' 		=> 'license-expiring',
					'duration' 	=> 10,
				] );
			}
		}
	}

}