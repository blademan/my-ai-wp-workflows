<?php
/**
 * Login screen, dashboard cleanup, footer and client white-labelling.
 *
 * @package DicAdminSkin
 */

defined( 'ABSPATH' ) || exit;

add_action( 'login_enqueue_scripts', 'dic_admin_skin_login_assets' );
add_filter( 'login_headerurl', 'dic_admin_skin_login_url' );
add_filter( 'login_headertext', 'dic_admin_skin_login_title' );
add_action( 'wp_dashboard_setup', 'dic_admin_skin_dashboard', 999 );
add_action( 'admin_bar_menu', 'dic_admin_skin_admin_bar', 999 );
add_filter( 'admin_footer_text', 'dic_admin_skin_footer_text', 99 );
add_filter( 'update_footer', 'dic_admin_skin_update_footer', 99 );
add_action( 'admin_init', 'dic_admin_skin_hide_nags' );

function dic_admin_skin_login_assets(): void {
	$opts = dic_admin_skin_options();

	wp_enqueue_style( 'dic-admin-skin-login', DIC_ADMIN_SKIN_URL . 'assets/login.css', array(), dic_admin_skin_asset_ver( 'assets/login.css' ) );

	$css = ':root{--dic-brand:' . sanitize_hex_color( $opts['brand_color'] ) . ';--dic-login-bg:' . sanitize_hex_color( $opts['login_bg'] ) . ';}';

	$logo = $opts['logo_id'] ? wp_get_attachment_image_url( (int) $opts['logo_id'], 'full' ) : '';
	if ( $logo ) {
		$css .= 'body.login h1 a{background-image:url("' . esc_url_raw( $logo ) . '");}';
	}
	wp_add_inline_style( 'dic-admin-skin-login', $css );
}

function dic_admin_skin_login_url(): string {
	return home_url( '/' );
}

function dic_admin_skin_login_title(): string {
	return get_bloginfo( 'name' );
}

/** Replace default dashboard widgets with a single Status widget. */
function dic_admin_skin_dashboard(): void {
	$opts = dic_admin_skin_options();
	if ( empty( $opts['clean_dashboard'] ) ) {
		return;
	}

	remove_action( 'welcome_panel', 'wp_welcome_panel' );
	$boxes = array(
		array( 'dashboard_right_now', 'normal' ),
		array( 'dashboard_activity', 'normal' ),
		array( 'dashboard_site_health', 'normal' ),
		array( 'dashboard_quick_press', 'side' ),
		array( 'dashboard_primary', 'side' ),
		array( 'dashboard_secondary', 'side' ),
		array( 'dashboard_recent_comments', 'normal' ),
		array( 'dashboard_incoming_links', 'normal' ),
		array( 'dashboard_plugins', 'normal' ),
	);
	foreach ( $boxes as list( $id, $context ) ) {
		remove_meta_box( $id, 'dashboard', $context );
	}

	wp_add_dashboard_widget( 'dic_admin_skin_status', __( 'Site status', 'dic-admin-skin' ), 'dic_admin_skin_status_widget' );
}

function dic_admin_skin_status_widget(): void {
	$user = wp_get_current_user();
	$last = get_posts(
		array(
			'numberposts'   => 1,
			'post_status'   => 'publish',
			'post_type'     => 'any',
			'no_found_rows' => true,
		)
	);
	$rows = array(
		__( 'Site', 'dic-admin-skin' )           => get_bloginfo( 'name' ),
		__( 'WordPress', 'dic-admin-skin' )      => get_bloginfo( 'version' ),
		__( 'PHP', 'dic-admin-skin' )            => PHP_VERSION,
		__( 'Active theme', 'dic-admin-skin' )   => wp_get_theme()->get( 'Name' ),
		__( 'Last published', 'dic-admin-skin' ) => $last ? get_the_title( $last[0] ) . ' (' . get_the_date( '', $last[0] ) . ')' : __( 'Nothing yet', 'dic-admin-skin' ),
	);
	?>
	<p class="dic-greeting">
		<?php
		/* translators: %s: user display name */
		echo esc_html( sprintf( __( 'Welcome back, %s.', 'dic-admin-skin' ), $user->display_name ) );
		?>
	</p>
	<table class="dic-status-table">
		<?php foreach ( $rows as $label => $value ) : ?>
			<tr>
				<th scope="row"><?php echo esc_html( (string) $label ); ?></th>
				<td><?php echo esc_html( (string) $value ); ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php
}

/**
 * @param WP_Admin_Bar $bar Admin bar instance.
 */
function dic_admin_skin_admin_bar( WP_Admin_Bar $bar ): void {
	$opts = dic_admin_skin_options();
	if ( ! empty( $opts['whitelabel'] ) && ! dic_admin_skin_is_agency_user() ) {
		$bar->remove_node( 'wp-logo' );
	}
}

function dic_admin_skin_is_client_whitelabel(): bool {
	$opts = dic_admin_skin_options();
	return ! empty( $opts['whitelabel'] ) && ! dic_admin_skin_is_agency_user();
}

/**
 * @param string $text Default footer text.
 */
function dic_admin_skin_footer_text( string $text ): string {
	$opts = dic_admin_skin_options();
	if ( '' !== $opts['footer_text'] ) {
		return wp_kses_post( $opts['footer_text'] );
	}
	return dic_admin_skin_is_client_whitelabel() ? '' : $text;
}

/**
 * @param string $text Default version string.
 */
function dic_admin_skin_update_footer( string $text ): string {
	return dic_admin_skin_is_client_whitelabel() ? '' : $text;
}

function dic_admin_skin_hide_nags(): void {
	if ( dic_admin_skin_is_client_whitelabel() ) {
		remove_action( 'admin_notices', 'update_nag', 3 );
		remove_action( 'network_admin_notices', 'update_nag', 3 );
		remove_action( 'admin_notices', 'maintenance_nag', 10 );
	}
}
