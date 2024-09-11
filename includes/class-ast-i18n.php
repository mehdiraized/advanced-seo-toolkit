<?php
/**
 * Define the internationalization functionality.
 */
class AST_I18n {
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'advanced-seo-toolkit',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}