<?php
class AST_Social_Preview {
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_social_preview_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_social_preview_data' ) );
		add_action( 'wp_head', array( $this, 'output_social_meta_tags' ) );
	}

	public function add_social_preview_meta_box() {
		add_meta_box(
			'ast_social_preview',
			__( 'Social Media Preview', 'advanced-seo-toolkit' ),
			array( $this, 'render_social_preview_meta_box' ),
			array( 'post', 'page' ),
			'normal',
			'high'
		);
	}

	public function render_social_preview_meta_box( $post ) {
		wp_nonce_field( 'ast_social_preview_nonce', 'ast_social_preview_nonce' );

		$og_title = get_post_meta( $post->ID, '_ast_og_title', true );
		$og_description = get_post_meta( $post->ID, '_ast_og_description', true );
		$og_image = get_post_meta( $post->ID, '_ast_og_image', true );
		$twitter_title = get_post_meta( $post->ID, '_ast_twitter_title', true );
		$twitter_description = get_post_meta( $post->ID, '_ast_twitter_description', true );
		$twitter_image = get_post_meta( $post->ID, '_ast_twitter_image', true );

		?>
		<h3><?php _e( 'Facebook / Open Graph', 'advanced-seo-toolkit' ); ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="ast_og_title"><?php _e( 'OG Title', 'advanced-seo-toolkit' ); ?></label></th>
				<td><input type="text" id="ast_og_title" name="ast_og_title" value="<?php echo esc_attr( $og_title ); ?>"
						class="large-text" /></td>
			</tr>
			<tr>
				<th><label for="ast_og_description"><?php _e( 'OG Description', 'advanced-seo-toolkit' ); ?></label></th>
				<td><textarea id="ast_og_description" name="ast_og_description" rows="3"
						class="large-text"><?php echo esc_textarea( $og_description ); ?></textarea></td>
			</tr>
			<tr>
				<th><label for="ast_og_image"><?php _e( 'OG Image', 'advanced-seo-toolkit' ); ?></label></th>
				<td>
					<input type="text" id="ast_og_image" name="ast_og_image" value="<?php echo esc_url( $og_image ); ?>"
						class="large-text" />
					<input type="button" class="button button-secondary"
						value="<?php _e( 'Upload Image', 'advanced-seo-toolkit' ); ?>" id="ast_og_image_button" />
				</td>
			</tr>
		</table>

		<h3><?php _e( 'Twitter Card', 'advanced-seo-toolkit' ); ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="ast_twitter_title"><?php _e( 'Twitter Title', 'advanced-seo-toolkit' ); ?></label></th>
				<td><input type="text" id="ast_twitter_title" name="ast_twitter_title"
						value="<?php echo esc_attr( $twitter_title ); ?>" class="large-text" /></td>
			</tr>
			<tr>
				<th><label for="ast_twitter_description"><?php _e( 'Twitter Description', 'advanced-seo-toolkit' ); ?></label></th>
				<td><textarea id="ast_twitter_description" name="ast_twitter_description" rows="3"
						class="large-text"><?php echo esc_textarea( $twitter_description ); ?></textarea></td>
			</tr>
			<tr>
				<th><label for="ast_twitter_image"><?php _e( 'Twitter Image', 'advanced-seo-toolkit' ); ?></label></th>
				<td>
					<input type="text" id="ast_twitter_image" name="ast_twitter_image"
						value="<?php echo esc_url( $twitter_image ); ?>" class="large-text" />
					<input type="button" class="button button-secondary"
						value="<?php _e( 'Upload Image', 'advanced-seo-toolkit' ); ?>" id="ast_twitter_image_button" />
				</td>
			</tr>
		</table>

		<script>
			jQuery(document).ready(function ($) {
				function uploadImage(button, input) {
					var frame;
					$(button).click(function (e) {
						e.preventDefault();
						if (frame) {
							frame.open();
							return;
						}
						frame = wp.media({
							title: 'Select or Upload Image',
							button: {
								text: 'Use this image'
							},
							multiple: false
						});
						frame.on('select', function () {
							var attachment = frame.state().get('selection').first().toJSON();
							$(input).val(attachment.url);
						});
						frame.open();
					});
				}

				uploadImage('#ast_og_image_button', '#ast_og_image');
				uploadImage('#ast_twitter_image_button', '#ast_twitter_image');
			});
		</script>
		<?php
	}

	public function save_social_preview_data( $post_id ) {
		if ( ! isset( $_POST['ast_social_preview_nonce'] ) || ! wp_verify_nonce( $_POST['ast_social_preview_nonce'], 'ast_social_preview_nonce' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$fields = array(
			'ast_og_title',
			'ast_og_description',
			'ast_og_image',
			'ast_twitter_title',
			'ast_twitter_description',
			'ast_twitter_image'
		);

		foreach ( $fields as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				$value = sanitize_text_field( $_POST[ $field ] );
				update_post_meta( $post_id, '_' . $field, $value );
			}
		}
	}

	public function output_social_meta_tags() {
		if ( is_singular() ) {
			global $post;

			$og_title = get_post_meta( $post->ID, '_ast_og_title', true ) ?: get_the_title( $post->ID );
			$og_description = get_post_meta( $post->ID, '_ast_og_description', true ) ?: get_the_excerpt( $post->ID );
			$og_image = get_post_meta( $post->ID, '_ast_og_image', true ) ?: get_the_post_thumbnail_url( $post->ID, 'large' );

			$twitter_title = get_post_meta( $post->ID, '_ast_twitter_title', true ) ?: $og_title;
			$twitter_description = get_post_meta( $post->ID, '_ast_twitter_description', true ) ?: $og_description;
			$twitter_image = get_post_meta( $post->ID, '_ast_twitter_image', true ) ?: $og_image;

			// Open Graph meta tags
			echo '<meta property="og:title" content="' . esc_attr( $og_title ) . '" />' . "\n";
			echo '<meta property="og:description" content="' . esc_attr( $og_description ) . '" />' . "\n";
			echo '<meta property="og:type" content="article" />' . "\n";
			echo '<meta property="og:url" content="' . esc_url( get_permalink( $post->ID ) ) . '" />' . "\n";
			if ( $og_image ) {
				echo '<meta property="og:image" content="' . esc_url( $og_image ) . '" />' . "\n";
			}

			// Twitter Card meta tags
			echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
			echo '<meta name="twitter:title" content="' . esc_attr( $twitter_title ) . '" />' . "\n";
			echo '<meta name="twitter:description" content="' . esc_attr( $twitter_description ) . '" />' . "\n";
			if ( $twitter_image ) {
				echo '<meta name="twitter:image" content="' . esc_url( $twitter_image ) . '" />' . "\n";
			}
		}
	}
}