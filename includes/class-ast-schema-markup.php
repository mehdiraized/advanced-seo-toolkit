<?php
/**
 * Handle the creation and output of schema markup.
 */
class AST_Schema_Markup {
    public function __construct() {
        add_action('wp_footer', array($this, 'output_schema_markup'));
    }

    public function output_schema_markup() {
        if (is_single() || is_page()) {
            $schema = $this->generate_schema_markup();
            if ($schema) {
                echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>';
            }
        }
    }

    private function generate_schema_markup() {
        global $post;

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => get_the_title(),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author()
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_site_icon_url()
                )
            )
        );

        // Add featured image if available
        if (has_post_thumbnail()) {
            $schema['image'] = array(
                '@type' => 'ImageObject',
                'url' => get_the_post_thumbnail_url($post, 'full')
            );
        }

        // Add description if available
        $description = get_post_meta($post->ID, '_ast_meta_description', true);
        if ($description) {
            $schema['description'] = $description;
        }

        return $schema;
    }

    public function generate_product_schema($product_id) {
        // This is a placeholder for WooCommerce integration
        // You would need to implement the actual product schema generation here
        $product = wc_get_product($product_id);

        if (!$product) {
            return false;
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->get_name(),
            'description' => $product->get_short_description(),
            'sku' => $product->get_sku(),
            'offers' => array(
                '@type' => 'Offer',
                'price' => $product->get_price(),
                'priceCurrency' => get_woocommerce_currency(),
                'availability' => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'url' => get_permalink($product_id)
            )
        );

        return $schema;
    }
}