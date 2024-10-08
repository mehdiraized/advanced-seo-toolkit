<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Advanced_SEO_Toolkit
 * @subpackage Advanced_SEO_Toolkit/admin/partials
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// $settings is now passed from the display_plugin_setup_page method

// Enqueue admin scripts
function ast_enqueue_admin_scripts( $hook ) {
	if ( 'toplevel_page_advanced-seo-toolkit' !== $hook ) {
		return;
	}

	wp_enqueue_script(
		'ast-admin-script',
		plugin_dir_url( __FILE__ ) . '../js/admin-script.js',
		array( 'jquery' ),
		'1.0.0',
		true
	);

	wp_localize_script( 'ast-admin-script', 'astAdminData', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'ast_run_analysis' ),
		'runningAnalysisText' => esc_html__( 'Running Analysis...', 'advanced-seo-toolkit' ),
		'runAnalysisText' => esc_html__( 'Run Analysis', 'advanced-seo-toolkit' ),
		'errorText' => esc_html__( 'An error occurred while running the analysis.', 'advanced-seo-toolkit' )
	) );
}
add_action( 'admin_enqueue_scripts', 'ast_enqueue_admin_scripts' );

?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<form method="post" action="options.php">
		<?php settings_fields( 'ast_general_settings' ); ?>
		<?php do_settings_sections( 'ast_general_settings' ); ?>
		<?php wp_nonce_field( 'ast_save_settings', 'ast_settings_nonce' ); ?>

		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Default Meta Title', 'advanced-seo-toolkit' ); ?></th>
				<td>
					<input type="text" name="ast_default_meta_title"
						value="<?php echo esc_attr( $settings['default_meta_title'] ); ?>" class="regular-text" />
					<p class="description">
						<?php esc_html_e( 'Enter the default meta title. Use {site_title} for the site title and {page_title} for the page title.', 'advanced-seo-toolkit' ); ?>
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Default Meta Description', 'advanced-seo-toolkit' ); ?></th>
				<td>
					<textarea name="ast_default_meta_description" rows="3" cols="50"
						class="large-text"><?php echo esc_textarea( $settings['default_meta_description'] ); ?></textarea>
					<p class="description">
						<?php esc_html_e( 'Enter the default meta description. Use {site_description} for the site description.', 'advanced-seo-toolkit' ); ?>
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Enable Schema Markup', 'advanced-seo-toolkit' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="ast_enable_schema_markup" value="1" <?php checked( $settings['enable_schema_markup'], '1' ); ?> />
						<?php esc_html_e( 'Enable automatic schema markup generation', 'advanced-seo-toolkit' ); ?>
					</label>
				</td>
			</tr>
		</table>

		<?php submit_button( esc_html__( 'Save Settings', 'advanced-seo-toolkit' ) ); ?>
	</form>

	<hr>

	<h2><?php esc_html_e( 'SEO Analysis', 'advanced-seo-toolkit' ); ?></h2>
	<p><?php esc_html_e( 'Click the button below to run an SEO analysis of your site.', 'advanced-seo-toolkit' ); ?></p>
	<button id="ast-run-analysis"
		class="button button-primary"><?php esc_html_e( 'Run Analysis', 'advanced-seo-toolkit' ); ?></button>
	<div id="ast-analysis-results"></div>

	<hr>

	<h2><?php esc_html_e( 'Integrations', 'advanced-seo-toolkit' ); ?></h2>
	<p><?php esc_html_e( 'Configure integrations with third-party services.', 'advanced-seo-toolkit' ); ?></p>
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=ast-integrations' ) ); ?>"
		class="button"><?php esc_html_e( 'Manage Integrations', 'advanced-seo-toolkit' ); ?></a>
</div>