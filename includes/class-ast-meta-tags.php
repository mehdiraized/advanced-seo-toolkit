<?php
/**
 * Handle the creation and output of meta tags.
 */
class AST_Meta_Tags {
	public function __construct() {
		add_action( 'wp_head', array( $this, 'output_meta_tags' ) );
	}

	public function output_meta_tags() {
		global $post;

		if ( is_single() || is_page() ) {
			$title = get_post_meta( $post->ID, '_ast_meta_title', true );
			$description = get_post_meta( $post->ID, '_ast_meta_description', true );
			$keywords = get_post_meta( $post->ID, '_ast_meta_keywords', true );

			if ( ! $title ) {
				$title = get_the_title() . ' | ' . get_bloginfo( 'name' );
			}
			if ( ! $description ) {
				$description = wp_trim_words( $post->post_content, 20 );
			}

			echo "<title>" . esc_html( $title ) . "</title>\n";
			echo "<meta name='description' content='" . esc_attr( $description ) . "'>\n";
			if ( $keywords ) {
				echo "<meta name='keywords' content='" . esc_attr( $keywords ) . "'>\n";
			}

			// Open Graph tags
			echo "<meta property='og:title' content='" . esc_attr( $title ) . "'>\n";
			echo "<meta property='og:description' content='" . esc_attr( $description ) . "'>\n";
			echo "<meta property='og:type' content='article'>\n";
			echo "<meta property='og:url' content='" . esc_url( get_permalink() ) . "'>\n";

			// Twitter Card tags
			echo "<meta name='twitter:card' content='summary'>\n";
			echo "<meta name='twitter:title' content='" . esc_attr( $title ) . "'>\n";
			echo "<meta name='twitter:description' content='" . esc_attr( $description ) . "'>\n";
		}
	}
}