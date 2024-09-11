<?php
/**
 * The admin-specific functionality of the plugin.
 */
class AST_Admin {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_plugin_admin_menu() {
        add_menu_page(
            __('Advanced SEO Toolkit', 'advanced-seo-toolkit'),
            __('Advanced SEO', 'advanced-seo-toolkit'),
            'manage_options',
            'advanced-seo-toolkit',
            array($this, 'display_plugin_setup_page'),
            'dashicons-chart-line',
            100
        );
    }

    public function display_plugin_setup_page() {
        // Ensure settings are loaded before including the display file
        $settings = $this->get_plugin_settings();
        include_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/advanced-seo-toolkit-admin-display.php';
    }

    public function register_settings() {
        register_setting('ast_general_settings', 'ast_default_meta_title');
        register_setting('ast_general_settings', 'ast_default_meta_description');
        register_setting('ast_general_settings', 'ast_enable_schema_markup');
    }

    public function get_plugin_settings() {
        $settings = array(
            'default_meta_title' => get_option('ast_default_meta_title', '{page_title} | {site_title}'),
            'default_meta_description' => get_option('ast_default_meta_description', '{site_description}'),
            'enable_schema_markup' => get_option('ast_enable_schema_markup', '1'),
        );

        return $settings;
    }

    public function save_plugin_settings() {
        if (isset($_POST['ast_settings_nonce']) && wp_verify_nonce($_POST['ast_settings_nonce'], 'ast_save_settings')) {
            update_option('ast_default_meta_title', sanitize_text_field($_POST['ast_default_meta_title']));
            update_option('ast_default_meta_description', sanitize_textarea_field($_POST['ast_default_meta_description']));
            update_option('ast_enable_schema_markup', isset($_POST['ast_enable_schema_markup']) ? '1' : '0');

            add_settings_error('ast_messages', 'ast_message', __('Settings Saved', 'advanced-seo-toolkit'), 'updated');
        }
    }
}