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
if (!defined('WPINC')) {
    die;
}

// $settings is now passed from the display_plugin_setup_page method
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <form method="post" action="options.php">
        <?php settings_fields('ast_general_settings'); ?>
        <?php do_settings_sections('ast_general_settings'); ?>
        <?php wp_nonce_field('ast_save_settings', 'ast_settings_nonce'); ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Default Meta Title', 'advanced-seo-toolkit'); ?></th>
                <td>
                    <input type="text" name="ast_default_meta_title" value="<?php echo esc_attr($settings['default_meta_title']); ?>" class="regular-text" />
                    <p class="description"><?php _e('Enter the default meta title. Use {site_title} for the site title and {page_title} for the page title.', 'advanced-seo-toolkit'); ?></p>
                </td>
            </tr>
            
            <tr valign="top">
                <th scope="row"><?php _e('Default Meta Description', 'advanced-seo-toolkit'); ?></th>
                <td>
                    <textarea name="ast_default_meta_description" rows="3" cols="50" class="large-text"><?php echo esc_textarea($settings['default_meta_description']); ?></textarea>
                    <p class="description"><?php _e('Enter the default meta description. Use {site_description} for the site description.', 'advanced-seo-toolkit'); ?></p>
                </td>
            </tr>
            
            <tr valign="top">
                <th scope="row"><?php _e('Enable Schema Markup', 'advanced-seo-toolkit'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="ast_enable_schema_markup" value="1" <?php checked($settings['enable_schema_markup'], '1'); ?> />
                        <?php _e('Enable automatic schema markup generation', 'advanced-seo-toolkit'); ?>
                    </label>
                </td>
            </tr>
        </table>
        
        <?php submit_button(__('Save Settings', 'advanced-seo-toolkit')); ?>
    </form>

    <hr>

    <h2><?php _e('SEO Analysis', 'advanced-seo-toolkit'); ?></h2>
    <p><?php _e('Click the button below to run an SEO analysis of your site.', 'advanced-seo-toolkit'); ?></p>
    <button id="ast-run-analysis" class="button button-primary"><?php _e('Run Analysis', 'advanced-seo-toolkit'); ?></button>
    <div id="ast-analysis-results"></div>

    <hr>

    <h2><?php _e('Integrations', 'advanced-seo-toolkit'); ?></h2>
    <p><?php _e('Configure integrations with third-party services.', 'advanced-seo-toolkit'); ?></p>
    <a href="<?php echo admin_url('admin.php?page=ast-integrations'); ?>" class="button"><?php _e('Manage Integrations', 'advanced-seo-toolkit'); ?></a>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#ast-run-analysis').on('click', function() {
        var $button = $(this);
        var $results = $('#ast-analysis-results');

        $button.prop('disabled', true).text('<?php _e('Running Analysis...', 'advanced-seo-toolkit'); ?>');
        $results.html('');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'ast_run_analysis',
                nonce: '<?php echo wp_create_nonce('ast_run_analysis'); ?>'
            },
            success: function(response) {
                $results.html(response);
            },
            error: function() {
                $results.html('<p class="error"><?php _e('An error occurred while running the analysis.', 'advanced-seo-toolkit'); ?></p>');
            },
            complete: function() {
                $button.prop('disabled', false).text('<?php _e('Run Analysis', 'advanced-seo-toolkit'); ?>');
            }
        });
    });
});
</script>