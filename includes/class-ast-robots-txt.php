<?php
class AST_Robots_Txt {
	private $options;

	public function __construct() {
		add_filter( 'robots_txt', array( $this, 'modify_robots_txt' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'add_robots_txt_menu' ) );
		add_action( 'admin_init', array( $this, 'register_robots_txt_settings' ) );

		$this->options = get_option( 'ast_robots_txt_options' );
	}

	public function add_robots_txt_menu() {
		add_submenu_page(
			'advanced-seo-toolkit',
			__( 'Robots.txt Editor', 'advanced-seo-toolkit' ),
			__( 'Robots.txt', 'advanced-seo-toolkit' ),
			'manage_options',
			'ast-robots-txt',
			array( $this, 'render_robots_txt_page' )
		);
	}

	public function register_robots_txt_settings() {
		register_setting( 'ast_robots_txt_options', 'ast_robots_txt_options', array( $this, 'sanitize_robots_txt_options' ) );

		add_settings_section(
			'ast_robots_txt_main',
			__( 'Robots.txt Settings', 'advanced-seo-toolkit' ),
			array( $this, 'robots_txt_section_callback' ),
			'ast-robots-txt'
		);

		add_settings_field(
			'ast_robots_txt_content',
			__( 'Robots.txt Content', 'advanced-seo-toolkit' ),
			array( $this, 'robots_txt_content_callback' ),
			'ast-robots-txt',
			'ast_robots_txt_main'
		);
	}

	public function sanitize_robots_txt_options( $input ) {
		$sanitized_input = array();
		if ( isset( $input['content'] ) ) {
			$sanitized_input['content'] = wp_kses_post( $input['content'] );
		}
		return $sanitized_input;
	}

	public function robots_txt_section_callback() {
		echo '<p>' . __( 'Customize your robots.txt file content here. Be careful as incorrect settings can affect your site\'s search engine visibility.', 'advanced-seo-toolkit' ) . '</p>';
	}

	public function robots_txt_content_callback() {
		$content = isset( $this->options['content'] ) ? $this->options['content'] : $this->get_default_robots_txt();
		echo '<textarea name="ast_robots_txt_options[content]" rows="10" cols="50" class="large-text code">' . esc_textarea( $content ) . '</textarea>';
	}

	public function render_robots_txt_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'ast_robots_txt_options' );
				do_settings_sections( 'ast-robots-txt' );
				submit_button( __( 'Save Changes', 'advanced-seo-toolkit' ) );
				?>
			</form>
		</div>
		<?php
	}

	public function modify_robots_txt( $output, $public ) {
		if ( $public ) {
			$custom_content = isset( $this->options['content'] ) ? $this->options['content'] : '';
			if ( ! empty( $custom_content ) ) {
				return $custom_content;
			}
		}
		return $output;
	}

	private function get_default_robots_txt() {
		$default = "User-agent: *\n";
		$default .= "Disallow: /wp-admin/\n";
		$default .= "Allow: /wp-admin/admin-ajax.php\n\n";
		$default .= "Sitemap: " . home_url( '/sitemap.xml' );

		return $default;
	}
}