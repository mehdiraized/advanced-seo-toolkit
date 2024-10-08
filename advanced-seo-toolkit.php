<?php
/**
 * Plugin Name: Advanced SEO Toolkit
 * Plugin URI: https://github.com/mehdiraized/advanced-seo-toolkit
 * Description: A comprehensive SEO plugin with meta tags, custom language support, schema markup, and third-party integrations.
 * Version: 1.1.2
 * Author: Mehdi Rezaei
 * Author URI: https://mehd.ir
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: advanced-seo-toolkit
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Advanced_SEO_Toolkit {
	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->define_constants();
		$this->load_dependencies();
		$this->set_locale();
		$this->init_hooks();
	}

	private function define_constants() {
		define( 'AST_VERSION', '1.1.2' );
		define( 'AST_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		define( 'AST_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	}

	private function load_dependencies() {
		require_once AST_PLUGIN_DIR . 'admin/class-ast-admin.php';
		require_once AST_PLUGIN_DIR . 'includes/class-ast-integrations.php';
		require_once AST_PLUGIN_DIR . 'includes/class-ast-loader.php';
		require_once AST_PLUGIN_DIR . 'includes/class-ast-i18n.php';
		require_once AST_PLUGIN_DIR . 'includes/class-ast-meta-tags.php';
		require_once AST_PLUGIN_DIR . 'includes/class-ast-meta-boxes.php';
		require_once AST_PLUGIN_DIR . 'includes/class-ast-schema-markup.php';
		require_once AST_PLUGIN_DIR . 'includes/class-ast-content-analysis.php';
		require_once AST_PLUGIN_DIR . 'includes/class-ast-sitemap-generator.php';
		require_once AST_PLUGIN_DIR . 'includes/class-ast-robots-txt.php';
		require_once AST_PLUGIN_DIR . 'includes/class-ast-social-preview.php';
	}

	private function set_locale() {
		$plugin_i18n = new AST_I18n();
		add_action( 'plugins_loaded', array( $plugin_i18n, 'load_plugin_textdomain' ) );
	}

	private function init_hooks() {
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {
		if ( is_admin() ) {
			new AST_Admin();
		}
		new AST_Integrations();
		new AST_Meta_Tags();
		new AST_Meta_Boxes();
		new AST_Schema_Markup();
		new AST_Content_Analysis();
		new AST_Sitemap_Generator();
		new AST_Robots_Txt();
		new AST_Social_Preview();
	}
}

function run_advanced_seo_toolkit() {
	return Advanced_SEO_Toolkit::get_instance();
}

run_advanced_seo_toolkit();

