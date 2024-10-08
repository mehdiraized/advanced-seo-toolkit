<?php
/**
 * Handle the creation and output of schema markup.
 */
class AST_Schema_Markup {
	public function __construct() {
		add_action( 'wp_footer', array( $this, 'output_schema_markup' ) );
	}

	public function output_schema_markup() {
		if ( is_single() || is_page() ) {
			$schema = $this->generate_schema_markup();
			if ( $schema ) {
				echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>';
			}
		}
	}

	private function generate_schema_markup() {
		if ( is_single() || is_page() ) {
			$schema = $this->generate_article_schema();
		} elseif ( is_author() ) {
			$schema = $this->generate_author_schema();
		} elseif ( is_archive() ) {
			$schema = $this->generate_archive_schema();
		} else {
			$schema = $this->generate_website_schema();
		}

		return $schema;
	}

	public function generate_product_schema( $product_id ) {
		// This is a placeholder for WooCommerce integration
		// You would need to implement the actual product schema generation here
		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			return false;
		}

		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'Product',
			'name' => wp_strip_all_tags( $product->get_name() ),
			'description' => wp_strip_all_tags( $product->get_short_description() ),
			'sku' => $product->get_sku(),
			'offers' => array(
				'@type' => 'Offer',
				'price' => $product->get_price(),
				'priceCurrency' => get_woocommerce_currency(),
				'availability' => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
				'url' => esc_url( get_permalink( $product_id ) )
			)
		);

		return $schema;
	}

	private function generate_article_schema() {
		global $post;

		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'Article',
			'headline' => wp_strip_all_tags( get_the_title() ),
			'datePublished' => get_the_date( 'c' ),
			'dateModified' => get_the_modified_date( 'c' ),
			'author' => array(
				'@type' => 'Person',
				'name' => get_the_author()
			),
			'publisher' => array(
				'@type' => 'Organization',
				'name' => get_bloginfo( 'name' ),
				'logo' => array(
					'@type' => 'ImageObject',
					'url' => esc_url( get_site_icon_url() )
				)
			)
		);

		if ( has_post_thumbnail() ) {
			$schema['image'] = array(
				'@type' => 'ImageObject',
				'url' => esc_url( get_the_post_thumbnail_url( $post, 'full' ) )
			);
		}

		$description = get_post_meta( $post->ID, '_ast_meta_description', true );
		if ( $description ) {
			$schema['description'] = wp_strip_all_tags( $description );
		}

		return $schema;
	}

	private function generate_author_schema() {
		$author_id = get_queried_object_id();
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'Person',
			'name' => wp_strip_all_tags( get_the_author_meta( 'display_name', $author_id ) ),
			'description' => wp_strip_all_tags( get_the_author_meta( 'description', $author_id ) ),
			'url' => esc_url( get_author_posts_url( $author_id ) )
		);

		return $schema;
	}

	private function generate_archive_schema() {
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'CollectionPage',
			'headline' => wp_strip_all_tags( get_the_archive_title() ),
			'description' => wp_strip_all_tags( get_the_archive_description() ),
			'url' => esc_url( get_permalink() )
		);

		return $schema;
	}

	private function generate_website_schema() {
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'WebSite',
			'name' => wp_strip_all_tags( get_bloginfo( 'name' ) ),
			'description' => wp_strip_all_tags( get_bloginfo( 'description' ) ),
			'url' => esc_url( get_site_url() )
		);

		return $schema;
	}
}