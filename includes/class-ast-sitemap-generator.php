<?php
class AST_Sitemap_Generator {
	private $filename = 'sitemap.xml';
	private $max_urls = 50000; // Maximum URLs per sitemap
	private $max_filesize = 10485760; // Maximum filesize (10MB)

	public function __construct() {
		add_action( 'init', array( $this, 'generate_sitemap' ) );
		add_action( 'save_post', array( $this, 'update_sitemap' ) );
		add_action( 'admin_menu', array( $this, 'add_sitemap_menu' ) );
		add_action( 'admin_init', array( $this, 'register_sitemap_settings' ) );
		add_action( 'update_option_ast_sitemap_post_types', array( $this, 'generate_sitemap' ) );
		add_action( 'update_option_ast_sitemap_taxonomies', array( $this, 'generate_sitemap' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	public function enqueue_admin_scripts( $hook ) {
		if ( 'advanced-seo-toolkit_page_ast-sitemap-settings' !== $hook ) {
			return;
		}

		wp_enqueue_style( 'ast-sitemap-admin-style', plugin_dir_url( __FILE__ ) . 'src/css/ast-sitemap-admin.css', array(), '1.0.0' );
		wp_enqueue_script( 'ast-sitemap-admin-script', plugin_dir_url( __FILE__ ) . 'src/js/ast-sitemap-admin.js', array( 'jquery' ), '1.0.0', true );
	}

	public function add_sitemap_menu() {
		add_submenu_page(
			'advanced-seo-toolkit',
			esc_html__( 'Sitemap Settings', 'advanced-seo-toolkit' ),
			esc_html__( 'Sitemap', 'advanced-seo-toolkit' ),
			'manage_options',
			'ast-sitemap-settings',
			array( $this, 'render_sitemap_settings_page' )
		);
	}

	public function register_sitemap_settings() {
		register_setting( 'ast_sitemap_settings', 'ast_sitemap_post_types', array( $this, 'sanitize_post_types' ) );
		register_setting( 'ast_sitemap_settings', 'ast_sitemap_taxonomies', array( $this, 'sanitize_taxonomies' ) );
	}

	public function sanitize_post_types( $input ) {
		return is_array( $input ) ? array_map( 'sanitize_text_field', $input ) : array();
	}

	public function sanitize_taxonomies( $input ) {
		return is_array( $input ) ? array_map( 'sanitize_text_field', $input ) : array();
	}

	public function render_sitemap_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$sitemap_url = esc_url( home_url( '/' . $this->filename ) );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<div class="ast-sitemap-container">
				<div class="ast-sitemap-settings">
					<h2><?php esc_html_e( 'Sitemap Settings', 'advanced-seo-toolkit' ); ?></h2>
					<form action="options.php" method="post">
						<?php
						settings_fields( 'ast_sitemap_settings' );
						do_settings_sections( 'ast_sitemap_settings' );
						?>
						<table class="form-table">
							<tr valign="top">
								<th scope="row"><?php esc_html_e( 'Include Post Types', 'advanced-seo-toolkit' ); ?></th>
								<td>
									<?php
									$post_types = get_post_types( array( 'public' => true ), 'objects' );
									$selected_post_types = get_option( 'ast_sitemap_post_types', array( 'post', 'page' ) );
									foreach ( $post_types as $post_type ) {
										printf(
											'<label><input type="checkbox" name="ast_sitemap_post_types[]" value="%s" %s> %s</label><br>',
											esc_attr( $post_type->name ),
											checked( in_array( $post_type->name, $selected_post_types ), true, false ),
											esc_html( $post_type->label )
										);
									}
									?>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php esc_html_e( 'Include Taxonomies', 'advanced-seo-toolkit' ); ?></th>
								<td>
									<?php
									$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
									$selected_taxonomies = get_option( 'ast_sitemap_taxonomies', array( 'category', 'post_tag' ) );
									foreach ( $taxonomies as $taxonomy ) {
										printf(
											'<label><input type="checkbox" name="ast_sitemap_taxonomies[]" value="%s" %s> %s</label><br>',
											esc_attr( $taxonomy->name ),
											checked( in_array( $taxonomy->name, $selected_taxonomies ), true, false ),
											esc_html( $taxonomy->label )
										);
									}
									?>
								</td>
							</tr>
						</table>
						<?php submit_button( esc_html__( 'Save Settings and Regenerate Sitemap', 'advanced-seo-toolkit' ) ); ?>
					</form>
				</div>

				<div class="ast-sitemap-preview">
					<h2><?php esc_html_e( 'Sitemap Preview', 'advanced-seo-toolkit' ); ?></h2>
					<p>
						<strong>
							<a href="<?php echo esc_url( $sitemap_url ); ?>"
								title="<?php esc_attr_e( 'View Sitemap', 'advanced-seo-toolkit' ); ?>" target="_blank">
								<?php echo esc_url( $sitemap_url ); ?>
							</a>
						</strong>
					</p>
					<div class="sitemap-preview">
						<?php
						$sitemap_content = $this->get_sitemap_content();
						$sitemap_preview = $this->format_xml( $sitemap_content );
						echo '<pre><code>' . esc_html( $sitemap_preview ) . '</code></pre>';
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public function generate_sitemap() {
		$sitemap_content = $this->get_sitemap_content();
		$this->write_sitemap( $sitemap_content );
	}

	private function get_sitemap_content() {
		$sitemap = new SimpleXMLElement( '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>' );

		$post_types = get_option( 'ast_sitemap_post_types', array( 'post', 'page' ) );
		$taxonomies = get_option( 'ast_sitemap_taxonomies', array( 'category', 'post_tag' ) );

		// Add homepage
		$this->add_url( $sitemap, home_url( '/' ), '1.0', 'daily' );

		// Add post type archives
		foreach ( $post_types as $post_type ) {
			if ( $post_type !== 'page' && $archive_url = get_post_type_archive_link( $post_type ) ) {
				$this->add_url( $sitemap, $archive_url, '0.8', 'daily' );
			}
		}

		// Add individual posts and pages
		foreach ( $post_types as $post_type ) {
			$posts = get_posts( array(
				'post_type' => $post_type,
				'post_status' => 'publish',
				'posts_per_page' => -1,
			) );

			foreach ( $posts as $post ) {
				$this->add_url( $sitemap, get_permalink( $post ), '0.6', 'weekly', get_the_modified_date( 'c', $post ) );
			}
		}

		// Add taxonomy terms
		foreach ( $taxonomies as $taxonomy ) {
			$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => true ) );
			foreach ( $terms as $term ) {
				$this->add_url( $sitemap, get_term_link( $term ), '0.4', 'weekly' );
			}
		}

		return $sitemap->asXML();
	}

	private function add_url( $sitemap, $loc, $priority, $changefreq, $lastmod = '' ) {
		$url = $sitemap->addChild( 'url' );
		$url->addChild( 'loc', esc_url( $loc ) );
		$url->addChild( 'priority', esc_attr( $priority ) );
		$url->addChild( 'changefreq', esc_attr( $changefreq ) );
		if ( $lastmod ) {
			$url->addChild( 'lastmod', esc_attr( $lastmod ) );
		}
	}

	private function write_sitemap( $content ) {
		$sitemap_path = ABSPATH . $this->filename;
		file_put_contents( $sitemap_path, $content );

		// Ping search engines
		$this->ping_search_engines();
	}

	private function ping_search_engines() {
		$sitemap_url = home_url( '/' . $this->filename );
		$ping_urls = array(
			"https://www.google.com/webmasters/tools/ping?sitemap=" . urlencode( $sitemap_url ),
			"https://www.bing.com/ping?sitemap=" . urlencode( $sitemap_url )
		);

		foreach ( $ping_urls as $url ) {
			wp_safe_remote_get( esc_url_raw( $url ) );
		}
	}

	public function update_sitemap( $post_id ) {
		// Check if this is a revision or autosave
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Check if the post type should be included in the sitemap
		$post_type = get_post_type( $post_id );
		$included_post_types = get_option( 'ast_sitemap_post_types', array( 'post', 'page' ) );
		if ( ! in_array( $post_type, $included_post_types ) ) {
			return;
		}

		// Regenerate the sitemap
		$this->generate_sitemap();
	}

	private function format_xml( $xml ) {
		$dom = new DOMDocument( '1.0' );
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML( $xml );
		$formatted = $dom->saveXML();

		// Add syntax highlighting (you may want to use a proper XML syntax highlighter library for better results)
		$formatted = preg_replace(
			array(
				'/&lt;urlset.*?&gt;/',
				'/&lt;\/urlset&gt;/',
				'/&lt;url&gt;/',
				'/&lt;\/url&gt;/',
				'/&lt;(\w+)&gt;/',
				'/&lt;\/(\w+)&gt;/',
				'/&gt;(.+?)&lt;/',
			),
			array(
				'<span class="xml-tag">&lt;urlset$1&gt;</span>',
				'<span class="xml-tag">&lt;/urlset&gt;</span>',
				'<span class="xml-tag">&lt;url&gt;</span>',
				'<span class="xml-tag">&lt;/url&gt;</span>',
				'<span class="xml-tag">&lt;$1&gt;</span>',
				'<span class="xml-tag">&lt;/$1&gt;</span>',
				'&gt;<span class="xml-content">$1</span>&lt;',
			),
			htmlspecialchars( $formatted )
		);

		return $formatted;
	}
}