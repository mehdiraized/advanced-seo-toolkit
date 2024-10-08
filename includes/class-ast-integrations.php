<?php
/**
 * Handle integrations with third-party services.
 *
 * This class integrates with external services including Ahrefs and Google Analytics.
 * Please refer to the plugin's README file for more information on these integrations.
 */
class AST_Integrations {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_integrations_menu' ) );
		add_action( 'admin_init', array( $this, 'register_integration_settings' ) );
	}

	public function add_integrations_menu() {
		add_submenu_page(
			'advanced-seo-toolkit', // Parent slug
			__( 'Integrations', 'advanced-seo-toolkit' ),
			__( 'Integrations', 'advanced-seo-toolkit' ),
			'manage_options',
			'ast-integrations',
			array( $this, 'render_integrations_page' )
		);
	}

	public function register_integration_settings() {
		register_setting( 'ast_integrations', 'ast_ahrefs_api_key' );
		register_setting( 'ast_integrations', 'ast_google_analytics_id' );
	}

	public function render_integrations_page() {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'ast_integrations' );
				do_settings_sections( 'ast_integrations' );
				?>
				<table class="form-table">
					<tr>
						<th scope="row"><label
								for="ast_ahrefs_api_key"><?php _e( 'Ahrefs API Key', 'advanced-seo-toolkit' ); ?></label></th>
						<td><input type="text" id="ast_ahrefs_api_key" name="ast_ahrefs_api_key"
								value="<?php echo esc_attr( get_option( 'ast_ahrefs_api_key' ) ); ?>" class="regular-text"></td>
					</tr>
					<tr>
						<th scope="row"><label
								for="ast_google_analytics_id"><?php _e( 'Google Analytics ID', 'advanced-seo-toolkit' ); ?></label>
						</th>
						<td><input type="text" id="ast_google_analytics_id" name="ast_google_analytics_id"
								value="<?php echo esc_attr( get_option( 'ast_google_analytics_id' ) ); ?>" class="regular-text">
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Fetch data from Ahrefs API.
	 *
	 * This method makes a request to the Ahrefs API, which is a third-party service.
	 * Users should be aware that data will be sent to and received from Ahrefs servers.
	 * For more information, see the Ahrefs API documentation and terms of service.
	 *
	 * @param string $url The URL to fetch data for.
	 * @return array|false The API response data, or false on failure.
	 */
	public function get_ahrefs_data( $url ) {
		$api_key = get_option( 'ast_ahrefs_api_key' );
		if ( ! $api_key ) {
			return false;
		}

		$response = wp_remote_get( "https://api.ahrefs.com/v1/url-metrics?url={$url}&apikey={$api_key}" );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		return json_decode( $body, true );
	}
}