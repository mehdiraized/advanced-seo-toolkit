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
		$suggestions = [];

		// Analyze keyword density
		$keyword_density = $this->calculate_keyword_density( $content );
		if ( $keyword_density < 1 ) {
			$suggestions[] = "Increase keyword density.";
		}

		// Check for meta description
		if ( ! $this->has_meta_description( $content ) ) {
			$suggestions[] = "Add a meta description.";
		}

		// Check for internal linking
		if ( ! $this->has_internal_links( $content ) ) {
			$suggestions[] = "Add internal links.";
		}

		// New checks
		if ( ! $this->has_outbound_links( $content ) ) {
			$suggestions[] = "Consider adding outbound links to authoritative sources.";
		}

		if ( ! $this->has_images( $content ) ) {
			$suggestions[] = "Add images to make your content more engaging.";
		}

		$readability_score = $this->calculate_readability_score( $content );
		if ( $readability_score < 60 ) {
			$suggestions[] = "Improve the readability of your content.";
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
		wp_enqueue_script(
			'ast-content-analysis',
			AST_PLUGIN_URL . 'assets/js/content-analysis.js',
			array( 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data' ),
			AST_VERSION,
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

		$content = isset( $_POST['content'] ) ? sanitize_text_field( wp_unslash( $_POST['content'] ) ) : '';
		$suggestions = $this->analyze_content( $content );

		wp_send_json_success( $suggestions );
	}
}