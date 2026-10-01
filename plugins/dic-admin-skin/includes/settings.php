<?php
/**
 * Settings page (Settings -> Admin Skin).
 *
 * @package DicAdminSkin
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_init', 'dic_admin_skin_register_settings' );
add_action( 'admin_menu', 'dic_admin_skin_add_settings_page' );
add_action( 'admin_enqueue_scripts', 'dic_admin_skin_settings_assets' );

function dic_admin_skin_register_settings(): void {
	register_setting(
		'dic_admin_skin_group',
		DIC_ADMIN_SKIN_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'dic_admin_skin_sanitize_options',
			'default'           => dic_admin_skin_defaults(),
		)
	);
}

function dic_admin_skin_add_settings_page(): void {
	add_options_page(
		__( 'Admin Skin', 'dic-admin-skin' ),
		__( 'Admin Skin', 'dic-admin-skin' ),
		'manage_options',
		'dic-admin-skin',
		'dic_admin_skin_render_settings_page'
	);
}

/**
 * Sanitize every field on input.
 *
 * @param mixed $input Raw submitted value.
 * @return array<string, mixed>
 */
function dic_admin_skin_sanitize_options( $input ): array {
	$defaults = dic_admin_skin_defaults();
	$input    = is_array( $input ) ? $input : array();
	$out      = array();

	$out['brand_color'] = sanitize_hex_color( $input['brand_color'] ?? '' ) ?: $defaults['brand_color'];
	$out['login_bg']    = sanitize_hex_color( $input['login_bg'] ?? '' ) ?: $defaults['login_bg'];

	$logo_id         = absint( $input['logo_id'] ?? 0 );
	$out['logo_id']  = ( $logo_id && wp_attachment_is_image( $logo_id ) ) ? $logo_id : 0;

	$out['footer_text'] = wp_kses_post( $input['footer_text'] ?? '' );

	$sidebar        = sanitize_key( $input['sidebar'] ?? 'dark' );
	$out['sidebar'] = in_array( $sidebar, array( 'dark', 'light' ), true ) ? $sidebar : 'dark';

	$out['clean_dashboard'] = empty( $input['clean_dashboard'] ) ? 0 : 1;
	$out['whitelabel']      = empty( $input['whitelabel'] ) ? 0 : 1;

	$domain = strtolower( trim( sanitize_text_field( $input['agency_domain'] ?? '' ) ) );
	$domain = ltrim( $domain, '@' );
	$out['agency_domain'] = preg_match( '/^[a-z0-9-]+(\.[a-z0-9-]+)*\.[a-z]{2,}$/', $domain ) ? $domain : '';

	return $out;
}

function dic_admin_skin_settings_assets( string $hook ): void {
	if ( 'settings_page_dic-admin-skin' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script(
		'dic-admin-skin-settings',
		DIC_ADMIN_SKIN_URL . 'assets/settings.js',
		array( 'jquery', 'wp-color-picker' ),
		dic_admin_skin_asset_ver( 'assets/settings.js' ),
		true
	);
	wp_localize_script(
		'dic-admin-skin-settings',
		'dicAdminSkinSettings',
		array(
			'frameTitle'  => __( 'Choose logo', 'dic-admin-skin' ),
			'frameButton' => __( 'Use this logo', 'dic-admin-skin' ),
		)
	);
}

function dic_admin_skin_render_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$o        = dic_admin_skin_options();
	$name     = DIC_ADMIN_SKIN_OPTION;
	$logo_url = $o['logo_id'] ? wp_get_attachment_image_url( (int) $o['logo_id'], 'medium' ) : '';
	?>
	<div class="wrap dic-settings">
		<h1><?php esc_html_e( 'Admin Skin', 'dic-admin-skin' ); ?></h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'dic_admin_skin_group' ); ?>

			<h2><?php esc_html_e( 'Branding', 'dic-admin-skin' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="dic_brand_color"><?php esc_html_e( 'Brand color', 'dic-admin-skin' ); ?></label></th>
					<td><input type="text" id="dic_brand_color" class="dic-color-field" name="<?php echo esc_attr( $name ); ?>[brand_color]" value="<?php echo esc_attr( $o['brand_color'] ); ?>" data-default-color="#2563eb"></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Logo', 'dic-admin-skin' ); ?></th>
					<td>
						<input type="hidden" id="dic_logo_id" name="<?php echo esc_attr( $name ); ?>[logo_id]" value="<?php echo esc_attr( (string) $o['logo_id'] ); ?>">
						<p><img id="dic_logo_preview" src="<?php echo esc_url( (string) $logo_url ); ?>" alt="" style="max-width:240px;height:auto;<?php echo $logo_url ? '' : 'display:none;'; ?>"></p>
						<button type="button" class="button" id="dic_logo_pick"><?php esc_html_e( 'Select logo', 'dic-admin-skin' ); ?></button>
						<button type="button" class="button-link" id="dic_logo_remove"><?php esc_html_e( 'Remove', 'dic-admin-skin' ); ?></button>
						<p class="description"><?php esc_html_e( 'Shown on the login screen. Recommended: PNG or SVG-as-image, at least 320px wide.', 'dic-admin-skin' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="dic_login_bg"><?php esc_html_e( 'Login background', 'dic-admin-skin' ); ?></label></th>
					<td><input type="text" id="dic_login_bg" class="dic-color-field" name="<?php echo esc_attr( $name ); ?>[login_bg]" value="<?php echo esc_attr( $o['login_bg'] ); ?>" data-default-color="#f3f4f6"></td>
				</tr>
				<tr>
					<th scope="row"><label for="dic_footer_text"><?php esc_html_e( 'Admin footer text', 'dic-admin-skin' ); ?></label></th>
					<td>
						<input type="text" id="dic_footer_text" class="large-text" name="<?php echo esc_attr( $name ); ?>[footer_text]" value="<?php echo esc_attr( $o['footer_text'] ); ?>">
						<p class="description"><?php esc_html_e( 'Basic HTML allowed. Leave empty for the WordPress default.', 'dic-admin-skin' ); ?></p>
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Layout', 'dic-admin-skin' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Sidebar style', 'dic-admin-skin' ); ?></th>
					<td>
						<label><input type="radio" name="<?php echo esc_attr( $name ); ?>[sidebar]" value="dark" <?php checked( $o['sidebar'], 'dark' ); ?>> <?php esc_html_e( 'Dark', 'dic-admin-skin' ); ?></label><br>
						<label><input type="radio" name="<?php echo esc_attr( $name ); ?>[sidebar]" value="light" <?php checked( $o['sidebar'], 'light' ); ?>> <?php esc_html_e( 'Light', 'dic-admin-skin' ); ?></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Dashboard', 'dic-admin-skin' ); ?></th>
					<td>
						<label><input type="checkbox" name="<?php echo esc_attr( $name ); ?>[clean_dashboard]" value="1" <?php checked( $o['clean_dashboard'], 1 ); ?>> <?php esc_html_e( 'Replace default widgets with one clean Status widget', 'dic-admin-skin' ); ?></label>
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'White-label for clients', 'dic-admin-skin' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Enable', 'dic-admin-skin' ); ?></th>
					<td>
						<label><input type="checkbox" name="<?php echo esc_attr( $name ); ?>[whitelabel]" value="1" <?php checked( $o['whitelabel'], 1 ); ?>> <?php esc_html_e( 'Hide WordPress branding, update notices and footer credit from client users', 'dic-admin-skin' ); ?></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="dic_agency_domain"><?php esc_html_e( 'Agency email domain', 'dic-admin-skin' ); ?></label></th>
					<td>
						<input type="text" id="dic_agency_domain" class="regular-text" name="<?php echo esc_attr( $name ); ?>[agency_domain]" value="<?php echo esc_attr( $o['agency_domain'] ); ?>" placeholder="your-agency.com">
						<p class="description"><?php esc_html_e( 'Users with this email domain keep the full admin. Leave empty and white-label stays off for everyone. Capabilities are never removed, only visual clutter is hidden.', 'dic-admin-skin' ); ?></p>
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
