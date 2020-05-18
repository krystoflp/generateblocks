<?php
/**
 * Our settings page.
 *
 * @package GenerateBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Build our settings page.
 */
class GenerateBlocks_Settings {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'save' ) );

		if ( ! empty( $_POST ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			add_action( 'wp_ajax_generateblocks_regenerate_css_files', array( $this, 'regenerate_css_files' ) );
		}
	}

	/**
	 * Add our Dashboard menu item.
	 */
	public function add_menu() {
		$settings = add_options_page(
			__( 'Settings', 'generateblocks' ),
			__( 'GenerateBlocks', 'generateblocks' ),
			'manage_options',
			'generateblocks-settings',
			array( $this, 'settings_page' )
		);

		add_action( "admin_print_scripts-$settings", array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue our scripts.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			'generateblocks-settings',
			GENERATEBLOCKS_DIR_URL . 'assets/js/scripts.js',
			array( 'jquery' ),
			filemtime( GENERATEBLOCKS_DIR . 'assets/js/scripts.js' ),
			true
		);
	}

	/**
	 * Save our settings.
	 */
	public function save() {
		if ( isset( $_POST['generateblocks_settings'] ) ) {
			if ( ! check_admin_referer( 'generateblocks_settings', 'generateblocks_settings' ) ) {
				wp_die( esc_html( __( 'Security check failed.', 'generateblocks' ) ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html( __( 'Security check failed.', 'generateblocks' ) ) );
			}

			$settings = get_option( 'generateblocks', array() );
			$values = $_POST['generateblocks'];

			if ( isset( $values['css_print_method'] ) ) {
				$settings['css_print_method'] = sanitize_key( $values['css_print_method'] );
			}

			update_option( 'generateblocks', $settings );

			wp_safe_redirect( admin_url( 'admin.php?page=generateblocks-settings' ) );
			exit;
		}
	}

	/**
	 * Regenerate our CSS files.
	 */
	public function regenerate_css_files() {
		check_ajax_referer( 'generateblocks_regenerate_css_files', '_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Security check failed.', 'generateblocks' ) );
		}

		update_option( 'generateblocks_dynamic_css_posts', array() );

		wp_send_json_success();
	}

	/**
	 * Output our Dashboard HTML.
	 *
	 * @since 0.1
	 */
	public function settings_page() {
		?>
			<div class="wrap gblocks-dashboard-wrap">
				<div class="gblocks-dashboard-header">
					<h1><?php esc_html_e( 'Settings', 'generateblocks' ); ?></h1>

					<?php generateblocks_dashboard_navigation(); ?>
				</div>

				<div class="gblocks-settings-content">
					<form action="options.php" method="post">
						<?php
						wp_nonce_field( 'generateblocks_settings', 'generateblocks_settings' );
						?>
						<table class="form-table" role="presentation">
							<tbody>
								<tr>
									<th scope="row">
										<?php esc_html_e( 'CSS Print Method', 'generateblocks' ); ?>
									</th>
									<td>
										<select name="generateblocks[css_print_method]">
											<option value="file"<?php selected( 'file', generateblocks_get_option( 'css_print_method' ) ); ?>><?php esc_html_e( 'External File', 'generateblocks' ); ?></option>
											<option value="inline"<?php selected( 'inline', generateblocks_get_option( 'css_print_method' ) ); ?>><?php esc_html_e( 'Inline Embedding', 'generateblocks' ); ?></option>
										</select>
										<p><?php esc_html_e( 'Generating your CSS in external files is better for overall performance.', 'generateblocks' ); ?></p>
									</td>
								</tr>
								<tr>
									<th scope="row">
										<?php esc_html_e( 'Regenerate CSS', 'generateblocks' ); ?>
									</th>
									<td>
										<?php
											printf(
												'<button data-nonce="%s" class="button generateblocks-button-spinner" id="generateblocks-regenerate-css-files-button">%s</button>',
												wp_create_nonce( 'generateblocks_regenerate_css_files' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
												esc_html__( 'Regenerate Files', 'generateblocks' )
											);
										?>
										<p><?php esc_html_e( 'Force your external CSS files to regenerate next time their page is loaded.', 'generateblocks' ); ?></p>
									</td>
								</tr>
								<?php
								/**
								 * Do generateblocks_settings_fields hook.
								 *
								 * @since 1.0
								 */
								do_action( 'generateblocks_settings_fields' );
								?>
							</tbody>
						</table>
						<?php
						submit_button();
						?>
					</form>
				</div>
			</div>
		<?php
	}
}

new GenerateBlocks_Settings();
