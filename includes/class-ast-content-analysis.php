<?php
/**
 * Handle content analysis and suggestions.
 */
class AST_Content_Analysis {
	public function __construct() {
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
		add_action( 'wp_ajax_ast_analyze_content', array( $this, 'ajax_analyze_content' ) );
	}

	public function analyze_content( $content ) {
		$suggestions = array();

		// Analyze keyword density
		$keyword_density = $this->calculate_keyword_density( $content );
		if ( $keyword_density < 1 ) {
			$suggestions[] = esc_html__( "Increase keyword density.", 'advanced-seo-toolkit' );
		}

		// Check for meta description
		if ( ! $this->has_meta_description( $content ) ) {
			$suggestions[] = esc_html__( "Add a meta description.", 'advanced-seo-toolkit' );
		}

		// Check for internal linking
		if ( ! $this->has_internal_links( $content ) ) {
			$suggestions[] = esc_html__( "Add internal links.", 'advanced-seo-toolkit' );
		}

		// New checks
		if ( ! $this->has_outbound_links( $content ) ) {
			$suggestions[] = esc_html__( "Consider adding outbound links to authoritative sources.", 'advanced-seo-toolkit' );
		}

		if ( ! $this->has_images( $content ) ) {
			$suggestions[] = esc_html__( "Add images to make your content more engaging.", 'advanced-seo-toolkit' );
		}

		$readability_score = $this->calculate_readability_score( $content );
		if ( $readability_score < 60 ) {
			$suggestions[] = esc_html__( "Improve the readability of your content.", 'advanced-seo-toolkit' );
		}

		return $suggestions;
	}

	private function has_outbound_links( $content ) {
		// Check for outbound links
		return preg_match( '/<a\s[^>]*href=["\'](https?:\/\/(?!' . preg_quote( get_site_url(), '/' ) . '))[^\'"]+["\'][^>]*>/i', $content );
	}

	private function has_images( $content ) {
		// Check for image tags
		return strpos( $content, '<img' ) !== false;
	}

	private function calculate_readability_score( $content ) {
		// Implement readability calculation (e.g., Flesch-Kincaid)
		// This is a placeholder implementation
		return strlen( $content ) > 1000 ? 70 : 50;
	}

	private function calculate_keyword_density( $content ) {
		// Dummy function for keyword density calculation
		return 0.5;
	}

	private function has_meta_description( $content ) {
		// Dummy function to check for meta description
		return false;
	}

	private function has_internal_links( $content ) {
		// Dummy function to check for internal links
		return false;
	}

	public function enqueue_block_editor_assets() {
		$script_asset_path = AST_PLUGIN_DIR . 'assets/js/content-analysis.asset.php';
		$script_asset = file_exists( $script_asset_path )
			? require( $script_asset_path )
			: array( 'dependencies' => array(), 'version' => filemtime( AST_PLUGIN_DIR . 'assets/js/content-analysis.js' ) );

		wp_enqueue_script(
			'ast-content-analysis',
			AST_PLUGIN_URL . 'assets/js/content-analysis.js',
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		wp_localize_script(
			'ast-content-analysis',
			'ASTContentAnalysis',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'ast_content_analysis_nonce' )
			)
		);
	}

	public function ajax_analyze_content() {
		check_ajax_referer( 'ast_content_analysis_nonce', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( 'Insufficient permissions' );
			return;
		}

		$content = isset( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '';
		$suggestions = $this->analyze_content( $content );

		wp_send_json_success( $suggestions );
	}
}