<?php
/**
 * Handle the creation and saving of meta boxes.
 */
class AST_Meta_Boxes {
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
    }

    public function add_meta_boxes() {
        add_meta_box(
            'ast_seo_meta_box',
            __('SEO Settings', 'advanced-seo-toolkit'),
            array($this, 'render_meta_box'),
            array('post', 'page', 'product'),
            'normal',
            'high'
        );
    }

    public function render_meta_box($post) {
        wp_nonce_field('ast_seo_meta_box', 'ast_seo_meta_box_nonce');

        $title = get_post_meta($post->ID, '_ast_meta_title', true);
        $description = get_post_meta($post->ID, '_ast_meta_description', true);
        $keywords = get_post_meta($post->ID, '_ast_meta_keywords', true);

        echo '<p>';
        echo '<label for="ast_meta_title">' . __('Meta Title', 'advanced-seo-toolkit') . '</label><br>';
        echo '<input type="text" id="ast_meta_title" name="ast_meta_title" value="' . esc_attr($title) . '" size="50">';
        echo '</p>';

        echo '<p>';
        echo '<label for="ast_meta_description">' . __('Meta Description', 'advanced-seo-toolkit') . '</label><br>';
        echo '<textarea id="ast_meta_description" name="ast_meta_description" rows="4" cols="50">' . esc_textarea($description) . '</textarea>';
        echo '</p>';

        echo '<p>';
        echo '<label for="ast_meta_keywords">' . __('Meta Keywords', 'advanced-seo-toolkit') . '</label><br>';
        echo '<input type="text" id="ast_meta_keywords" name="ast_meta_keywords" value="' . esc_attr($keywords) . '" size="50">';
        echo '</p>';
    }

    public function save_meta_boxes($post_id) {
        if (!isset($_POST['ast_seo_meta_box_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['ast_seo_meta_box_nonce'], 'ast_seo_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (isset($_POST['ast_meta_title'])) {
            update_post_meta($post_id, '_ast_meta_title', sanitize_text_field($_POST['ast_meta_title']));
        }

        if (isset($_POST['ast_meta_description'])) {
            update_post_meta($post_id, '_ast_meta_description', sanitize_textarea_field($_POST['ast_meta_description']));
        }

        if (isset($_POST['ast_meta_keywords'])) {
            update_post_meta($post_id, '_ast_meta_keywords', sanitize_text_field($_POST['ast_meta_keywords']));
        }
    }
}